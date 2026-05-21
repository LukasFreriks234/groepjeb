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
            ['id' => '1', 'Veiligheid' => 5, 'Recreatie' => 1, 'Milieukwaliteit' => 0, 'Voorzieningen' => 1, 'Mobiliteit' => 2],
            ['id' => '2', 'Veiligheid' => 4, 'Recreatie' => 1, 'Milieukwaliteit' => 2, 'Voorzieningen' => 1, 'Mobiliteit' => 2],
            ['id' => '3', 'Veiligheid' => -2, 'Recreatie' => 5, 'Milieukwaliteit' => 4, 'Voorzieningen' => 0, 'Mobiliteit' => 0],
            ['id' => '4', 'Veiligheid' => -1, 'Recreatie' => 4, 'Milieukwaliteit' => 0, 'Voorzieningen' => 2, 'Mobiliteit' => 0],
            ['id' => '5', 'Veiligheid' => 0, 'Recreatie' => 5, 'Milieukwaliteit' => 2, 'Voorzieningen' => 3, 'Mobiliteit' => 0],
            ['id' => '6', 'Veiligheid' => 0, 'Recreatie' => 0, 'Milieukwaliteit' => 5, 'Voorzieningen' => 2, 'Mobiliteit' => 0],
            ['id' => '7', 'Veiligheid' => 2, 'Recreatie' => 2, 'Milieukwaliteit' => 0, 'Voorzieningen' => 5, 'Mobiliteit' => -3],
            ['id' => '8', 'Veiligheid' => 0, 'Recreatie' => 0, 'Milieukwaliteit' => -2, 'Voorzieningen' => 5, 'Mobiliteit' => 0],
            ['id' => '9', 'Veiligheid' => 3, 'Recreatie' => 0, 'Milieukwaliteit' => 0, 'Voorzieningen' => 5, 'Mobiliteit' => 0],
            ['id' => '10', 'Veiligheid' => -2, 'Recreatie' => 2, 'Milieukwaliteit' => 0, 'Voorzieningen' => 4, 'Mobiliteit' => 5],
            ['id' => '11', 'Veiligheid' => -4, 'Recreatie' => 2, 'Milieukwaliteit' => -4, 'Voorzieningen' => 3, 'Mobiliteit' => 5],
            ['id' => '12', 'Veiligheid' => 0, 'Recreatie' => 3, 'Milieukwaliteit' => 3, 'Voorzieningen' => 3, 'Mobiliteit' => 4],
            ['id' => '13', 'Veiligheid' => -2, 'Recreatie' => 0, 'Milieukwaliteit' => -4, 'Voorzieningen' => 1, 'Mobiliteit' => 4],

        ];

        foreach ($effects as $effect){
            EffectsModel::create($effect);
        }
    }
}
