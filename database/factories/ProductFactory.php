<?php

namespace Database\Factories;

use App\Enums\ProductType;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'description' => fake()->text(),
            'image_path' => fake()->imageUrl(),
            'price' => fake()->numberBetween(0, 1000),
            'type' => fake()->randomElement(ProductType::cases())->value,
        ];
    }
}
