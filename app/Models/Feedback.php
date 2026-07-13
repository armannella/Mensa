<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'feedbacks';
    protected $fillable = ['student_id', 'menu_id', 'reserve_id', 'rating', 'comment'];

    public function student() {
        return $this->belongsTo(Student::class);
    }

    public function menu() {
        return $this->belongsTo(Menu::class);
    }

    public function reserve() {
        return $this->belongsTo(Reserve::class);
    }
}
