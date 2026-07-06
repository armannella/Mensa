<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = ['name' , 'codice_fiscale' , 'matricola' , 'discount_plan_id'];
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function discountPlan(){
        return $this->belongsTo(DiscountPlan::class);
    }

    public function scholarshipApplication(){
        return $this->hasOne(ScholarshipApplication::class);
    }

    public function documents(){
        return $this->hasMany(Document::class);
    }
}
