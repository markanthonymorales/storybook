<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Repository\RecentViewedRepository;
use App\Repository\StoryRepository;
use App\Repository\BookRepository;

use Auth;

class ReadController extends Controller
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
    
    public function getStories($id)
    {
        $user = Auth::user();
        $story = StoryRepository::getStory($id, $user);
        RecentViewedRepository::create($user->id, $id, 'stories');
        return view('pages.read', [
            'user_id' => $user->id,
            'data' => json_encode($story),
            'type' => 'stories'
        ]);
    }

    public function getBooks($id)
    {
        $user = Auth::user();
        $book = BookRepository::getBook($id, $user);

        RecentViewedRepository::create($user->id, $id, 'books');
        return view('pages.read', [
            'user_id' => $user->id,
            'data' => json_encode($book),
            'type' => 'books'
        ]);
    }

    public function getNextAndPrev($id, $type)
    {
        $user = Auth::user();
        $current = \App\RecentViewed::where('object_id', $id)->where('user_id', $user->id)->where('object_name', $type)->first();

        $next = \App\RecentViewed::where('id', '=' ,$current->id + 1)->where('user_id', $user->id)->first();
        if(!$next)
            $next = \App\RecentViewed::where('id', '!=' ,$current->id)->where('user_id', $user->id)->orderBy('id', 'ASC')->first();

        $prev = \App\RecentViewed::where('id', '=' ,$current->id - 1)->where('user_id', $user->id)->first();
        if(!$prev)
            $prev = \App\RecentViewed::where('id', '!=' ,$current->id)->where('user_id', $user->id)->orderBy('id', 'DESC')->first();

        return response()->json([
            'success' => true,
            'next' => $next?$next->object_id.'/'.$next->object_name:null,
            'prev' => $prev?$prev->object_id.'/'.$prev->object_name:null,
        ], 200);
    }

     public function getTopRecentView()
     {
        return response()->json([
            'success' => true,
            'story' => RecentViewedRepository::getTopRecentView()
        ], 200);
    }

    public function getUserRecentView(){
        $user = Auth::user();
        $recent = RecentViewedRepository::isExists($user->id);

        if($recent->exists()){
            $recent = $recent->latest('recent_viewed.updated_at');
            $data = RecentViewedRepository::recursiveChecker($recent->first(), $recent);

            $id = $data->object_id;
            $name = $data->object_name;
        }

        return response()->json([
            'success' => true,
            'id' => $id,
            'name' => $name,
        ], 200);
    }
}
