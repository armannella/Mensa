<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class MenuDetail extends Pivot
{
    use HasFactory;
    
    public $incrementing = true;
    protected $table = "menu_detail";
    protected $fillable = ['menu_id','food_id',"capacity" , 'reserved' , 'daily_sale_capacity' , 'daily_sale_reserved'];

    protected function casts(): array
    {
        return [
            "capacity" => 'integer',
            'reserved' => 'integer',
            'daily_sale_capacity' => 'integer',
            'daily_sale_reserved'=> 'integer',
        ];
    }
    
}
