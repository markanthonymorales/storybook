<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    protected $table = 'attribute_types';
    protected $primaryKey = "id";
    protected $fillable = [
    	'keycode',
    	'name',
    	'order_number',
    ];

    public function combination(){
    	return $this->hasMany('App\FormatAttribute', 'format_id');
    }
}
