<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Canteen;
use App\Models\Food;
use App\Models\Menu;
use App\Models\WaitList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class WaitListController extends Controller
{
    public function addToWaitList(Canteen $canteen , Menu $menu , Food $food) {
        $student = Auth::user()->student;

        if (!Gate::allows('isMenuForTheCanteen', [$menu, $canteen])) {
            abort(403, "This menu is not for this Canteen Bro");
        }

        if (!$menu->canBeReserved()) {
            abort(403, "Reservation Time is finished");
        }

        if (Gate::allows('studentAlreadyReservedAnotherCanteen', [$menu, $student])) {
            abort(403, "You already reserved for this meal in another Mensa :)");
        }

        $selected_food = $menu->foods()->find($food->id);
            
        if (!$selected_food || ($selected_food->details->capacity - $selected_food->details->reserved) > 0) {
            return back()->withErrors(['error' => 'You chose a food that capacity is not finished yet. Try again.']);
        }

        $price = $food->category->price ;
        $off = $student->discountPlan?->percentage ?? 0;
        $totalprice = $price - ($price * $off / 100);

        $waitList = WaitList::create([
            'menu_id' => $menu->id ,
            'food_id' => $food->id ,
            'student_id' => $student->id ,
            'price' => $totalprice ,
        ]) ;

        return back()->with('success', "You successfully added for the {{$food->name}} Wait List");
    }

    public function removeWaitList(Canteen $canteen , Menu $menu , Food $food , WaitList $waitList) {
        $student = Auth::user()->student;

         if (!Gate::allows('isMenuForTheCanteen', [$menu, $canteen])) {
            abort(403, "This menu is not for this Canteen Bro");
        }

        if($waitList->menu_id === $menu->id && $waitList->student_id === $student->id && $waitList->food_id === $food->id){
            $waitList->delete();
            return back()->with('success' , "you are out of waiting list for {{$food->name}} !");
        }
        else {
            abort(403, "something is wrong about your waitlist :)");
        }
    }


}
