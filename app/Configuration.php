<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    protected $table = 'configurations';
    protected $primaryKey = "id";
    protected $fillable = [
    	'keycode',
    	'name',
    	'description',
    	'value',
    ];

    public static function getValue($key)
    {
        $key = self::where('keycode', '=', $key)->first();
        return $key->value;
    }
}
