<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Storage;
use Auth;

use App\User;
use App\Book;
use App\BookChapter;
use App\BookChapterPage;
use App\Events\BookEvent;
use App\Repository\BookRepository;
use App\Repository\RecentViewedRepository;

class BookController extends Controller
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

    public function create()
    {
        $user = Auth::user();
        $hasDefault = $user->books()->hasDefaultTitle()->get()->count();

        return view('pages.book', [
            'markup_price' => config('custom.class')::getValue('markup_price'),
            'ebook_markup_price' => config('custom.class')::getValue('ebook_markup_price'),
            'book' => json_encode([
                'title' => 'New Book'.($hasDefault > 0?'('.$hasDefault.')':''),

                'content' => '',
                'chapters' => [],
                'description' => '',

                'has_front_cover' => false,
                'has_spine_cover' => false,
                'has_back_cover' => false,

                'front_cover' => '',
                'spine_cover' => '',
                'back_cover' => '',

                'author' => $user->firstname.' '.$user->lastname,

                'original_price' => 0.00,
                'price' => 0.00,
                'markup_price' => 0,
                'ebook_markup_price' => 0,
                'ebook_price' => 0,

                'from_date' => null,
                'to_date' => null,
                'book_date' => null,

                'status_type' => 'private',
                'is_save_as_draft' => true,
                'tags' => [],
                'shared_to' => [],
                'total_page' => 0
            ])
        ]);
    }

    public function show($id)
    {
        $user = Auth::user();
        $book = BookRepository::getBookById($user->id, $id, false);

        return response()->json([
            'success' => true,
            'book' => $book
        ], 200);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
    	$this->validate($request, [
            'title' => [
                'required', 
                'string', 
                'max:255', 
                'unique:books,title,NULL,id,user_id,'.$user->id
            ],
            'price' => ['required', 'numeric', 'min:0'],
            'content' => ['required', 'string'],
            'description' => ['required', 'string'],
            'chapters' => ['required', 'array', 'min:1'],
        ]);

        $book = BookRepository::setData($request);

        event(new BookEvent($book));

        $book = BookRepository::convertToArray($book, true);

        if(!$book['is_save_as_draft']){
            $file = BookRepository::generatePDF($book['id'], $book);
        }

        return response()->json([
            'success' => true,
            'book' => $book
        ], 200);
    }

    public function edit(Book $book)
    {
        if(!$book->is_save_as_draft)
            abort(404);

        event(new BookEvent($book));

        $book = BookRepository::convertToArray($book, true);
        
        return view('pages.book', [
            'markup_price' => config('custom.class')::getValue('markup_price'),
            'ebook_markup_price' => config('custom.class')::getValue('ebook_markup_price'),
            'book' => json_encode($book)
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $this->validate($request, [
            'id' => ['required', 'integer', 'min:1'],
            'title' => [
                'required', 
                'string', 
                'max:255', 
                'unique:books,title,'.$id.',id,user_id,'.$user->id
            ],
            'price' => ['required', 'numeric', 'min:0'],
            'content' => ['required', 'string'],
            'description' => ['required', 'string'],
            'chapters' => ['required', 'array', 'min:1'],
        ]);

        $getChaptersId = [];

        $book = BookRepository::updateData($request, $id, $getChaptersId);

        BookRepository::cleanChapter($getChaptersId, $id);

        event(new BookEvent($book));

        $book = BookRepository::convertToArray($book, true);

        if(!$book['is_save_as_draft']){
            $file = BookRepository::generatePDF($id, $book);
        }

        return response()->json([
            'success' => true,
            'book' => $book
        ], 200);
    }

    public function destroy(Book $book){
        // $book = Book::find($id);
        return response()->json([
            'success' => $book->update([ 'is_deleted' => true ])
        ], 200);
    }

    public function getBooks(Request $request)
    {
        $user = Auth::user();
        
        $myBooks = BookRepository::getMyBooks($user->id, new Book);
        $myBooks = BookRepository::filterBooks($request, $myBooks);
        
        $cartBooks = BookRepository::getBookFromCart($user);
        if($cartBooks){
            $cartBooks = BookRepository::filterBooks($request, $cartBooks);
            $myBooks = $myBooks->union($cartBooks);
        }

        $books = $myBooks;

        $perPage = $request->page_size;

        if ($request->current_page == 1) {
            $skip = 0;
        }else
            $skip = $perPage * ($request->current_page - 1);

        $total = $books->count();

        $books->skip($skip)
        ->take($perPage);

        $books = $books->orderBy('id', 'DESC')->get();
        
        $bookList = [];
        foreach($books as $k => $book){
            $bookList[] = BookRepository::convertToArray($book);
        }

        $min = Book::where('user_id', $user->id)->min('book_date');
        if($min){
            $min = explode('-', $min);
            $min = $min[0];
        }else
            $min = 0;
        
        $max = Book::where('user_id', $user->id)->max('book_date');
        if($max){
            $max = explode('-', $max);
            $max = $max[0];
        }else
            $max = 0;

        return response()->json([
            'success' => true,
            'min' => $min,
            'max' => $max,
            'total' => $total,
            'book' => $bookList
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function uploadFile(Request $request, $id, $name)
    {
        try {
            $widthByType = 565;
            $heightByType = 842;
            
            $this->validate($request, [
                'file' => 'required|image|mimes:jpeg,png,jpg',
            ]);

            $user = Auth::user();
      
            $image = $request->file('file');
            $input['imagename'] = ($name == 'spineCover' ? 'original-copy-spineCover' : $name) .'.png';
         
            $destinationPath = public_path('storage/images/book/'.$user->id.'/'.$id.'/thumbnail');
            $url = $destinationPath;
            if(!\File::exists($destinationPath)){
                Storage::makeDirectory('public/images/book/'.$user->id.'/'.$id.'/thumbnail');
            }

            $img = \Image::make($image->path());
            $img->orientate();
            
            
            $img->resize($widthByType, $heightByType, function ($constraint) {
                $constraint->aspectRatio();
            });

            $img->resizeCanvas($widthByType, $heightByType, 'center', false, '#ffffff');
            $img->save($destinationPath.'/'.$input['imagename']);
            
            return response()->json([
                'success' => true,
                'url' => Storage::url('public/images/book/'.$user->id.'/'.$id.'/thumbnail/'.$input['imagename'])
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
