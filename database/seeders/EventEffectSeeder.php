<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventEffectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $eventsData = [
            [
                'event_id' => 1, 
                'Safety' => -5, 
                'Recreation' => 10, 
                'Mobility' => -4,
                'Environmental Quality' => -3,
                'Services' => 5
            ],
            [
                'event_id' => 2, 
                'Safety' => 2, 
                'Services' => 5, 
                'Environmental Quality' => -2,
                'Recreation' => 3,
                'Mobility' => -1
            ]
        ];

        foreach ($eventsData as $data) {

            $eventId = $data['event_id'];

            foreach ($data as $key => $value) {

                if ($key === 'event_id') continue;

                DB::table('event_effects')->insert([
                    'event_id' => $eventId,
                    'category_name' => $key,
                    'effect' => (int) $value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
