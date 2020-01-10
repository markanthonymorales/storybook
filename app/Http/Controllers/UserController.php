<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use App\User;

use App\Book;
use App\Story;
use App\Contact;
use App\Configuration;

use App\Repository\UserRepository;
use App\Repository\RecentViewedRepository;
use App\Repository\StoryRepository;
use App\Repository\BookRepository;

use Auth;
use Storage;

class UserController extends Controller
{
    private $image_ext = ['jpg', 'jpeg', 'png'];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $contacts = UserRepository::getContacts($user);

    	return view('pages.profile', [
    		'contacts' => json_encode($contacts),
    	]);
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'firstname' => ['required', 'string', 'max:255', 'regex:/(^([a-zA-Z]+)(\d+)?$)/u'],
            'lastname' => ['required', 'string', 'max:255', 'regex:/(^([a-zA-Z]+)(\d+)?$)/u'],
            'nickname' => ['required', 'string', 'max:255', 'regex:/(^([a-zA-Z]+)(\d+)?$)/u'],
            'gender' => ['required', 'string', 'max:255'],
            'birthdate' => ['required', 'date'],
        ]);

        $user = User::findOrFail($request->get('id'));
        if($user->update($request->all()))
            return response()->json([
                'success' => true
            ], 200);
        return response()->json([
            'success' => false
        ], 500);
    }

    public function updatePassword(Request $request)
    {
        $this->validate($request, [
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::findOrFail($request->get('id'));
        if($user->update(['password' => Hash::make($request->get('password'))]))
            return response()->json([
                'success' => true
            ], 200);
        return response()->json([
            'success' => false
        ], 500);
    }

    public function seacrhEmail($name)
    {
        $user = Auth::user();
        return UserRepository::searchEmail($user->id, $name, new User);
    }

    public function updateConfig(Request $request)
    {
        $this->validate($request, [
            'markup_price' => ['required', 'integer'],
            'terms_policies' => ['required', 'string'],
            'ebook_markup_price' => ['required', 'string'],
            'footer' => ['required', 'string'],
        ]);

        $checkMarkupPrice = Configuration::where('keycode', '=', 'markup_price')
        ->where('value', '=', $request->markup_price)
        ->first();

        $books = Book::get();
        foreach ($books as $b => $book) {
            $newPrice = $book->original_price + ($book->original_price * $request->markup_price / 100);
            
            $book->update([
                'price' => $newPrice,
                'markup_price' => $request->markup_price,
                'ebook_price' => $newPrice + $request->ebook_markup_price,
                'ebook_markup_price' => $request->ebook_markup_price,
            ]);
        }

        $getConfig = Configuration::get();
        foreach ($getConfig as $key => $config) {
            $val = $request->get($config->keycode);
            $finKey = Configuration::find($config->id);
            $finKey->update([
                'value' => $val
            ]);
        }

        return response()->json([
            'success' => true
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function uploadProfilePic(Request $request, $id)
    {
        try {
            $this->validate($request, [
                'file' => 'required|image|mimes:jpeg,png,jpg',
            ]);
      
            $image = $request->file('file');
            $input['imagename'] = $id.'.png';
         
            $destinationPath = public_path('storage/images/profile/'.$id.'/thumbnail');
            $url = $destinationPath;
            if(!\File::exists($destinationPath)){
                Storage::makeDirectory('public/images/profile/'.$id.'/thumbnail');
            }

            $img = \Image::make($image->path());
            $img->orientate();

            $img->resize(500, 500, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath.'/'.$input['imagename']);
            
            return response()->json([
                'success' => true,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function uploadConfigFile(Request $request)
    {
        try {
            $this->validate($request, [
                'upload' => 'required|image|mimes:jpeg,png,jpg',
            ]);
      
            $image = $request->file('upload');
            $input['imagename'] = time().'.'.$image->extension();
         
            $destinationPath = public_path('storage/terms-policies/thumbnail');
            $url = $destinationPath;
            if(!\File::exists($destinationPath)){
                Storage::makeDirectory('public/terms-policies/thumbnail');
            }

            $img = \Image::make($image->path());
            $img->orientate();

            $img->resize(500, 700, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath.'/'.$input['imagename']);
            
            return response()->json([
                'uploaded' => true,
                'url' => Storage::url('public/terms-policies/thumbnail/'.$input['imagename'])
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'uploaded' => false,
                'error' => [
                    'message' => $e->getMessage()
                ]
            ], 200);
        }
    }
}
