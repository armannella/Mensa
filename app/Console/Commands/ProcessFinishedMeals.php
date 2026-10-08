<?php

namespace App\Console\Commands;

use App\Enums\ReserveStatus;
use App\Models\Menu;
use App\Models\WaitList;
use App\Notifications\AskForFeedbackNotification;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('mensa:process-finished-meals')]
#[Description('change non delivered reserves to Finished and Remove All Waiting lists')]
class ProcessFinishedMeals extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $menus = Menu::query()
            ->whereDate('date', '<=', today())
            ->where('is_processed', false)
            ->get()
            ->filter(function ($menu) {
                return $menu->isFinished();
            });

        foreach ($menus as $menu) {
            
            
            WaitList::where('menu_id', $menu->id)->delete();

            $menu->reserves()
                 ->where('status', ReserveStatus::ACTIVE->value) 
                 ->update(['status' => ReserveStatus::MISSED->value]);

            
            $deliveredReserves = $menu->reserves()
                ->where('status', 'delivered') 
                ->doesntHave('feedback')
                ->with('student.user')
                ->get();

            
            foreach ($deliveredReserves as $reserve) {
                if ($reserve->student->user) {
                    $reserve->student->user->notify(new AskForFeedbackNotification($reserve));
                }
            }

            $menu->update(['is_processed' => true]);
        }
        
        $this->info("{$menus->count()} Menus Processed Bro.");
    }
    
}
