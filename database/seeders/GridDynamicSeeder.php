<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\GridDynamic;

class GridDynamicSeeder extends Seeder
{
    /*
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [];
        for ($y =0; $y < 3; $y++) {
            for ($x =0; $x < 4; $x++) {
                $data[] = [
                    'x_coordinate' => $x,
                    'y_coordinate' => $y,
                    'is_available' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        foreach ($data as $cell) {
            GridDynamic::updateOrCreate(
                [
                    'x_coordinate' => $cell['x_coordinate'],
                    'y_coordinate' => $cell['y_coordinate'],
                ],
                $cell
            );
        }
    }
}
