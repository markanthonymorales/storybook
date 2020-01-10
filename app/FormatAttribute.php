<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FormatAttribute extends Model
{
	protected $table = 'format_attribute';
    protected $primaryKey = "id";
    protected $fillable = [
    	'format_id',
    	'attribute_type_id',
    	'price',
    	'color_price',
    ];

    public function format(){
    	return $this->hasMany('App\Format', 'id', 'format_id');
    }

    public function attribute(){
    	return $this->hasMany('App\Attribute', 'id', 'attribute_type_id');
    }
}
