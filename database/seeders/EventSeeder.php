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
            'image_url' => 'images/PoliceStation.png',
            'type' => 'recurring',
            'recurrence_pattern' => 'weekly',
            'days_of_week' => ['Friday', 'Saturday'],
            'start_date' => now(),
            'end_date' => now()->addDays(7),
            'dynamic' => false,
        ]);

        Event::create([
            'name' => 'Food Truck Markt',
            'image_url' => 'images/PoliceStation.png',
            'type' => 'one-off',
            'recurrence_pattern' => null,
            'days_of_week' => null,
            'start_date' => now()->addDays(3),
            'end_date' => now()->addDays(3)->addHours(5),
            'dynamic' => false,
        ]);
    }
}
