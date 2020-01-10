<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $guarded = [];

    public $object_type = 'books';

    public function chapters(){
        return $this->hasMany('App\BookChapter', 'book_id')->orderBy('position', 'ASC');
    }

    public function user(){
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_deleted', 0);
    }

    public function scopeInactive($query)
    {
        return $this->where('is_deleted', 1);
    }

    public function scopeHasDefaultTitle($query)
    {
        return $query->where('title','LIKE','%New Book%');
    }
}
