<?php

namespace Database\Factories;

use App\Models\categories;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<categories>
 */
class CategoriesFactory extends Factory
{
    protected $model = categories::class;

    public const CATEGORIES = [
        'Safety',
        'Recreation',
        'Environmental Quality',
        'Services',
        'Mobility',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category' => $this->faker->randomElement(self::CATEGORIES),
        ];
    }
}
