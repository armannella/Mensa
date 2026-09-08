<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Canteen extends Model
{
    protected $fillable = ['name' , 'address' ,  'user_id'];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function menus(){
        return $this->hasMany(Menu::class);
    }
}
