<?php

namespace App\Repository;
use App\Story;
use App\StoryPage;
use App\StoryShare;
use App\User;
use App\Repository\RecentViewedRepository;

use App\Http\Resources\GlobalResource;

use Auth;
use Storage;

class StoryRepository{

    public static function setData($request)
    {
        $story = new Story;
        $story = $story->create($request->only(['title', 'user_id', 'colored_index', 'total_page', 'from_date', 'to_date']));
        $getKey = $story->getKey();

        foreach($request->get('pages') as $key => $page){
            $story->pages()->create([
                'story_id' => $getKey,
                'order_number' => $key,
                'is_colored' => $page['is_colored'],
                'content' => $page['content'],
            ]);
        }

        $emails = [];
        foreach($request->get('shared_to') as $key => $shared){
            $user = User::where('email', '=', $shared['email']);
            if($user->exists()){
                $user = $user->firstOrFail();
                $story->shares()->create([
                    'story_id' => $getKey,
                    'user_id' => $user->id,
                    'is_allow_edit' => $shared['is_allow_edit']?1:0,
                ]);
            }
            
            $emails[] =  $shared['email'];
        }

        $story->update([
            'story_date' => $request->get('from_date'),
            'tags' => implode(',', $request->get('search_tags')),
            'shared_to' => implode(',', $emails)
        ]);

        return $story;
    }

    public static function updateData($request, $id)
    {
        $story = Story::find($id);
        $user = Auth::user();

        foreach($request->get('pages') as $key => $page){
            if($page['id']){
                $getPage = $story->pages()->find($page['id']);
                $getPage->order_number = $key;
                $getPage->is_colored = $page['is_colored'];
                $getPage->content = $page['content'];
                $getPage->save();
            }else{
                $story->pages()->create([
                    'story_id' => $id,
                    'order_number' => $key,
                    'is_colored' => $page['is_colored'],
                    'content' => $page['content'],
                ]);
            }
        }

        $storyChanges = [
            'title' => $request->get('title'),
            'colored_index' => $request->get('colored_index'), 
            'total_page' => $request->get('total_page'),
            'from_date' => $request->get('from_date'),
            'story_date' => $request->get('from_date'),
            'to_date' => $request->get('to_date'),
            'tags' => implode(',', $request->get('search_tags'))
        ];

        if($user->id == $story->user_id){
            $emails = [];
            $story->shares()->delete();
            foreach($request->get('shared_to') as $key => $shared){
                $user = User::where('email', '=', $shared['email']);
                if($user->exists()){
                    $user = $user->firstOrFail();
                    $story->shares()->create([
                        'story_id' => $id,
                        'user_id' => $user->id,
                        'is_allow_edit' => $shared['is_allow_edit']?1:0,
                    ]);
                }
                
                $emails[] =  $shared['email'];
            }

            $storyChanges['shared_to'] = implode(',', $emails);
        }
        

        $story->update($storyChanges);

        return $story;
    }

    public static function getMyStories($user_id, $model){
        return $model->where('user_id', $user_id)
        ->active();
    }

    public static function getSharedStories($user_id, $model){
        return $model->select('stories.*')
        ->leftJoin('story_shares','story_shares.story_id','=','stories.id')
        ->active()
        ->where('story_shares.user_id', '=', $user_id);
    }

    public static function filterStories($request, $stories){
        if($request->has('title') 
            || $request->has('keyword') 
            || $request->has('month') 
            || $request->has('year')){

            if($request->has('title'))
                $stories->where('stories.title', 'like', '%'.$request->title.'%');
            
            if($request->has('keyword'))
                $stories->where('stories.tags', 'like', '%'.$request->keyword.'%');

            if($request->has('month')){
                if(is_array($request->month)){
                    $stories
                    ->whereRaw('MONTH(stories.story_date) >= '.$request->month[0])
                    ->whereRaw('MONTH(stories.story_date) <= '.$request->month[1]);
                }else
                    $stories->whereRaw('MONTH(stories.story_date) = '.$request->month);
            }

            if($request->has('year')){
                if(is_array($request->year)){
                    $stories
                    ->whereRaw('YEAR(stories.story_date) >= '.$request->year[0])
                    ->whereRaw('YEAR(stories.story_date) <= '.$request->year[1]);
                }else
                    $stories->whereRaw('YEAR(stories.story_date) = '.$request->year);
            }
        }
        return $stories;
    }

    public static function filterDate($request, $stories){
        if($request->has('date')){
                $stories->where('stories.story_date', '=', $request->date);
        }
        return $stories;
    }

    public static function getStory($id, $user)
    {
        $story = Story::where('id', $id)->active()->first();

        if(!$story)
            abort(404);

        if($user->id != $story->user_id)
            if(!self::isAllowed($user->id, $story))
                abort(404);

        $storyToArray = RecentViewedRepository::collectDataToArray($story);

        return $storyToArray;
    }

    public static function recentlyViewedStory($user_id)
    {
        $recentViewed = RecentViewedRepository::isExists($user_id, '', 'stories', Story::with('pages'));
        $recentStory = [];
        
        if($recentViewed->exists()){
            $stories = $recentViewed->select(['stories.*', 'recent_viewed.updated_at'])
            ->distinct('stories.id')
            ->latest('recent_viewed.updated_at')
            ->take(3)
            ->get();
                        
            foreach($stories as $story){
                $recentStory[] = RecentViewedRepository::collectMultiDataToArray($story, $user_id);
            }
        }
        
        return $recentStory;
    }

    public static function hasAccessToEdit($id, $story){
        if($story->shares->count() > 0){
            $getUser_id = [];
            foreach($story->shares as $share){
                if($share->is_allow_edit)
                    $getUser_id[] = $share->user_id;
            }

            if(in_array($id, $getUser_id)){
                return true;
            }
        }
        return false;
    }

    public static function isAllowed($id, $story){
        if($story->shares->count() > 0){
            $getUser_id = [];
            foreach($story->shares as $share){
                $getUser_id[] = $share->user_id;
            }

            if(in_array($id, $getUser_id)){
                return true;
            }
        }
        return false;
    }

    public static function uploadStoryPDF($request, $story)
    {
        $oldPath = str_replace('/storage', 'public', $request->file_url);

        $newPath = 'public/story/pdf/'.$story->user_id.'/'.$story->id.'/';
        $newName = $newPath.sha1($story->user_id.$story->id).'.pdf';
        
        if($request->file_url == Storage::url($newName)){
            return $story;
        }

        if(count(Storage::directories($newPath)) < 1){
            Storage::makeDirectory($newPath);
        }

        if (Storage::exists($newName)) {
            Storage::delete($newName);
        }

        Storage::move($oldPath, $newName);
        $story->is_file_type = 1;
        $story->file_url = Storage::url($newName);
        $story->save();
        return $story;
    }
}