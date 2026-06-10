<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Event;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        Event::create([
            'name' => 'Zomerfestival',
            'image_url' => 'festival.jpg',
            'recurring_id' => 1,
            'start_date' => now(),
            'time' => now(),
            'length' => 5,
            'length_unit' => 'hours',
            'dynamic' => false,
        ]);

        Event::create([
            'name' => 'Food Truck Markt',
            'image_url' => 'foodmarket.jpg',
            'recurring_id' => null,
            'start_date' => now()->addDays(3),
            'time' => now(),
            'length' => 2,
            'length_unit' => 'days',
            'dynamic' => false,
        ]);
    }
}
