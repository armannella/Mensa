<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountPlan extends Model
{
    protected $fillable = ['name' , 'percentage' , 'description'];
    
    public function students(){
        return $this->hasMany(Student::class);
    }
}
