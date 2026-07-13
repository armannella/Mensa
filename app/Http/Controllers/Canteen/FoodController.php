<?php

namespace App\Http\Controllers\Canteen;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Food;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FoodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $foods = Food::query()->with('category')->get();
        $categories = Category::all();
        return view('mensa.foods' , compact('foods' , 'categories'));
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
        $request->validate(['category_id' =>['required' , 'integer' , 'exists:categories,id'] ,
                            'name' =>['string', 'required' , 'max:255'] ,
                            'ingredients' => ['string' , 'required' , 'max:500'] , 
                            'image' => ['file' , 'required' , 'mimes:png,jpg' , 'max:5000']]);

        $category = Category::query()->find($request->category_id);
        if($request->hasFile('image')){

            $image = $request->file('image');
            $image_path = $image->store("foods",'public');
            $category->foods()->create(['name' => $request->name , 'ingredients' => $request->ingredients , 'image_path' => $image_path]);
            return back()->with(['success' => 'you added successfully the food']);
            
        }
        return back()->withErrors(['image' => 'there is problem in uploading the image try again']);

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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Food $food)
    {
        if ($food->image_path && Storage::disk('public')->exists($food->image_path )) {
                Storage::disk('public')->delete($food->image_path );
        }
        $food->delete();
        return back()->with('success' , 'you deleted the food successfully');
    }
}
