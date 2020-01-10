<?php

namespace App\Repository;
use App\User;
use App\Story;
use App\StoryShare;



class UserRepository{
	
	public static function revokedToken(&$resquest)
    {
        return $resquest
        ->user()
        ->join('oauth_access_tokens', 'users.id', 'oauth_access_tokens.user_id')
        ->where('oauth_access_tokens.revoked', 0)
        ->update(['revoked' => 1]);
    }

    public static function searchEmail($user_id, $name, $model)
    {
        return $model->where('users.email', 'like', '%'.$name.'%')
        ->where('users.id', '!=', 1)
        ->where('users.id', '!=', $user_id)
        ->get();
    }

    public static function checkSharedStory($user)
    {
        $stories = Story::where('stories.shared_to' ,'like', '%'.$user->email.'%');
        if($stories->exists()){
            $stories = $stories->get();
            foreach ($stories as $key => $story) {
                StoryShare::create([
                    'story_id' => $story->id,
                    'user_id' => $user->id,
                ]);
            }
        }
    }

    public static function checkEmailIsExists($users, $email)
    {
        foreach ($users as $u => $user) {
            if($user['email'] == $email)
                return true;            
        }
        return false;
    }

    public static function getContacts($user)
    {
        $getUserInvolved = [];
        foreach ($user->story as $s => $story) {
            $getSharedTo = explode(',', $story->shared_to);
            foreach ($getSharedTo as $sh => $shared) {
                if($shared && !self::checkEmailIsExists($getUserInvolved, $shared)){
                    $getUser = User::where('email', 'LIKE','%'.$shared.'%')->first();

                    $total_stories_you_shared = Story::where('shared_to', 'LIKE', '%'.$shared.'%')
                    ->where('user_id','=', $user->id)
                    ->get()
                    ->count();

                    if($getUser){

                        $total_stories_shared_to_you = Story::where('shared_to', 'LIKE', '%'.$user->email.'%')
                        ->where('user_id','=', $getUser->id)
                        ->get()
                        ->count();

                        $getUserInvolved[] = [
                            'is_user' => 'Yes',
                            'firstname' => $getUser->firstname,
                            'lastname' => $getUser->lastname,
                            'nickname' => $getUser->nickname,
                            'birthday' => $getUser->birthday,
                            'email' => $getUser->email,
                            'total_stories' => $getUser->story->count(),
                            'total_books' => $getUser->books->count(),
                            'total_stories_shared_to_you' => $total_stories_shared_to_you,
                            'total_stories_you_shared' => $total_stories_you_shared,
                        ];
                    }else{
                        $getUserInvolved[] = [
                            'is_user' => 'No',
                            'firstname' => 'N/A',
                            'lastname' => 'N/A',
                            'nickname' => 'N/A',
                            'birthday' => 'N/A',
                            'email' => $shared,
                            'total_stories' => 'N/A',
                            'total_books' => 'N/A',
                            'total_stories_shared_to_you' => 'N/A',
                            'total_stories_you_shared' => $total_stories_you_shared,
                        ];
                    }
                }
            }
        }
        return $getUserInvolved;
    }

    // public static function getTopRecentView(){
    //     $authId = \Auth::user()->id;
        
    //     $story = StoryRepository::recentlyViewedStory($authId);
    //     $book = BookRepository::recentlyViewedBook($authId);
        
    //     $result = array_merge($story, $book);
    //     $updated_at = array_column($result, 'updated_at');

    //     array_multisort($updated_at, SORT_DESC, $result);
    //     return array_slice($result, 0, 3);
    // }
}