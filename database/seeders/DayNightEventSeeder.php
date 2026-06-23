<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Event;
use App\Models\EventEffect;
use Illuminate\Database\Seeder;

class DayNightEventSeeder extends Seeder
{
    public function run(): void
    {
        // Delete existing Day/Night events if re-running
        Event::whereIn('name', ['Day', 'Night'])->delete();

        // Day event: starts as not global, will be toggled by the clock
        $dayEvent = Event::create([
            'name' => 'Day',
            'image_url' => 'images/day.png',
            'recurring_id' => null,
            'start_date' => now(),
            'time' => '06:00:00',
            'length' => 12,
            'length_unit' => 'hours',
            'dynamic' => false,
            'is_global' => true,
        ]);

        $categories = Category::all();
        foreach ($categories as $category) {
            EventEffect::create([
                'event_id' => $dayEvent->id,
                'category_name' => $category->category,
                'effect' => 1,
            ]);
        }

        // Night event: -1 to all effects, not global initially (will toggle)
        $nightEvent = Event::create([
            'name' => 'Night',
            'image_url' => 'images/night.png',
            'recurring_id' => null,
            'start_date' => now(),
            'time' => '18:00:00',
            'length' => 12,
            'length_unit' => 'hours',
            'dynamic' => false,
            'is_global' => false,
        ]);

        foreach ($categories as $category) {
            EventEffect::create([
                'event_id' => $nightEvent->id,
                'category_name' => $category->category,
                'effect' => -1,
            ]);
        }

        $this->command->info('Created Day and Night events');
    }
}