<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Mail\InvitationNotification;
use App\Mail\UserNotification;
use App\Jobs\SendEmailJob;

use App\Events\StoryEvent;

use App\Rules\StoryRule;

use App\User;
use App\Story;
use App\StoryPage;
use App\StoryShare;
use App\RecentViewed;

use App\Repository\StoryRepository;
use App\Repository\RecentViewedRepository;
use Auth;
use Storage;
class StoryController extends Controller
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
        $hasDefault = $user->story()->hasDefaultTitle()->get()->count();

    	return view('pages.story', [
            'story' => json_encode([
                'id' => null,
                'user_id' => $user->id,
                'is_file_type' => false,
                'file_url' => '',
                'title' => 'New Story'.($hasDefault > 0?'('.$hasDefault.')':''),
                'search_tags' => [],
                'shared_to' => [],
                'pages' => [],
                'total_page' => 0,
                'from_date' => '',
                'to_date' => '',
            ])
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $this->validate($request, [
            'title' => [
                'required', 
                'string', 
                'max:255', 
                'unique:stories,title,NULL,id,user_id,'.$user->id
            ],
            'from_date' => ['required', 'date'],
            'to_date' => ['required', 'date'],
            // 'pages' => ['required', 'array', 'min:1'],
        ]);

        $story = StoryRepository::setData($request);

        if($request->is_file_type)
            StoryRepository::uploadStoryPDF($request, $story);

        event(new StoryEvent($story, ''));

        return response()->json([
            'success' => true,
            'story' => RecentViewedRepository::collectDataToArray($story)
        ], 200);
    }

    public function edit($id){
        $user = Auth::user();
        $story = Story::find($id);

        if(!$story)
            abort(404);

        if($user->id != $story->user_id && !StoryRepository::hasAccessToEdit($user->id, $story)){
            if(StoryRepository::isAllowed($user->id, $story))
                return redirect('/read')->with(['id'=>$id, 'name' => 'stories']);
            abort(404);
        }

        // Push story ID to session
        RecentViewedRepository::create($user->id, $story->getKey(), 'stories');

        return view('pages.story', [
            'user_id' => $user->id,
            'story' => json_encode(RecentViewedRepository::collectDataToArray($story))
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $this->validate($request, [
            'title' => [
                'required', 
                'string', 
                'max:255', 
                'unique:stories,title,'.$id.',id,user_id,'.$user->id
            ],
            'from_date' => ['required', 'date'],
            'to_date' => ['required', 'date'],
        ]);

        $oldSharedTo = Story::find($id)->shared_to;
        $story = StoryRepository::updateData($request, $id);

        if($request->is_file_type)
            StoryRepository::uploadStoryPDF($request, $story);

        event(new StoryEvent($story, $oldSharedTo));

        return response()->json([
            'success' => true,
            'story' => RecentViewedRepository::collectDataToArray($story)
        ], 200);
    }

     /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request, $id)
    {
        try {
            $this->validate($request, [
                'upload' => 'required|image|mimes:jpeg,png,jpg',
            ]);

            $width = 500;
            $height = 700;
      
            $image = $request->file('upload');
            $input['imagename'] = time().'.'.$image->extension();
         
            $destinationPath = public_path('storage/images/ckeditor/'.$id.'/thumbnail');
            $url = $destinationPath;
            if(!\File::exists($destinationPath)){
                Storage::makeDirectory('public/images/ckeditor/'.$id.'/thumbnail');
            }

            $img = \Image::make($image->path());
            $img->orientate();
            
            $img->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath.'/'.$input['imagename']);
       
            return response()->json([
                'uploaded' => true,
                'url' => Storage::url('public/images/ckeditor/'.$id.'/thumbnail/'.$input['imagename'])
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'uploaded' => false,
                'error' => [
                    "message" => $e->getMessage()
                ]
            ], 200);
        }
    }

    public function destroy($id){
        $story = Story::find($id);

        
        $bool = $story->update([
            'is_deleted' => 1
        ]);

        return response()->json([
            'success' => $bool
        ], 200);
    }

    public function deletePage($id){
        $page = StoryPage::find($id);
        $page->delete();
        return response()->json([
            'success' => true
        ], 200);
    }

    public function getStories(Request $request)
    {
        $user = Auth::user();
        $stories = StoryRepository::getMyStories($user->id, new Story);
        $stories = StoryRepository::filterStories($request, $stories);
        $shares = StoryRepository::getSharedStories($user->id, new Story);
        $shares = StoryRepository::filterStories($request, $shares);

        $perPage = $request->page_size;

        if ($request->current_page == 1) {
            $skip = 0;
        }else
            $skip = $perPage * ($request->current_page - 1);

        $stories = $shares->union($stories);

        $total = $stories->count();

        $stories->skip($skip)
        ->take($perPage);

        $stories = $stories->orderBy('id', 'DESC')->get();
        
        $storyList = [];
        foreach($stories as $k => $story){
            $storyList[] = RecentViewedRepository::collectMultiDataToArray($story, $user->id);
        }

        $min = Story::where('user_id', $user->id)->min('story_date');
        if($min){
            $min = explode('-', $min);
            $min = $min[0];
        }else
            $min = 0;

        $max = Story::where('user_id', $user->id)->max('story_date');
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
            'story' => $storyList
        ], 200);
    }

    public function getStoriesByDate(Request $request)
    {
        $user = Auth::user();
        $stories = StoryRepository::getMyStories($user->id, new Story);
        $stories = StoryRepository::filterDate($request, $stories);
        $sharedStories = StoryRepository::getSharedStories($user->id, new Story);
        $sharedStories = StoryRepository::filterDate($request, $sharedStories);

        $stories = $sharedStories->union($stories)->latest()->get();
        
        $storyList = [];
        foreach($stories as $story){
            $storyList[] = RecentViewedRepository::collectMultiDataToArray($story, $user->id);
        }
        
        return response()->json([
            'success' => true,
            'story' => $storyList
        ], 200);
    }

    public function uploadPDF(Request $request)
    {
        $user_id = Auth::user()->id;
        $filename = sha1(time().$user_id).'.pdf';

        // check tmp folder if have a file exists
        // Get all files in a directory
        $files =   Storage::allFiles('public/story/pdf/tmp');
        if(count($files) > 0){
            // Delete Files
            Storage::delete($files); 
        }

        $file = $request->file('file');
        $path = $file->storeAs('public/story/pdf/tmp', $filename);

        return response()->json([
            'success' => true,
            'url' => Storage::url($path)
        ], 200);
    }
}
