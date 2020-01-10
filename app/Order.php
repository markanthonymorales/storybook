<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';
    protected $primaryKey = "id";
    protected $fillable = [
    	'book_id',
    	'user_id',

    	'fullname',
    	'email',
    	'title',
    	'author',

    	'total_height',
    	'total_width',
    	'total_height_incl_bleed',
    	'total_width_incl_bleed',

    	'total_book',
    	'total_page',
    	'colored_index',
    	'total_colored_page',

    	'paper_id',
    	'binding_id',
    	'cover_id',
    	'lamination_id',

    	'manufacturing_cost',
    	'retail_price',

    	'shipping_option',
    	'shipping_price',
    	'book_price',
    	'total_price',

    	'address1',
    	'address2',
    	'street',
    	'city',
    	'zipcode',
    	'country',
    	'xml_directory',
    ];
}
