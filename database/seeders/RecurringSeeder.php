<?php

namespace Database\Seeders;

use App\Models\Recurring;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RecurringSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Recurring::create([
            'end_date' => now()->addDays(7),
            'frequency' => 'weekly',
            'amount' => 1
        ]);
    }
}
