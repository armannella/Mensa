<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Canteen;
use App\Models\Category;
use App\Models\Food;
use App\Models\Menu;
use App\Models\Reserve;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ReserveController extends Controller
{
    public function canteens(){
        $canteens = Canteen::all();
        return view('student.reserve.canteens' , compact('canteens'));
    }

    public function menus(Canteen $canteen){
        $menus = $canteen->menus()->TodayOrFuture()->orderBy('date' ,'asc')->get();
        return view('student.reserve.menus' , compact('menus'));
    }

    public function showNormalMenu(Canteen $canteen , Menu $menu){
        if(!Gate::allows('isMenuForTheCanteen' , [$menu , $canteen])){
            abort(403,"this menu is not for this Canteen Bro");
        }

        if(!$menu->canBeReserved()){
            abort(403,"Reservation Time is finished");
        }

        $foods = $menu->foods()->with('category')->get();
        $categories = Category::all();
        return view('student.reserve.foods' , compact('foods' , 'categories','menu'));
    }

    public function storeNormalReserve(Canteen $canteen , Menu $menu , Request $request){
        $request->validate(['foods' => ['required' , 'array' , 'min:1'] ,
                            'foods.*' => ['required','integer' , 'exists:food,id'] ]);
        
        //checking ha :
        $student = Auth::user()->student ;
        if(!Gate::allows('isMenuForTheCanteen' , [$menu , $canteen])){
            abort(403,"this menu is not for this Canteen Bro");
        }

        if(!$menu->canBeReserved()){
            abort(403,"Reservation Time is finished");
        }

        if(Gate::allows('studentAlreadyReserved' , [$menu , $student])){
            abort(403,"you already reserved for this menu bro :) ");
        }

        if(Gate::allows('studentAlreadyReservedAnotherCanteen' , [$menu , $student])){
            abort(403,"you already reserved for this meal in another Mensa :) ");
        }



        DB::transaction(function() use ($request , $student , $menu){
            $selected_foods = $request->foods ;
            $reserve = $student->reserves()->create(['menu_id' => $menu->id]);
            $totalprice = 0 ;
            foreach($selected_foods as $categoryID => $foods){
                if($foods->count() > 1){
                    return back()->withErrors(['error' , 'you chosed more than 1 food from each category']);
                }
                $food = Food::find($foods[0]);
                $capacity = $menu->foods()->where('food_id' , $food->id)->pluck('capacity') ;
                if($capacity > 0) {
                    $reserve->foods()->attach([$food->id]);
                    $basePrice = $food->category->price ;
                    $totalprice += ($basePrice);
                }
                else{
                    return back()->withErrors(['error' , 'you chosed a food that capacity is finished try again']);
                }
                
            }
            $off = $student->discountPlan->percentage ;
            $totalprice = $totalprice * $off /100 ;
            if($student->wallet->hasEnoughMoney($totalprice)){
                $reserve->update(['price' => $totalprice]);
            }
            else {
                return back()->withErrors(['error' , "you dont have enough money to reserve {$totalprice} Euro"]);

            }

            
        });

        return back()->with('success' , 'you successfully reserved for this menu');

        
                            
    }

    public function showDailyReserveMenu() {
        //show daily reserve menu
    }

    public function storeDailyReserve(){
        //show daily reserve menu
    }


    public function delivereMeal(Reserve $reserve){
        //generate Barcode
    }

    public function cancelReservation(Reserve $reserve){

    }


    public function showReserveFoods(Reserve $reserve){

    }

    public function showFeedbackPage(){

    }

    public function storeFeedback(){

    }

    public function showAllReserves(){

    }

}
