<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StoryShare extends Model
{
    protected $table = 'story_shares';
    protected $fillable = [
    	'story_id',
        'user_id',
    	'is_allow_edit',
    ];
    public $timestamps = false;

    public function user(){
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    public function story(){
        return $this->hasMany('App\Story');
    }
}
