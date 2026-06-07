<?php

namespace Database\Seeders;

use App\Models\Weekly;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WeeklySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Weekly::create([
            'recurring_id' => 1,
            'weekday' => 'friday'
        ]);

        Weekly::create([
            'recurring_id' => 1,
            'weekday' => 'saturday'
        ]);
    }
}
