<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(), // Assumsi setiap product milik satu user
            'name' => fake()->word,
            'description' => fake()->sentence,
            'price' => fake()->randomFloat(2, 10, 1000), // harga antara 10 dan 1000
            'quantity' => fake()->numberBetween(1, 100),
        ];
    }
}