<?php

namespace App\Models;

use App\Enums\MealEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;
    
    protected $fillable = ['date' , 'meal', 'canteen_id'];

    protected function casts(): array
    {
        return [
            'meal' => MealEnum::class,
            'date' => 'date',
        ];
    }

    public function canteen(){
        return $this->belongsTo(Canteen::class);
    }

    public function foods(){
        return $this->belongsToMany(Food::class , 'menu_detail')
                ->using(MenuDetail::class)
                ->withPivot(["capacity" , 'reserved' , 'daily_sale_capacity' , 'daily_sale_reserved'])
                ->withTimestamps()
                ->as('details');
    }

    public function reserves(){
        return $this->hasMany(Reserve::class);
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class);
    }

    public function aiSummary()
    {
        return $this->hasOne(MenuAiSummary::class);
    }

    // 

    public function getStartDateTime(): Carbon
    {
        $configs = Config::getAll();
        $lunchStart = $configs['lunch_start'];
        $dinnerStart = $configs['dinner_start'];
        $time = $this->meal->value === 'lunch' ? $lunchStart : $dinnerStart;
        return Carbon::parse($this->date->format('Y-m-d') . ' ' . $time);
    }

    public function getEndDateTime(): Carbon
    {
        $configs = Config::getAll();
        $lunchEnd = $configs['lunch_end'];
        $dinnerEnd = $configs['dinner_end'];
        $time = $this->meal->value === 'lunch' ? $lunchEnd : $dinnerEnd;
        return Carbon::parse($this->date->format('Y-m-d') . ' ' . $time);
    }

    //

    public function canBeReserved(){
        

        $configs = Config::getAll();
        $reserveHour = (int) $configs['reserve_time'];
        $start = $this->getStartDateTime();
        $deadline = $start->copy()->subHours($reserveHour);
        return now()->isBefore($deadline);
    }

    public function canBeDailyReserved(){

        if (env('APP_DEMO_MODE', false)) {
            return true;
        }
        $processStart = $this->getDailySaleStartTime();
        $end = $this->getDailySaleEndTime();
        
        return now()->isBetween($processStart , $end);
    }

    public function canBeServed(){
        if (env('APP_DEMO_MODE', false)) {
            return true;
        }


        $start = $this->getStartDateTime();
        $end = $this->getEndDateTime();
        return now()->isBetween($start , $end);
    }

    public function canDailySaleDefined(){

        if (env('APP_DEMO_MODE', false)) {
            return true;
        }

        $end = $this->getEndDateTime();
        return now()->isBefore($end);
    }

    public function canSeeFeedbacks(){
        if (env('APP_DEMO_MODE', false)) {
            return true;
        }
        
        $end = $this->getEndDateTime();
        return now()->isAfter($end);
    }

    public function getDailySaleStartTime(){
        $configs = Config::getAll();
        $minsBeforeStart = (int) $configs['daily_sale_reserve_time'];
        $start = $this->getStartDateTime();
        $processStart = $start->copy()->subMinutes($minsBeforeStart);
        return $processStart;
    }

    public function getDailySaleEndTime(){
        $end = $this->getEndDateTime();
        return $end;
    }
    public function scopeTodayOrFuture($query){
        return $query->where('date','>=' , now()->toDateString());
    }
    
    
}
