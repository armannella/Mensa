<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Override;

class WaitList extends Model
{
    /** @use HasFactory<\Database\Factories\WaitListFactory> */
    use HasFactory;
    protected $fillable = ['student_id' , 'menu_id' , 'food_id' , 'price'];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2' ,
        ];
    }

    public function student(){
        return $this->belongsTo(Student::class);
    }

    public function menu(){
        return $this->belongsTo(Menu::class);
    }

    public function food(){
        return $this->belongsTo(Food::class);
    }


    public function positionInWaitList(){

        return WaitList::query()
            ->where('menu_id', $this->menu_id)
            ->where('food_id', $this->food_id)
            ->where('created_at', '<=', $this->created_at)
            ->count();
    }

    
}
