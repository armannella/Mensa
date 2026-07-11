<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name' , 'price'];
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2'
        ];
    }
    public function foods(){
        return $this->hasMany(Food::class);
    }

}
