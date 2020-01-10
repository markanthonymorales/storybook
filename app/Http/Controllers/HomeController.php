<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Events\PrintEvent;

use App\Repository\UserRepository;
use App\Repository\RecentViewedRepository;
use App\Repository\PrintRepository;
use App\Repository\BookRepository;

use Stripe\Stripe;
use Stripe\Charge;

use App\Book;
use App\Format;
use App\FormatGroup;
use App\Attribute;
use Auth;
use App\Http\Resources\GlobalResource;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application home.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function home()
    {
        return view('home');
    }

    public static function getTopRecentView()
    {
        return RecentViewedRepository::getTopRecentView();
    }

    public function getCalculate(Request $request)
    {
        $this->validate($request, [
            'id' => ['required', 'integer'],
            'book_format' => ['required', 'integer'],
            'paper' => ['required', 'integer'],
            'cover' => ['required', 'integer'],
            'total_page' => ['required', 'integer'],
            'total_book' => ['required', 'integer'],
        ]);

        $getCalculation = PrintRepository::calculation($request);

        return response()->json($getCalculation, 200);
    }

    public function checkOutPrint(Request $request)
    {
        $status = 500;
        $is_success = false;
        $message = 'Something went wrong on the payment process. Please try again!';
        Stripe::setApiKey(config('services.stripe.secret'));

        $book = Book::find($request->id);
        $description = 'Print a Physical Copy of a Book(s) - Title: "'.$request->title.'" | Total: '.$request->total_book;
        
        $tags = explode(',', $book->tags);
        foreach ($tags as $t => $val)
            $metadata['book_'.$t] = $val;

        $amount = str_replace(".", '', $request->total_price);

        try {
            // send payment to stripe
            $result = Charge::create([
                'amount' => $amount,
                'currency' => 'usd',
                'source' => $request->stripeToken,
                'description' => $description,
                'receipt_email' => $request->email,
                'metadata' => $metadata
            ]);

            if($result->status == 'succeeded'){

                BookRepository::regenerateSpineCover($book, 'spine', 'copy-spine', $request->spine);
                
                event(new PrintEvent($request));

                $status = 200; 
                $is_success = true;
                $message = 'Successfully checkout!';

            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
        }

        return response()->json([
            'success' => $is_success,
            'message' => $message
        ], $status);
                  
    }

    public function getData($id)
    {
        $user = Auth::user();
        $formats = Format::get();
        $attribute = Attribute::get();

        return response()->json([
            'user' => $user,
            'formatType' => $formats,
            'attribute' => $attribute,
            'address' => $user->address,
        ], 200);
    }
}
