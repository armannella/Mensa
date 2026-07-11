<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    protected $fillable = ['key' , 'value' , 'title'];

    private static $settings = null; 

    public static function getAll()
    {
        if (is_null(self::$settings)) {
            self::$settings = self::pluck('value', 'key');
        }
        return self::$settings;
    }
}
