<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Functions;

class FunctionSeeder extends Seeder
{
    public function run(): void
    {
        $functions = [
            ['name' => 'Police Station', 'image' => 'images/PoliceStation.png', 'category' => 'Safety'],
            ['name' => 'Fire Station', 'image' => 'images/FireStation.webp', 'category' => 'Safety'],
            ['name' => 'Park', 'image' => 'images/Park.webp', 'category' => 'Recreation'],
            ['name' => 'Cinema', 'image' => 'images/Cinema.png', 'category' => 'Recreation'],
            ['name' => 'Sports Park', 'image' => 'images/SportsPark.png', 'category' => 'Recreation'],
            ['name' => 'Water Treatment Plant', 'image' => 'images/WaterTreatmentPlant.png', 'category' => 'Environmental Quality'],
            ['name' => 'School', 'image' => 'images/School.webp', 'category' => 'Services'],
            ['name' => 'Shop', 'image' => 'images/Shop.png', 'category' => 'Services'],
            ['name' => 'Hospital', 'image' => 'images/Hospital.png', 'category' => 'Services'],
            ['name' => 'Station', 'image' => 'images/Station.jpg', 'category' => 'Mobility'],
            ['name' => 'Road', 'image' => 'images/Road.jpg', 'category' => 'Mobility'],
            ['name' => 'Bicycle Path', 'image' => 'images/BicyclePath.png', 'category' => 'Mobility'],
            ['name' => 'Gas Station', 'image' => 'images/GasStation.png', 'category' => 'Mobility'],
        ];

        foreach ($functions as $function){
            Functions::create($function);
        }
    }
}
