<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insertOrIgnore(
            array_map(
                fn (string $category) => ['category' => $category],
                [
                    'Safety',
                    'Recreation',
                    'Environmental Quality',
                    'Services',
                    'Mobility',
                ]
            )
        );
    }
}
