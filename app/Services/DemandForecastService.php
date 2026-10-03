<?php

namespace App\Services;

use App\Enums\MealEnum;
use App\Models\Canteen;
use App\Models\Food;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DemandForecastService
{
    private Canteen $canteen;

    public function predictCapacity(Food $food, Carbon $date, MealEnum $mealEnum, ?Carbon $today = null): array
    {
        $this->canteen = Auth::user()->canteen;
        return $this->layerExactMatch($food, $date, $mealEnum, $today);
    }

    public function layerExactMatch(Food $food, Carbon $date, MealEnum $mealEnum, ?Carbon $today = null): array
    {
        $today = $today ?? now();
        $allMenusInMonth = $this->canteen->menus()
            ->where('meal', $mealEnum)
            ->whereBetween('date', [$today->copy()->subMonth(), $today])
            ->with(['foods' => function ($query) use ($food) {
                $query->where('food.id', $food->id);
            }])
            ->whereHas('foods', function ($query) use ($food) {
                $query->where('food.id', $food->id);
            })
            ->orderBy('date', 'desc')
            ->get();
            
        $menus = $allMenusInMonth->filter(function ($menu) use ($date) {
            return \Carbon\Carbon::parse($menu->date)->dayOfWeek === $date->dayOfWeek;
        })->values();
        
        if ($menus->count() < 2) {
            return $this->layerSameMeal($food, $date, $mealEnum, $today);
        }
        
        return $this->calculatePrediction($menus, $food->id, 'Based on Exact Match (Same Meal & Same Day) since last month');
    }

    public function layerSameMeal(Food $food, Carbon $date, MealEnum $mealEnum, ?Carbon $today = null): array
    {
        $today = $today ?? now();
        
        $menus = $this->canteen->menus()
            ->where('meal', $mealEnum)
            ->whereBetween('date', [$today->copy()->subMonth(), $today])
            ->with(['foods' => function ($query) use ($food) {
                $query->where('food.id', $food->id);
            }])
            ->whereHas('foods', function ($query) use ($food) {
                $query->where('food.id', $food->id);
            })
            ->orderBy('date', 'desc')
            ->get();
        
        if ($menus->count() < 2) {
            return $this->layerCategoryCheck($food, $date, $mealEnum, $today);
        }
        return $this->calculatePrediction($menus, $food->id, 'Based on Same Meal (All Days) since last month');
    }

    public function layerCategoryCheck(Food $food, Carbon $date, MealEnum $mealEnum, ?Carbon $today = null): array
    {
        $today = $today ?? now();
        
        $menus = $this->canteen->menus()
            ->where('meal', $mealEnum)
            ->whereBetween('date', [$today->copy()->subWeek(), $today])
            ->with(['foods' => function ($query) use ($food) {
                $query->where('category_id', $food->category_id);
            }])
            ->whereHas('foods', function ($query) use ($food) {
                $query->where('category_id', $food->category_id);
            })
            ->orderBy('date', 'desc')
            ->get();
        
        if ($menus->count() < 1) {
            
            return [
                'predicted_capacity' => 50, 
                'info' => 'Default capacity (No historical data found for this category)',
            ];
        }

        $capacities = [];
        foreach ($menus as $menu) {
            $sum = 0;
            $count = $menu->foods->count();
            
            if ($count === 0) continue;

            foreach ($menu->foods as $selectedFood) {
                $capacity = $selectedFood->details->reserved + $selectedFood->details->daily_sale_reserved;
                $capacity = $this->censoredDemand($selectedFood, $capacity);
                $sum += $capacity;
            }
            $capacities[] = round($sum / $count);
        }

        return [
            'predicted_capacity' => round($this->weightedMean($capacities)),
            'info' => 'Based on Category Average (Same Meal) since last week',
        ];
    }

    private function calculatePrediction($menus, int $foodId, string $info): array
    {
        $capacities = [];
        foreach ($menus as $menu) {
            $selectedFood = $menu->foods->firstWhere('id', $foodId);
            
            if ($selectedFood) {
                $capacity = $selectedFood->details->reserved + $selectedFood->details->daily_sale_reserved;
                $capacity = $this->censoredDemand($selectedFood, $capacity);
                $capacities[] = $capacity;
            }
        }

        return [
            'predicted_capacity' => round($this->weightedMean($capacities)),
            'info' => $info,
        ];
    }

    public function censoredDemand(Food $food, int $capacity): int
    {
        if ($food->details->capacity == $food->details->reserved) {
            $capacity = (int) round($capacity * 1.1);
        }
        return $capacity;
    }

    public function weightedMean(array $capacities): float
    {
        $count = count($capacities);
        if ($count === 0) return 0;

        $sum = 0;
        for ($i = $count; $i > 0; $i--) {
            $sum += $capacities[$count - $i] * $i;
        }
        $denominator = ($count * ($count + 1)) / 2;
        return $sum / $denominator;
    }
}