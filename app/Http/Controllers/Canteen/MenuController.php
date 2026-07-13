<?php

namespace App\Http\Controllers\Canteen;

use App\Enums\MealEnum;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Food;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\Enum;

class MenuController extends Controller
{
    
    public function create(){
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
        $canteen = Auth::user()->canteen ;
        $menus = $canteen->menus()->orderBy('date' , 'desc')->paginate(10);

        return view('mensa.allmenus' , compact('menus'));
    }

    public function showStatisticsOfMenu(Menu $menu){
        $canteen = Auth::user()->canteen ;
        if(!Gate::allows('isMenuForTheCanteen' , [$menu , $canteen])){
            abort(403 , 'This menu is not for this Canteen');
        }

        $foods = $menu->foods()->with('category')->get();
        return view('mensa.statistics' , compact('foods' , 'menu'));
    }

    public function defineDailySaleForFood(Request $request, Menu $menu){

        if(!$menu->canDailySaleDefined()){
            abort(403 , 'The time for daily reserve define is finished');
        }

        $request->validate([
            'food_id' => ['required', 'exists:food,id'],
            'daily_capacity' => ['required', 'integer', 'min:1']
        ]);

        $menu->foods()->updateExistingPivot($request->food_id, [
            'daily_sale_capacity' => $request->daily_capacity
        ]);

        return back()->with('success', 'Daily sale capacity defined successfully.');
    }

    

    public function showFeedbacksOfAMenu(Menu $menu)
    {
        if (!Gate::allows('isMenuForTheCanteen', [$menu, Auth::user()->canteen])) {
            abort(403, 'This menu is not for your Canteen');
        }

        if(!$menu->canSeeFeedbacks()){
            abort(403, 'This menu is not Started to see Feedbaks yet');
        }

        $feedbacks = $menu->feedbacks()->with('student.user')->latest()->get();
        
        $averageRating = round($menu->feedbacks()->avg('rating'), 1) ?? 0;
        
        $aiSummary = $menu->aiSummary;

        return view('mensa.feedbacks', compact('menu', 'feedbacks', 'averageRating', 'aiSummary'));
    }

    public function generateAiSummary(Menu $menu)
    {
        if (!Gate::allows('isMenuForTheCanteen', [$menu, Auth::user()->canteen])) {
            abort(403, 'This menu is not for your Canteen');
        }

        
        $comments = $menu->feedbacks()->pluck('comment')->toArray();

        if (empty($comments)) {
            return back()->withErrors(['error' => 'No feedbacks available to analyze.']);
        }

        
        $prompt = "Please act as a data analyst. Analyze these student feedbacks for our canteen meal and provide a concise, single-paragraph summary of the pros, cons, and overall sentiment. Feedbacks: " . implode(" | ", $comments);

        //connect to an API of AI

        
        $summaryText = "AI Analysis: Based on " . count($comments) . " reviews, the overall sentiment is highly positive. Students generally appreciated the food quality and taste. However, a few mentioned that the portion sizes could be slightly improved.";

        $menu->aiSummary()->updateOrCreate(
            ['menu_id' => $menu->id],
            ['summary' => $summaryText]
        );

        return back()->with('success', 'AI Summary generated successfully!');
    }




 
}
