<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StoryPage extends Model
{
    protected $table = 'story_pages';
    protected $primaryKey = "id";
    protected $fillable = [
    	'story_id',
    	'order_number',
    	'is_colored',
    	'content',
    ];
}
