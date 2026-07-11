<?php

namespace Database\Seeders;

use App\Models\Config;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['key' => 'lunch_start', 'value' => '12:00', 'title' => 'Lunch Time Start'],
            ['key' => 'lunch_end', 'value' => '14:30', 'title' => 'Lunch Time End'],
            ['key' => 'dinner_start', 'value' => '19:00', 'title' => 'Dinner Time Start'],
            ['key' => 'dinner_end', 'value' => '20:30', 'title' => 'Dinner Time Start' ],
            ['key' => 'reserve_time', 'value' => '12', 'title' => 'Reserve Time (per hour)'],
            ['key' => 'daily_sale_reserve_time', 'value' => '30', 'title' => 'Daily Sale Reserve Time open (per minute)'],
        ];

        foreach ($settings as $setting) {   
            Config::updateOrCreate(
                ['key' => $setting['key']],
                $setting 
            );
        }
    }
}
