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
            'name' => 'festival',
            'image_url' => 'images/festival.png',
            'recurring_id' => 1,
            'time' => now(),
            'length' => 5,
            'length_unit' => 'hours',
            'dynamic' => false,
        ]);

        Event::create([
            'name' => 'Food Truck Market',
            'image_url' => 'images/foodtruck.jpg',
            'recurring_id' => null,
            'time' => now(),
            'length' => 2,
            'length_unit' => 'days',
            'dynamic' => false,
        ]);
    }
}
