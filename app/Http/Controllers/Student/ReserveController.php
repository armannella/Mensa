<?php

namespace App\Http\Controllers\Student;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Http\Controllers\Controller;
use App\Models\Canteen;
use App\Models\Category;
use App\Models\Food;
use App\Models\Menu;
use App\Models\Reserve;
use App\Models\Student;
use App\Services\DynamicPricingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

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

    public function storeNormalReserve(Canteen $canteen, Menu $menu, Request $request){
        
        $request->validate([
            'foods' => ['required', 'array', 'min:1'],
            'foods.*' => ['required', 'integer', 'exists:food,id']
        ]);
        
        $student = Auth::user()->student;

        if (!Gate::allows('isMenuForTheCanteen', [$menu, $canteen])) {
            abort(403, "This menu is not for this Canteen Bro");
        }

        if (!$menu->canBeReserved()) {
            abort(403, "Reservation Time is finished");
        }

        if (Gate::allows('studentAlreadyReserved', [$menu, $student])) {
            abort(403, "You already reserved for this menu bro :)");
        }

        if (Gate::allows('studentAlreadyReservedAnotherCanteen', [$menu, $student])) {
            abort(403, "You already reserved for this meal in another Mensa :)");
        }

        $selected_foods = $request->foods;
        $totalprice = 0;
        $foodsToAttach = [];

        //check prices and capacities
        foreach ($selected_foods as $categoryID => $foodID) {
            $food = $menu->foods()->find($foodID);
            
            if (!$food || ($food->details->capacity - $food->details->reserved) <= 0) {
                return back()->withErrors(['error' => 'You chose a food that capacity is finished. Try again.']);
            }
            
            $foodsToAttach[] = $food;
            $totalprice += $food->category->price;
        }

        // Apply discount
        $off = $student->discountPlan?->percentage ?? 0;
        $totalprice = $totalprice - ($totalprice * $off / 100);

        // Check wallet
        if (!$student->wallet->hasEnoughMoney($totalprice)) {
            return back()->withErrors(['error' => "You don't have enough money to reserve. Required: {$totalprice}"]);
        }

        // Perform DB Transaction safely
        DB::transaction(function() use ($student, $menu, $foodsToAttach, $totalprice) {
            
            $reserve = $student->reserves()->create([
                'menu_id' => $menu->id,
                'price' => $totalprice,
                'secret_barcode' => Str::random(16) // Generate unique barcode
            ]);

            foreach ($foodsToAttach as $food) {
                $reserve->foods()->attach($food->id);
                
                // Increment reserved capacity
                $menu->foods()->updateExistingPivot($food->id, [
                    'reserved' => $food->details->reserved + 1
                ]);
            }

            // Deduct wallet balance
            $student->wallet->transactions()->create(['type' => TransactionType::OUTCOME , 'amount' => $totalprice , 'status' => TransactionStatus::SUCCESS , 'note' => 'reserved for a meal']);
            $student->wallet->balance -= $totalprice;
            $student->wallet->save();
        });

        return redirect()->route('student.reserves.all')->with('success', 'You successfully reserved for this menu');
    }

    public function showDailyReserveMenu(Canteen $canteen , Menu $menu , DynamicPricingService $dynamicPricingService){

        if(!Gate::allows('isMenuForTheCanteen' , [$menu , $canteen])){
            abort(403,"this menu is not for this Canteen Bro");
        }

        if(!$menu->canBeDailyReserved()){
            abort(403,"its not time to daily reserve");
        }
        
        $foods = $menu->foods()->wherePivot('daily_sale_capacity' , '!=' , null)->with('category')->get();
        $categories = Category::all();
        $foodsPricePerID =[];

        $dynamicPricingService->clearPriceLock($menu);
        foreach($foods as $food){
            $food->flash_quote = $dynamicPricingService->FlashSaleDynamicPrice($food , $menu);
            $foodsPricePerID[$food->id] = $food->flash_quote['price'];
        }
        $dynamicPricingService->lockPricesInSession($menu , $foodsPricePerID);
        return view('student.reserve.daily_foods' , compact('foods' , 'categories','menu'));
    }

    public function storeDailyReserve(Canteen $canteen , Menu $menu , Request $request , DynamicPricingService $dynamicPricingService){
         $request->validate([
            'foods' => ['required', 'array', 'min:1'],
            'foods.*' => ['required', 'integer', 'exists:food,id']
        ]);
        
        $student = Auth::user()->student;

        if (!Gate::allows('isMenuForTheCanteen', [$menu, $canteen])) {
            abort(403, "This menu is not for this Canteen Bro");
        }

        if (!$menu->canBeDailyReserved()) {
            abort(403, "Reservation Time is finished");
        }

        if (Gate::allows('studentAlreadyReserved', [$menu, $student])) {
            abort(403, "You already reserved for this menu bro :)");
        }

        if (Gate::allows('studentAlreadyReservedAnotherCanteen', [$menu, $student])) {
            abort(403, "You already reserved for this meal in another Mensa :)");
        }

        $selected_foods = $request->foods;
        $totalprice = 0;
        $foodsToAttach = [];

        // Check prices and capacities
        foreach ($selected_foods as $categoryID => $foodID) {
            $food = $menu->foods()->find($foodID);
            
            if (!$food || ($food->details->daily_sale_capacity - $food->details->daily_sale_reserved) <= 0) {
                return back()->withErrors(['error' => 'You chose a food that capacity is finished. Try again.']);
            }
            
            $lockedPrice = $dynamicPricingService->getLockedPrice($menu, (int) $foodID);
            
            if ($lockedPrice === null) {
                return redirect()->route('student.reserves.reserve.dailymenu', [$canteen->id, $menu->id])
                    ->withErrors(['error' => 'Your 2-minute price lock has expired! Prices have been updated. Please review and confirm again.']);
            }

            $liveQuote = $dynamicPricingService->FlashSaleDynamicPrice($food, $menu);
            $appliedPrice = min($lockedPrice, $liveQuote['price']);

            $foodsToAttach[] = $food;
            $totalprice += $appliedPrice;
        }

        $dynamicPricingService->clearPriceLock($menu);

        // Apply discount
        $off = $student->discountPlan?->percentage ?? 0;
        $totalprice = round($totalprice - ($totalprice * $off / 100), 2);

        // Check wallet
        if (!$student->wallet->hasEnoughMoney($totalprice)) {
            return back()->withErrors(['error' => "You don't have enough money to reserve. Required: {$totalprice}"]);
        }

        // Perform DB Transaction safely
        DB::transaction(function() use ($student, $menu, $foodsToAttach, $totalprice) {
            
            $reserve = $student->reserves()->create([
                'menu_id' => $menu->id,
                'price' => $totalprice,
                'secret_barcode' => Str::random(16) // Generate unique barcode
            ]);

            foreach ($foodsToAttach as $food) {
                $reserve->foods()->attach($food->id);
                
                // Increment reserved capacity
                $menu->foods()->updateExistingPivot($food->id, [
                    'daily_sale_reserved' => $food->details->daily_sale_reserved + 1
                ]);
            }

            // Deduct wallet balance
            $student->wallet->transactions()->create(['type' => TransactionType::OUTCOME , 'amount' => $totalprice , 'status' => TransactionStatus::SUCCESS , 'note' => 'reserved for a meal']);
            $student->wallet->balance -= $totalprice;
            $student->wallet->save();
        });

        return redirect()->route('student.reserves.all')->with('success', 'You successfully reserved for this menu');
    }
    


    public function delivereMeal(Reserve $reserve){
        $menu = $reserve->menu ;
        if(!$menu->canBeServed()){
            abort(403 , 'this menu is not open for delivery');
        }

        if(!Gate::allows('isReserveForStudent' , $reserve)){
            abort(403 , 'this reserve is not  for you Baby');
        }

        $barcode = $reserve->encrypteBarcode();

        $foods = $reserve->foods()->get();
        return view('student.reserve.delivery' , compact('foods' , 'barcode' , 'reserve' , 'menu'));

    }

    public function cancelReservation(Reserve $reserve , Request $request){
        if(!Gate::allows('isReserveForStudent' , $reserve)){
            abort(403 , 'this Reserve is not for u dear Student');
        }

        if(!$reserve->canBeCancelled()){
            abort(403 , 'the cancell time is finished for this menu');
        }

        $fine = $reserve->getFinePercent();
        $payed_price = $reserve->price;
        $refund = $payed_price * (100 - $fine) /100 ;
        $student = Auth::user()->student ;
        DB::transaction(function() use ($reserve , $refund , $student){

            $menu = $reserve->menu;
            foreach ($reserve->foods as $food) {
                $menuFood = $menu->foods()->find($food->id);
                
                if ($menuFood) {
                    $menu->foods()->updateExistingPivot($food->id, [
                        'reserved' => max(0, $menuFood->details->reserved - 1)
                    ]);
                }
            }


            $reserve->foods()->detach();
            $reserve->delete();
            $student->wallet->transactions()->create(['type' => TransactionType::INCOME , 'amount' => $refund , 'status' => TransactionStatus::SUCCESS , 'note' => 'refund of cancellation for a meal']);
            $student->wallet->balance += $refund;
            $student->wallet->save();
        });

        return redirect()->route('student.reserves.all')->with('success', 'You successfully cancelled the reservation');



    }


    public function showReserveFoods(Reserve $reserve){
        // reserve is for the student 
        if(!Gate::allows('isReserveForStudent' , $reserve)){
            abort(403 , 'this Reserve is not for u dear Student');
        }
        //

        $foods = $reserve->foods()->get();
        $categories = Category::all();
        return view('student.reserve.viewreserve' , compact('foods' , 'categories' , 'reserve'));
    }

    public function showFeedbackPage(Reserve $reserve)
    {
        if (!Gate::allows('isReserveForStudent', $reserve)) {
            abort(403, 'This reserve is not for you!');
        }

        if ($reserve->status->value !== 'delivered') {
            abort(403, 'You can only leave feedback for delivered meals.');
        }

        if (!$reserve->canBeReviewed()) {
            abort(403, 'You can not review this reserve it is finished');
        }

        if ($reserve->feedback()->exists()) {
            return redirect()->route('student.reserves.all')->withErrors(['error' => 'You have already submitted feedback for this meal.']);
        }

        return view('student.reserve.feedback', compact('reserve'));
    }

    public function storeFeedback(Request $request, Reserve $reserve)
    {
        $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'min:3', 'max:500']
        ]);

        if (!Gate::allows('isReserveForStudent', $reserve)) {
            abort(403, 'This reserve is not for you!');
        }

        if ($reserve->status->value !== 'delivered') {
            abort(403, 'You can only leave feedback for delivered meals.');
        }

        if (!$reserve->canBeReviewed()) {
            abort(403, 'You can not review this reserve it is finished');
        }

        if ($reserve->feedback()->exists()) {
            return redirect()->route('student.reserves.all')->withErrors(['error' => 'You have already submitted feedback for this meal.']);
        }

        $reserve->feedback()->create([
            'student_id' => Auth::user()->student->id,
            'menu_id' => $reserve->menu_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return redirect()->route('student.reserves.all')->with('success', 'Thank you! Your feedback has been submitted successfully.');
    }

    public function showAllReserves(){
        $student = Auth::user()->student ;
        $reserves = $student->reserves()
            ->with(['menu.canteen', 'foods'])
            ->latest()
            ->paginate(10);
    
        return view('student.reserve.allreserves' , compact('reserves'));
    }

}
