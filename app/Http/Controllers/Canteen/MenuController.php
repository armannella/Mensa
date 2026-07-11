<?php

namespace App\Http\Controllers\Canteen;

use App\Enums\MealEnum;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Food;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Enum;

class MenuController extends Controller
{
    
    public function create()
    {
        $categories = Category::all();
        $meals = MealEnum::cases();
        $foods = Food::query()->with('category')->get();
        return view('mensa.addmenu' , compact('meals' , 'categories' , 'foods'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'date' => ['required', 'date' , 'after:today'],
            'meal' => ['required', new Enum(MealEnum::class)],
            'foods' => ['required' , 'array' , 'min:1'] ,
            'foods.*' => ['array' ] ,
            'foods.*.quantity' => ['nullable','integer']
        ]);

        $canteen = Auth::user()->canteen;

        //check there is not any menu for that time
        $check_menu = Menu::query()->where('meal' , $request->meal)->where('date' , $request->date)->where('canteen_id',$canteen->id)->exists();
        if($check_menu){
            return back()->withErrors(['date' => 'there exist a menu for this day']);
        }
        
        // create menu :

        $menu = $canteen->menus()->create([
            'date' => $request->date ,
            'meal' => $request->meal
        ]);

        //create menu Details :

        foreach($request->foods as $foodID => $details){
            $quantity= $details['quantity'];
            if(!empty($quantity) && $quantity>0){
                $menu->foods()->attach($foodID , ['capacity' => $quantity]);
            }
        }

        //return back :

        return back()->with('success' , "added Menu Successfully for date {$menu->date} meal : {$menu->meal->value}");

    }

    public function showAllMenus(){
        //show all menus of a specific canteen ;
    }

    public function showStatisticsOfMenu(){
        //show page how many reserved we had and also define daily sales
    }

    public function defineDailySaleForFood(){

    }

    public function showDeliveryPage(){
        // show the page for serving time
    }

    public function DelieverReserve(){
        // storing a barcode and return list of foods and deliever the reserve
    }

    public function showFeedbacksOfAMenu(){
        
    }





 
}
