<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FormatGroup extends Model
{
    protected $table = 'format_groups';
    protected $primaryKey = "id";
    protected $fillable = [
    	'name',
    ];

    public function formats(){
    	return $this->hasMany('App\Format', 'format_group_id');
    }
}
