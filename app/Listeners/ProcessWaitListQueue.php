<?php

namespace App\Listeners;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Events\FoodCapacityFreedUp;
use App\Models\WaitList;
use App\Notifications\WaitListNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProcessWaitListQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(FoodCapacityFreedUp $event): void
    {
        $menu = $event->menu; 
        $food = $event->food;

        $waitLists = WaitList::query()
            ->where('menu_id', $menu->id)
            ->where('food_id', $food->id)
            ->orderBy('created_at', 'asc')
            ->lockForUpdate()
            ->get();

        foreach($waitLists as $waitList) {
            
            if($event->freed_capacity <= 0){
                break;
            }

            $student = $waitList->student;

            if($student->wallet->hasEnoughMoney($waitList->price)){

                DB::transaction(function() use ($event, $student, $menu, $food, $waitList) {
                    $reserve = $student->reserves()->where('menu_id', $menu->id)->first();
                    
                    if(!$reserve){
                        $reserve = $student->reserves()->create([
                            'menu_id' => $menu->id,
                            'price' => $waitList->price,
                            'secret_barcode' => Str::random(16)
                        ]);

                    } else {
                        $reserve->price += $waitList->price;
                        $reserve->save();
                    }
    
                    $reserve->foods()->attach($food->id);

                    if(!$event->is_dailySale) {
                        \App\Models\MenuDetail::where('menu_id', $menu->id)
                            ->where('food_id', $food->id)
                            ->increment('reserved');
                    } else {
                        \App\Models\MenuDetail::where('menu_id', $menu->id)
                            ->where('food_id', $food->id)
                            ->increment('daily_sale_reserved');
                    }                    

                    $student->wallet->transactions()->create([
                        'type' => TransactionType::OUTCOME, 
                        'amount' => $waitList->price, 
                        'status' => TransactionStatus::SUCCESS, 
                        'note' => 'reserved for a meal (Auto WaitList)'
                    ]);
                    $student->wallet->balance -= $waitList->price;
                    $student->wallet->save();
                });

                
                $event->freed_capacity--;
                $student->user->notify(new WaitListNotification($menu , $food , 'success'));
                $waitList->delete();
                
            } else {
                $student->user->notify(new WaitListNotification($menu , $food , 'failed'));
                $waitList->delete();
            }
        }
    }
}
