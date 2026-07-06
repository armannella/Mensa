<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiscountPlan;
use Illuminate\Http\Request;

class DiscountPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $discountPlans = DiscountPlan::all();
        return view('admin.discountplans',compact('discountPlans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate(['name' => ['required' , 'string' , 'min:3'] ,
                                    'percentage' =>['required' , 'integer' , 'min:1' , 'max:100'] ,
                                    'description' => ['string' ,'nullable', 'max:255']]);
        
        $discountPlan = DiscountPlan::create($data);
        return back()->with(['success' => 'you added new Discount plan']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DiscountPlan $discountPlan)
    {
        $data = $request->validate(['name' => ['required' , 'string' , 'min:3'] ,
                                    'percentage' =>['required' , 'integer' , 'min:1' , 'max:100'] ,
                                    'description' => ['string' ,'nullable', 'max:255']]);
        
        $discountPlan->update($data);
        return back()->with(['success' => 'you edited  Discount plan successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DiscountPlan $discountPlan)
    {
        $discountPlan->delete();
        return back()->with(['success' => 'you deleted Discount plan successfully']);
    }
}
