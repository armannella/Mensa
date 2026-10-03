<?php

namespace Tests\Feature;

use App\Enums\MealEnum;
use App\Models\Canteen;
use App\Models\Category;
use App\Models\Food;
use App\Models\Menu;
use App\Models\User;
use App\Services\DemandForecastService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class DemandForecastTest extends TestCase
{
    use RefreshDatabase;
    
    private DemandForecastService $demand_forecast_service;

    #[\Override]
    public function setUp(): void
    {
        parent::setUp();
        $this->demand_forecast_service = new DemandForecastService();
    }

    #[DataProvider('weightedMeanDataProvider')]
    public function test_weighted_mean_works_correctly(array $capacities, float $expectedResult): void
    {
        $result = $this->demand_forecast_service->weightedMean($capacities);
        $this->assertEqualsWithDelta($expectedResult, $result, 0.1);
    } 

    public static function weightedMeanDataProvider(): array
    {
        return [
            'Scenario 1: Empty array should return 0' => [
                'capacities' => [],
                'expectedResult' => 0.0,
            ],
            'Scenario 2: Single element should return exactly that element' => [
                'capacities' => [150],
                'expectedResult' => 150.0,
            ],
            'Scenario 3: Three identical capacities should return the same capacity' => [
                'capacities' => [100, 100, 100],
                'expectedResult' => 100.0,
            ],
            'Scenario 4: Ascending demand' => [
                'capacities' => [50, 100, 150], 
                'expectedResult' => 83.3,
            ],
            'Scenario 5: descending demand' => [
                'capacities' => [300, 200, 100, 50], 
                'expectedResult' => 205.0,
            ],
        ];
    }

    private function prepareDatabaseAndLogin(): array
    {
        $canteen = Canteen::factory()->create(['name' => 'Central Mensa', 'address' => 'Campus']);
        
        $user = $canteen->user ;
        
        $this->actingAs($user);

        $category = Category::factory()->create(['name' => 'Main Dish', 'price' => 10.00]);
        $food = Food::factory()->create(['name' => 'Pasta Carbonara', 'category_id' => $category->id]);

        return [$canteen, $category, $food];
    }

    public function test_Layer1_uses_exact_match_when_enough_data_exists(): void
    {
        [$canteen, $category, $food] = $this->prepareDatabaseAndLogin();
        
        $targetDate = Carbon::parse('2026-09-28'); 
        $menu1 = $canteen->menus()->create(['date' => '2026-09-21', 'meal' => MealEnum::LUNCH]); // هفته پیش
        $menu1->foods()->attach($food->id, ['capacity' => 150, 'reserved' => 100, 'daily_sale_capacity' => 50, 'daily_sale_reserved' => 20]); // تقاضا: ۱۲۰

        $menu2 = $canteen->menus()->create(['date' => '2026-09-14', 'meal' => MealEnum::LUNCH]); // دو هفته پیش
        $menu2->foods()->attach($food->id, ['capacity' => 100, 'reserved' => 80, 'daily_sale_capacity' => 20, 'daily_sale_reserved' => 10]); // تقاضا: ۹۰

        $prediction = $this->demand_forecast_service->predictCapacity($food, $targetDate, MealEnum::LUNCH, $targetDate);

        $this->assertEquals(110, $prediction['predicted_capacity']);
        $this->assertStringContainsString('Exact Match', $prediction['info']);
    }

    public function test_layer2_uses_same_meal_fallback_when_exact_day_is_missing(): void
    {
        [$canteen, $category, $food] = $this->prepareDatabaseAndLogin();
        
        $targetDate = Carbon::parse('2026-10-12'); 

        
        $menu1 = $canteen->menus()->create(['date' => '2026-10-06', 'meal' => MealEnum::LUNCH]);
        $menu1->foods()->attach($food->id, ['capacity' => 100, 'reserved' => 70, 'daily_sale_capacity' => 30, 'daily_sale_reserved' => 10]);

        $menu2 = $canteen->menus()->create(['date' => '2026-09-30', 'meal' => MealEnum::LUNCH]);
        $menu2->foods()->attach($food->id, ['capacity' => 100, 'reserved' => 50, 'daily_sale_capacity' => 20, 'daily_sale_reserved' => 10]);

        $prediction = $this->demand_forecast_service->predictCapacity($food, $targetDate, MealEnum::LUNCH, $targetDate);

        $this->assertEquals(73, $prediction['capacity'] ?? $prediction['predicted_capacity']);
        $this->assertStringContainsString('Same Meal', $prediction['info']);
    }

    public function test_layer3_uses_category_average_when_no_history_for_specific_food(): void
    {
        [$canteen, $category, $newFood] = $this->prepareDatabaseAndLogin();
        
        $oldFood = Food::create(['name' => 'Lasagna', 'category_id' => $category->id]);

        $targetDate = Carbon::parse('2026-10-12'); 

        $menu1 = $canteen->menus()->create(['date' => '2026-10-08', 'meal' => MealEnum::LUNCH]); 
        $menu1->foods()->attach($oldFood->id, ['capacity' => 100, 'reserved' => 65, 'daily_sale_capacity' => 0, 'daily_sale_reserved' => 5]); // تقاضا: ۷۰

        $prediction = $this->demand_forecast_service->predictCapacity($newFood, $targetDate, MealEnum::LUNCH, $targetDate);

        $this->assertEquals(70, $prediction['capacity'] ?? $prediction['predicted_capacity']);
        $this->assertStringContainsString('Category Average', $prediction['info']);
    }

    public function test_not_enough_menu_returns_default_capacity(): void
    {
        [$canteen, $category, $food] = $this->prepareDatabaseAndLogin();
        
        $targetDate = Carbon::parse('2026-10-12'); 

        $prediction = $this->demand_forecast_service->predictCapacity($food, $targetDate, MealEnum::LUNCH, $targetDate);

  
        $this->assertEquals(50, $prediction['capacity'] ?? $prediction['predicted_capacity']);
        $this->assertStringContainsString('Default capacity', $prediction['info']);
    }
}