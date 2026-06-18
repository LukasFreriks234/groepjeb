<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GridCell;
use App\Models\GridDynamic;

class GridDynamicSeeder extends Seeder
{
    public function run(): void
    {
        $gridCells = GridCell::all();

        foreach ($gridCells as $cell) {
            GridDynamic::updateOrCreate(
                ['grid_cell_id' => $cell->id],
                [
                    'x_coordinate' => $cell->x_coordinate,
                    'y_coordinate' => $cell->y_coordinate,
                    'is_available' => $cell->is_available,
                ]
            );
        }
    }
}