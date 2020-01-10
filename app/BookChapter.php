<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BookChapter extends Model
{
    protected $guarded = [];

    public function pages(){
        return $this->hasMany('App\BookChapterPage', 'book_chapter_id');
    }

    public function stories(){
        return $this->hasOne('App\Story', 'id', 'story_id');
    }
}
