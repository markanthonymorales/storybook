<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RecentViewed extends Model
{
    protected $table = 'recent_viewed';
    protected $fillable = [
    	'user_id',
    	'object_id',
    	'object_name',
    ];
}
