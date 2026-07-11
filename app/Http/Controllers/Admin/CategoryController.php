<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        return view('admin.categories' , compact('categories'));
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
        $request->validate(['name' => ['required','string' , 'max:255'],
                            'price' => ['required', 'numeric', 'min:0', 'max:99.99', 'decimal:0,2']]);

        $category = Category::create(['name' => $request->name , 'price' => $request->price]);
        return back()->with(['success' => 'you added new category successfully']);
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
    public function update(Request $request, Category $category)
    {
        $request->validate(['name' => ['required','string' , 'max:255'],
                            'price' => ['required', 'numeric', 'min:0', 'max:99.99', 'decimal:0,2']]);

        $category->update(['name' => $request->name , 'price' => $request->price]);
        return back()->with(['success' => 'you added new category successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return back()->with(['success' => 'you deleted category successfully']);
    }
}
