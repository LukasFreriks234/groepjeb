<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventGridCellSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('event_grid_cells')->insert([
            ['event_id' => 1, 'grid_dynamics_id' => 1, 'route_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['event_id' => 1, 'grid_dynamics_id' => 2, 'route_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['event_id' => 2, 'grid_dynamics_id' => 3, 'route_order' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
