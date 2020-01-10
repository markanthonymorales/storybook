<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BookChapterPage extends Model
{
    protected $table = 'book_chapter_pages';
    protected $primaryKey = "id";
    protected $fillable = [
    	'book_chapter_id',
    	'content',
    	'is_colored'
    ];

    public function chapter(){
        return $this->belongTo(App\BookChapter::class);
    }
}
