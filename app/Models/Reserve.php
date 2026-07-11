<?php

namespace App\Models;

use App\Enums\ReserveStatus;
use Illuminate\Database\Eloquent\Model;

class Reserve extends Model
{
    protected $fillable = ['menu_id' , 'student_id' , 'price','status'];

    protected function casts(): array
    {
        return [
            'status' => ReserveStatus::class,
            'price' => 'decimal:2'
        ];
    }

    public function menu(){
        return $this->belongsTo(Menu::class);
    }

    public function student(){
        return $this->belongsTo(Student::class);
    }

    public function foods(){
        return $this->belongsToMany(Reserve::class,'reserve_detail')->withTimestamps();
    }
}
