<?php

namespace App\Repository;
use App\RecentViewed;
use App\Http\Resources\GlobalResource;

use App\Repository\StoryRepository;
use App\Repository\BookRepository;

use App\StoryShare;
use App\User;
use App\Book;
use App\Story;

use Auth;

class RecentViewedRepository{
	
	public static function isExists($user_id, $obj_id = '', $obj_name = null, $parent = null)
    {
        if($parent){
            $recent = $parent
            ->join('recent_viewed', 'recent_viewed.object_id', '=', $obj_name.'.id')
            ->where([
                $obj_name.'.is_deleted' => 0,
                'recent_viewed.user_id' => $user_id,
                'recent_viewed.object_name' => $obj_name
            ]);
        }else{
            $recent = RecentViewed::where('user_id', '=', $user_id);
            if($obj_name){
                $recent = $recent->where('object_name', '=', $obj_name);
            }
        }

        if($obj_id != '')
	    	$recent->where('recent_viewed.object_id', $obj_id);

        return $recent;
    }

    public static function create($user_id, $obj_id = '', $obj_name = 'stories')
    {
    	$recent = self::isExists($user_id, $obj_id, $obj_name);
    	
        $bool = $recent->exists();
    	$data = self::convertArray($user_id, $obj_id, $obj_name);
    	if($data['object_name'] != 'undefined'){
            if(!$bool){
                $recent = new RecentViewed();  
                $recent->create($data);         
                return $recent;
            }else{
                $recent->update($data);
                return $recent;
            }
        }
    }

    public static function convertArray($user_id, $obj_id = '', $obj_name = 'stories')
    {
    	return [
    		'user_id' => $user_id,
    		'object_id' => $obj_id,
    		'object_name' => $obj_name
    	];
    }

    public static function collectMultiDataToArray($collection, $user_id, $frontCover = '')
    {
        $getEmails = [];
        $has_access = false;
        $shared_to = self::getShares($collection, $getEmails, $has_access);

        $property = config('properties.list');
        unset($property['shared_to|alias']);

        if($collection->object_type == 'books'){
            unset($property['pages|many']);
            unset($property['story_date']);
        }
        
        $toArray = GlobalResource::toArray($collection, $property);

        $toArray['shared_to'] = $getEmails;
        $toArray['has_access'] = $has_access;
        $toArray['is_file_type'] = isset($collection->is_file_type)?$collection->is_file_type:0;
        $toArray['file_url'] = isset($collection->file_url)?$collection->file_url:'';
        $toArray['total_page'] = $collection->total_page?$collection->total_page:($collection->pages?$collection->pages->count():0);
        $toArray['updated_at'] = $collection->updated_at;
        $toArray['object_type'] = $collection->object_type;
        $toArray['is_shared'] = $user_id == $collection->user_id ? false : true;
        $toArray['cover'] = $frontCover;
        $toArray['is_save_as_draft'] = (isset($collection->is_save_as_draft)?($collection->is_save_as_draft?true:false):true);

        if($collection->object_type == 'books'){
            $toArray['pages'] = $collection->chapters[0]->pages;
            $toArray['story_date'] = $collection->book_date;
        }

        return $toArray;
    }

    public static function collectDataToArray($collection)
    {
        $getEmails = [];
        $has_access = false;
        $user = Auth::user();

        $shared_to = self::getShares($collection, $getEmails, $has_access);

        $property = config('properties.story');
        if($collection->object_type == 'books'){
            unset($property[3]);
            unset($property['pages|many']);
        }

        $toArray = GlobalResource::toArray($collection, $property);

        if($collection->object_type == 'books'){
            $toArray['story_date'] = $collection->book_date;
            $toArray['pages'] = [];
        }

        $toArray['search_tags'] = $collection->tags?explode(',', $collection->tags):[];
        $toArray['shared_to'] = $getEmails;
        $toArray['is_shared'] = $user->id == $collection->user_id ? false : true;

        return $toArray;
    }

    public static function getShares($collection, &$getEmails, &$has_access)
    {
        $shared_to = [];
        $getEmails = [];
        $has_access = false;
        $user = Auth::user();

        if(isset($collection->shared_to) && $collection->shared_to != ''){
            $shared_to = explode(',', $collection->shared_to);
        }

        if(isset($collection->shares)){
            foreach ($collection->shares as $s => $share) {
                if(isset($share->user)){
                    if(!in_array($share->user->email, $shared_to)){
                        array_push($shared_to, $share->user->email);
                    }
                }
            }
        }

        if(count($shared_to) > 0){
            foreach ($shared_to as $s => $shared) {

                $id = null;
                $email = $shared;
                $is_allow_edit = false;

                $getUser = User::where('email','=',$email);
                
                if($getUser->exists()){
                    $getUser = $getUser->firstOrFail();
                    $id = $getUser->id;

                    $share = StoryShare::where('story_id','=', $collection->id)->where('user_id', '=', $id);
                    if($share->exists()){
                        $share = $share->firstOrFail();
                        $is_allow_edit = $share->is_allow_edit?true:false;
                    }
                }

                $getEmails[] = [
                    'id' => $id,
                    'email' => $email,
                    'is_allow_edit' => $is_allow_edit
                ];
            }
        }

        return $shared_to;
    }

    public static function getTopRecentView(){
        $user = Auth::user();
        
        $story = StoryRepository::recentlyViewedStory($user->id);
        $book = BookRepository::recentlyViewedBook($user->id);
        
        $result = array_merge($story, $book);
        $updated_at = array_column($result, 'updated_at');

        array_multisort($updated_at, SORT_DESC, $result);
        return array_slice($result, 0, 3);
    }

    public static function recursiveChecker($data, $recent, $index = 1)
    {
        if($data->object_name == 'books'){
            if(!Book::where('id', $data->object_id)->active()->exists())
                return self::recursiveChecker($recent->skip($index)->first(), $recent, $index+=1);
        }
        if($data->object_name == 'stories'){
            if(!Story::where('id', $data->object_id)->active()->exists())
                return self::recursiveChecker($recent->skip($index)->first(), $recent, $index+=1);
        }
        return $data;
    }
}