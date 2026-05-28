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
            'password' => Hash::make(env('ADMIN_PASSWORD'))
        ]);

        User::create([
            'name' => 'Test Admin',
            'role' => 'admin',
            'email' => 'noreply.metropolisb@gmail.com',
            'password' => Hash::make(env('ADMIN_PASSWORD'))
        ]);
    }
}
