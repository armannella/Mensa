<?php

namespace App\Services;

use App\Models\Config;
use App\Models\Food;
use App\Models\Menu;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

class DynamicPricingService
{
    private const TTL_MINUTES = 2;
    private const TIME_WEIGHT = 0.6;
    private const STOCK_WEIGHT = 0.4;

    public function FlashSaleDynamicPrice(Food $food, Menu $menu, ?Carbon $currentTime = null): array
    {
        $configs = Config::getAll();
        $max_discount = (float) ($configs['daily_sale_max_discount'] ?? 50);
        $basePrice = (float) $food->category->price;

        $dailySaleStartTime = $menu->getDailySaleStartTime();
        $dailySaleEndTime = $menu->getDailySaleEndTime();
        $current = $currentTime ?? now();

        $timeElapsed = $dailySaleStartTime->diffInSeconds($current, false);
        $totalTime = max(1, $dailySaleStartTime->diffInSeconds($dailySaleEndTime, false));

        if (env('APP_DEMO_MODE', false) && ($timeElapsed < 0 || $timeElapsed > $totalTime)) {
            $timeRatio = 0.5;
        } else {
            $timeRatio = min(1.0, max(0.0, $timeElapsed / $totalTime));
        }

        $unsoldItems = max(0, $food->details->daily_sale_capacity - $food->details->daily_sale_reserved);
        $totalItems = max(1, $food->details->daily_sale_capacity);
        $stockRatio = min(1.0, max(0.0, $unsoldItems / $totalItems));

        $t = self::TIME_WEIGHT * $timeRatio;
        $s = self::STOCK_WEIGHT * $stockRatio * $timeRatio;

        $final_discount = round(($t + $s) * $max_discount, 2);
        $final_price = round(($basePrice * (100 - $final_discount)) / 100, 2);

        return [
            'base_price' => round($basePrice, 2),
            'price' => $final_price,
            'discount' => $final_discount,
        ];
    }

    public function lockPricesInSession(Menu $menu, array $pricesByFoodId): void
    {
        Session::put("flash_quote_menu_{$menu->id}", [
            'expires_at' => now()->addMinutes(self::TTL_MINUTES)->timestamp,
            'prices' => $pricesByFoodId,
        ]);
    }

    public function getLockedPrice(Menu $menu, int $foodId): ?float
    {
        $quote = Session::get("flash_quote_menu_{$menu->id}");

        if (!$quote || now()->timestamp > $quote['expires_at']) {
            return null;
        }

        return isset($quote['prices'][$foodId]) ? (float) $quote['prices'][$foodId] : null;
    }

    public function clearPriceLock(Menu $menu): void
    {
        Session::forget("flash_quote_menu_{$menu->id}");
    }
}