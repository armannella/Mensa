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

        // ۱. تنظیم مقادیر پایه در جدول configs همراه با ستون title
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
        // ۲. ساختن سلف، کتگوری (قیمت پایه ۱۰ یورو)، منو و غذا
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

        // ۳. اتصال غذا به منو از طریق جدول واسط با ظرفیت روزفروش ۲۰ عدد (۰ رزرو شده)
        $this->menu->foods()->attach($food->id, [
            'capacity' => 100,
            'reserved' => 80,
            'daily_sale_capacity' => 20,
            'daily_sale_reserved' => 0,
        ]);

        // لود کردن غذا از طریق منو تا پراپرتی details (Pivot) همراهش باشد
        $this->food = $this->menu->foods()->find($food->id);
    }

    /**
     * تست ۱: بررسی اینکه در لحظه شروع روزفروش (ثانیه صفر)، تخفیف دقیقاً صفر باشد
     */
    public function test_discount_is_zero_at_exact_start_of_flash_sale(): void
    {
        // ساعت شروع روزفروش: ۱۱:۰۰ صبح (۶۰ دقیقه قبل از ساعت ۱۲:۰۰)
        $startTime = Carbon::parse('2026-09-27 11:00:00');

        $quote = $this->pricingService->FlashSaleDynamicPrice($this->food, $this->menu, $startTime);

        $this->assertEquals(10.00, $quote['base_price']);
        $this->assertEquals(0.0, $quote['discount']);
        $this->assertEquals(10.00, $quote['price']);
    }

    /**
     * تست ۲: بررسی فرمول ریاضی در میانه زمان و با نصف موجودی
     */
    public function test_dynamic_price_calculates_accurately_based_on_time_and_stock(): void
    {
        // فرض می‌کنیم ۱۰ عدد از ۲۰ عدد فروش رفته است (Stock Ratio = 0.5)
        $this->menu->foods()->updateExistingPivot($this->food->id, [
            'daily_sale_reserved' => 10
        ]);
        $foodWithHalfStock = $this->menu->foods()->find($this->food->id);

        // کل بازه ۱۱:۰۰ تا ۱۴:۰۰ (۱۸۰ دقیقه) است. ساعت ۱۲:۳۰ دقیقاً وسط بازه است (Time Ratio = 0.5)
        $midTime = Carbon::parse('2026-09-27 12:30:00');

        $quote = $this->pricingService->FlashSaleDynamicPrice($foodWithHalfStock, $this->menu, $midTime);

        $this->assertEquals(20.00, $quote['discount']);
        $this->assertEquals(8.00, $quote['price']);
    }

    /**
     * تست ۳: بررسی مکانیزم قفل ۲ دقیقه‌ای قیمت در سشن و منقضی شدن آن پس از ۲ دقیقه (NFR Test)
     */
    public function test_session_price_lock_expires_after_ttl(): void
    {
        $this->pricingService->lockPricesInSession($this->menu, [
            $this->food->id => 7.50
        ]);

        $lockedPrice = $this->pricingService->getLockedPrice($this->menu, $this->food->id);
        $this->assertEquals(7.50, $lockedPrice);

        // سفر در زمان به ۳ دقیقه بعد برای تست انقضای TTL (۲ دقیقه‌ای)
        $this->travel(3)->minutes();

        $expiredPrice = $this->pricingService->getLockedPrice($this->menu, $this->food->id);
        $this->assertNull($expiredPrice);
    }
}