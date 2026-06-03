<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventgridCellSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('eventgrid_cells')->insert([
            ['event_id' => 1, 'grid_cell_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['event_id' => 1, 'grid_cell_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['event_id' => 2, 'grid_cell_id' => 3, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
