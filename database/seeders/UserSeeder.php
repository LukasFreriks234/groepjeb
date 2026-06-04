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
        User::updateOrCreate([
            'email' => 'test@cityplanner.com',
        ], [
            'name' => 'Test Planner',
            'role' => 'cityplanner',
            'email' => 'test@cityplanner.com',
            'password' => 'test'
        ]);

        User::updateOrCreate([
            'email' => 'test@admin.com',
        ], [
            'name' => 'Test Admin',
            'role' => 'admin',
            'email' => 'test@admin.com',
            'password' => 'test'
        ]);
    }
}
