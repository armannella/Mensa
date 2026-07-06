<?php

namespace App\Models;

use App\Enums\ScholarshipStatus;
use Illuminate\Database\Eloquent\Model;

class ScholarshipApplication extends Model
{
    protected $fillable = ['student_note', 'status' , 'admin_note'];
    protected function casts(): array
    {
        return [
            'status' => ScholarshipStatus::class,
        ];
    }

    public function student(){
        return $this->belongsTo(Student::class);
    }

}
