<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    protected $guarded = [];
    public $object_type = 'stories';

    public function user(){
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    public function pages(){
        return $this->hasMany('App\StoryPage', 'story_id');
    }

    public function shares(){
        return $this->hasMany('App\StoryShare');
    }

    public function scopeHasDefaultTitle($query)
    {
        return $query->where('title','LIKE','%New Story%');
    }

    public function scopeActive($query)
    {
        return $query->where('is_deleted', 0);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_deleted', 1);
    }
}
