<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Facades\Auth;

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

    public function isCapacityFinished(){
        if($this->capacity - $this->reserved === 0){
            return true;
        }
        else {
            return false ;
        }
    }

    public function isDailySaleCapacityFinished(){

        if($this->daily_sale_capacity - $this->daily_sale_reserved === 0){
            return true;
        }
        else {
            return false ;
        }
    }

    public function getWaitListSize(){

        return WaitList::query()
                ->where('menu_id' , $this->menu_id)
                ->where('food_id' , $this->food_id)
                ->count();
    }

    public function hasStudentWaitList(){
        $student = Auth::user()->student ;
        $waitList = $student->waitLists()->where('food_id' , $this->food_id)->where('menu_id' , $this->menu_id)->first();
        return $waitList;
    }
    
}
