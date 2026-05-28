<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Test Planner',
            'role' => 'cityplanner',
            'email' => 'test@cityplanner.com',
            'password' => 'test'
        ]);

        User::create([
            'name' => 'Test Admin',
            'role' => 'admin',
            'email' => 'test@admin.com',
            'password' => 'test'
        ]);
    }
}
