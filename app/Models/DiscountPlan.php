<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountPlan extends Model
{
    protected $fillable = ['name' , 'percentage' , 'description'];

    protected function casts(): array
    {
        return [
            'percentage' => 'integer'
        ];
    }
    public function students(){
        return $this->hasMany(Student::class);
    }
}
