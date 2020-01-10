<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Mail\CheckoutNotification;

use App\Repository\BookRepository;
use App\Repository\OrderRepository;

use App\Book;

use Stripe\Stripe;
use Stripe\Charge;

use Mail;

use App\Http\Resources\GlobalResource;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cart = app('cart');
        if($cart->item_count < 1)
            return redirect('/');

        return view('pages.cart-summary');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $book = Book::find($request->id);

        $cart = app('cart');

        if(!$cart->hasItem(['product_id' => $book->id])){
           $cart->addItem([
                'product_id' => $book->id,
                'unit_price' => $book->ebook_price|0.00,
                'quantity' => 1
            ]);

            $result = OrderRepository::getCart($cart);
            return response()->json([
                'success' => true,
                'cart' => $result,
            ], 200); 
        }

        return response()->json([
            'error' => 'The book ('.$book->title.') is already exists in your cart.'
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $cart = app('cart');

        $result = OrderRepository::getCart($cart);

        return response()->json([
            'success' => true,
            'cart' => $result,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $book = Book::find($id);
        $cart = app('cart');

        $cart->removeItem([ 'id' => $id ]);
        if($cart->item_count < 1)
            return response()->json([
                'success' => true,
                'refresh' => true
            ], 200);

        return response()->json([
            'success' => true,
            'refresh' => false
        ], 200);
    }

    public function checkOutOrder(Request $request){
        try {
            
            $cart = app('cart');

            Stripe::setApiKey(config('services.stripe.secret'));

            $description = $request->fullname;
            $description .= ' order book'.($cart->item_count > 1?'\'s':'');
            $description .= ' [ ';
            $metadata = [];

            $items = OrderRepository::getItems($cart->items);

            $books = [];
            foreach ($items as $key => $item) {
                $data = 'book'.($key+1);

                $books[] = [
                    'id' => $item['product_id'],
                    'book' => BookRepository::getBookById(null, $item['product_id'], false)
                ];
                $metadata[$data] = $item['title'];
                $description .= $item['title'].($key == (count($items)-1)?'':', ');
            }

            $description .= ' ]';
            $amount = OrderRepository::convertToValidPrice($cart->total_price);
            $getAmount = $amount;
            $amount = str_replace(".", '', $amount);

            $cart->checkout();

            $result = Charge::create([
                'amount' => $amount,
                'currency' => 'usd',
                'source' => $request->stripeToken,
                'description' => $description,
                'receipt_email' => $request->email,
                'metadata' => $metadata
            ]);

            if($result->status == 'succeeded'){

                // get all books
                $files = [];
                foreach ($books as $book) {
                    // BookRepository::duplicate($book['id']);

                    // $files[] = BookRepository::generateEPub($book['id'], $book['book']); // remove for the meantime
                    $files[] = BookRepository::generatePDF($book['id'], $book['book']);
                }

                $data = OrderRepository::handleData($request, $getAmount);

                Mail::to($data['to'])->send(new CheckoutNotification($data, $files));
                // sleep(3);

                $cart->complete();

                return response()->json([
                    'success' => true,
                ], 200);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Something went wrong on the payment process. Please try again!'
                ], 200);
            }
              
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 200);
        }
    }

    public function getPublishBooks(Request $request){
        $data = BookRepository::getPublishBooks($request);

        return response()->json([
            'success' => true,
            'total' => $data['total'],
            'current_page' => $data['current_page'],
            'page_size' => $data['page_size'],
            'books' => $data['books']
        ], 200);
    }
}
