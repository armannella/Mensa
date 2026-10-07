<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    use HasFactory;
    
    protected $fillable = ['name' , 'ingredients' , 'image_path' , 'category_id'];

    public function category(){
        return $this->belongsTo(Category::class);
    }
    
    public function menus(){
        return $this->belongsToMany(Menu::class , 'menu_detail')
                ->using(MenuDetail::class)
                ->withPivot(["capacity" , 'reserved' , 'daily_sale_capacity' , 'daily_sale_reserved'])
                ->withTimestamps()
                ->as('details');
    }

    public function reserves(){
        return $this->belongsToMany(Reserve::class , 'reserve_detail')->withTimestamps();
    }

    public function waitLists(){
        return $this->hasMany(WaitList::class);
    }
}
