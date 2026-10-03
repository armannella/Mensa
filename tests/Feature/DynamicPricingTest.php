<?php

namespace Tests\Feature;

use App\Enums\MealEnum;
use App\Models\Canteen;
use App\Models\Category;
use App\Models\Config;
use App\Models\Food;
use App\Models\Menu;
use App\Models\User;
use App\Services\DynamicPricingService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DynamicPricingTest extends TestCase
{
    use RefreshDatabase;

    private DynamicPricingService $pricingService;
    private Menu $menu;
    private Food $food;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pricingService = new DynamicPricingService();

        $configs = [
            ['key' => 'lunch_start', 'value' => '12:00', 'title' => 'Lunch Start Time'],
            ['key' => 'lunch_end', 'value' => '14:00', 'title' => 'Lunch End Time'],
            ['key' => 'dinner_start', 'value' => '19:00', 'title' => 'Dinner Start Time'],
            ['key' => 'dinner_end', 'value' => '21:00', 'title' => 'Dinner End Time'],
            ['key' => 'reserve_time', 'value' => '2', 'title' => 'Normal Reservation Deadline (Hours)'],
            ['key' => 'daily_sale_reserve_time', 'value' => '60', 'title' => 'Daily Sale Start Before Meal (Minutes)'],
            ['key' => 'daily_sale_max_discount', 'value' => '50', 'title' => 'Maximum Flash Sale Discount (%)'],
        ];

        foreach ($configs as $item) {
            Config::updateOrCreate(
                ['key' => $item['key']],
                [
                    'value' => $item['value'],
                    'title' => $item['title'],
                ]
            );
        }
        $canteenUser = User::create([
            'name' => 'John Doe',
            'email' => 'student@unime.it',
            'username' => 'johndoe',
            'password' => bcrypt('password123'),
            'role' => 'mensa',
        ]);
        
        $canteen = Canteen::create([
            'name' => 'Central Mensa',
            'address' => 'University Street 1' ,
            'user_id' => $canteenUser->id, 
        ]);

        $category = Category::create([
            'name' => 'Primo',
            'price' => 10.00
        ]);

        $food = Food::create([
            'name' => 'Pasta Carbonara',
            'ingredients' => 'Pasta, Egg, Cheese',
            'category_id' => $category->id
        ]);

        $this->menu = Menu::create([
            'canteen_id' => $canteen->id,
            'date' => '2026-09-27',
            'meal' => MealEnum::LUNCH
        ]);
        $this->menu->foods()->attach($food->id, [
            'capacity' => 100,
            'reserved' => 80,
            'daily_sale_capacity' => 20,
            'daily_sale_reserved' => 0,
        ]);
        $this->food = $this->menu->foods()->find($food->id);
    }

    public function test_discount_is_zero_at_exact_start_of_flash_sale(): void
    {
        $startTime = Carbon::parse('2026-09-27 11:00:00');

        $quote = $this->pricingService->FlashSaleDynamicPrice($this->food, $this->menu, $startTime);

        $this->assertEquals(10.00, $quote['base_price']);
        $this->assertEquals(0.0, $quote['discount']);
        $this->assertEquals(10.00, $quote['price']);
    }

    
    public function test_dynamic_price_calculates_accurately_based_on_time_and_stock(): void
    {
        $this->menu->foods()->updateExistingPivot($this->food->id, [
            'daily_sale_reserved' => 10
        ]);
        $foodWithHalfStock = $this->menu->foods()->find($this->food->id);

        $midTime = Carbon::parse('2026-09-27 12:30:00');

        $quote = $this->pricingService->FlashSaleDynamicPrice($foodWithHalfStock, $this->menu, $midTime);

        $this->assertEquals(20.00, $quote['discount']);
        $this->assertEquals(8.00, $quote['price']);
    }

    public function test_session_price_lock_expires_after_ttl(): void
    {
        $this->pricingService->lockPricesInSession($this->menu, [
            $this->food->id => 7.50
        ]);

        $lockedPrice = $this->pricingService->getLockedPrice($this->menu, $this->food->id);
        $this->assertEquals(7.50, $lockedPrice);

        $this->travel(3)->minutes();

        $expiredPrice = $this->pricingService->getLockedPrice($this->menu, $this->food->id);
        $this->assertNull($expiredPrice);
    }
}