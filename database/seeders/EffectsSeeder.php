<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Effects as EffectsModel;

class EffectsSeeder extends Seeder
{
    public function run(): void
    {
        $effects = [
            ['id' => '1', 'Safety' => 5, 'Recreation' => 1, 'Environmental Quality' => 0, 'Services' => 1, 'Mobility' => 2],
            ['id' => '2', 'Safety' => 4, 'Recreation' => 1, 'Environmental Quality' => 2, 'Services' => 1, 'Mobility' => 2],
            ['id' => '3', 'Safety' => -2, 'Recreation' => 5, 'Environmental Quality' => 4, 'Services' => 0, 'Mobility' => 0],
            ['id' => '4', 'Safety' => -1, 'Recreation' => 4, 'Environmental Quality' => 0, 'Services' => 2, 'Mobility' => 0],
            ['id' => '5', 'Safety' => 0, 'Recreation' => 5, 'Environmental Quality' => 2, 'Services' => 3, 'Mobility' => 0],
            ['id' => '6', 'Safety' => 0, 'Recreation' => 0, 'Environmental Quality' => 5, 'Services' => 2, 'Mobility' => 0],
            ['id' => '7', 'Safety' => 2, 'Recreation' => 2, 'Environmental Quality' => 0, 'Services' => 5, 'Mobility' => -3],
            ['id' => '8', 'Safety' => 0, 'Recreation' => 0, 'Environmental Quality' => -2, 'Services' => 5, 'Mobility' => 0],
            ['id' => '9', 'Safety' => 3, 'Recreation' => 0, 'Environmental Quality' => 0, 'Services' => 5, 'Mobility' => 0],
            ['id' => '10', 'Safety' => -2, 'Recreation' => 2, 'Environmental Quality' => 0, 'Services' => 4, 'Mobility' => 5],
            ['id' => '11', 'Safety' => -4, 'Recreation' => 2, 'Environmental Quality' => -4, 'Services' => 3, 'Mobility' => 5],
            ['id' => '12', 'Safety' => 0, 'Recreation' => 3, 'Environmental Quality' => 3, 'Services' => 3, 'Mobility' => 4],
            ['id' => '13', 'Safety' => -2, 'Recreation' => 0, 'Environmental Quality' => -4, 'Services' => 1, 'Mobility' => 4],

        ];

        foreach ($effects as $effect) {
            EffectsModel::updateOrCreate(
                ['id' => $effect['id']],
                $effect
            );
        }
    }
}
