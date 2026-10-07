<?php

namespace App\Http\Controllers\Canteen;

use App\Enums\MealEnum;
use App\Events\FoodCapacityFreedUp;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Food;
use App\Models\Menu;
use App\Services\DemandForecastService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
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
            'foods' => ['required' , 'array'] ,
            'foods.*' => ['array'] ,
            'foods.*.quantity' => ['nullable', 'integer', 'min:1']
        ]);

        $selectedFoods = array_filter($request->foods, function($details) {
            return !empty($details['quantity']) && $details['quantity'] > 0;
        });

        if (empty($selectedFoods)) {
            return back()->withErrors(['error' => 'You must set a quantity for at least one food to create a menu!']);
        }

        $canteen = Auth::user()->canteen;

        $check_menu = Menu::query()
            ->where('meal' , $request->meal)
            ->where('date' , $request->date)
            ->where('canteen_id', $canteen->id)
            ->exists();
            
        if($check_menu){
            return back()->withErrors(['error' => 'There already exists a menu for this day in your canteen.']);
        }
        
        $menu = $canteen->menus()->create([
            'date' => $request->date ,
            'meal' => $request->meal
        ]);

        foreach($selectedFoods as $foodID => $details){
            $menu->foods()->attach($foodID , ['capacity' => $details['quantity']]);
        }

        return back()->with('success' , "Added Menu Successfully for date {$menu->date->format('Y-m-d')} meal : {$menu->meal->value}");
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
        $foodItem = $menu->foods()->find($request->food_id);
        $menu->foods()->updateExistingPivot($request->food_id, [
            'daily_sale_capacity' => $request->daily_capacity ,
            'daily_sale_reserved' => $foodItem->details->daily_sale_reserved ?? 0, 
        ]);
        $food = Food::find($request->food_id);
        
        event(new FoodCapacityFreedUp($food, $menu , $request->daily_capacity , true));

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

    public function generateAiSummary(Menu $menu){

        if (!Gate::allows('isMenuForTheCanteen', [$menu, Auth::user()->canteen])) {
            abort(403, 'This menu is not for your Canteen');
        }

        $comments = $menu->feedbacks()->pluck('comment')->toArray();

        if (empty($comments)) {
            return back()->withErrors(['error' => 'No feedbacks available to analyze.']);
        }

        $prompt = "Please act as a data analyst. Analyze these student feedbacks for our canteen meal and provide a concise, single-paragraph summary of the pros, cons, and overall sentiment. Feedbacks: " . implode(" | ", $comments);

        try {
            $response = Http::withoutVerifying()->timeout(8)->post('https://text.pollinations.ai/', [
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a helpful university canteen data analyst. Keep summaries under 80 words.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'model' => 'openai',
            ]);

            if ($response->successful() && !empty(trim($response->body()))) {
                $summaryText = trim($response->body());
            } else {
                throw new \Exception('AI service returned empty response');
            }
        } catch (\Exception $e) {
            $avgRating = round($menu->feedbacks()->avg('rating'), 1);
            $summaryText = "AI Analysis: Based on " . count($comments) . " reviews (Avg Rating: {$avgRating}/5), students shared mixed-to-positive feedback regarding food quality and portion sizes.";
            return back()->withErrors(['error' => 'AI API Connection Failed: ' . $e->getMessage()]);
        }

        $menu->aiSummary()->updateOrCreate(
            ['menu_id' => $menu->id],
            ['summary' => $summaryText]
        );

        return back()->with('success', 'AI Summary generated successfully!');
    }

    public function predictFoodCapacity(Request $request, DemandForecastService $forecastService)
    {
        $request->validate([
            'date' => ['required', 'date'],
            'meal' => ['required', new Enum(MealEnum::class)],
        ]);
        
        $date = Carbon::parse($request->date);
        $mealEnum = MealEnum::tryFrom($request->meal);
        $predictions = [];
        $foods = Food::all();
        foreach ($foods as $food) {
            if ($food) {
                $prediction = $forecastService->predictCapacity($food, $date, $mealEnum);
                $predictions[$food->id] = [
                    'capacity' => $prediction['predicted_capacity'],
                    'info' => $prediction['info']
                ];
            }
        }

        return response()->json($predictions);
    }




 
}
