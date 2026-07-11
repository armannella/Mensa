<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Config;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    public function index()
    {
        $configs = Config::all()->keyBy('key');
        return view('admin.configs', compact('configs'));
    }

    public function updateAll(Request $request)
    {
        $validated = $request->validate([
            'lunch_start' => ['required', 'date_format:H:i'],
            'lunch_end'   => ['required', 'date_format:H:i', 'after:lunch_start'],
            
            'dinner_start' => ['required', 'date_format:H:i'],
            'dinner_end'   => ['required', 'date_format:H:i', 'after:dinner_start'],
            
            'reserve_time' => ['required', 'integer', 'min:1', 'max:168'],
            'daily_sale_reserve_time' => ['required', 'integer', 'min:1', 'max:168'],

        ]);

        
        foreach ($validated as $key => $value) {
            Config::where('key', $key)->update(['value' => $value]);
        }
        
        return back()->with('success',"You Updated Configs !!");
    }
}