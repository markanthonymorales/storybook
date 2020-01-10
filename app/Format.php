<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Format extends Model
{
    protected $table = 'formats';
    protected $primaryKey = "id";
    protected $fillable = [
    	'format_group_id',
    	'name',
    	'value',
    ];

    public function group(){
    	return $this->hasOne('App\FormatGroup', 'id', 'format_group_id');
    }

    public function combination(){
    	return $this->hasMany('App\FormatAttribute', 'format_id');
    }
}
