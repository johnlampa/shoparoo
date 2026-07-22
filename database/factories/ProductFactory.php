<?php

namespace Database\Factories;

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
    public function definition()
    {
        $price = fake()->randomFloat(2, 4.99, 299.99);
        $hasDiscount = fake()->boolean(60);

        return [
            'title' => fake()->words(4, true),
            'description' => '<p>' . fake()->paragraphs(3, true) . '</p>',
            'price' => $price,
            'compare_at_price' => $hasDiscount ? round($price * fake()->randomFloat(2, 1.1, 1.5), 2) : null,
            'quantity' => fake()->numberBetween(5, 200),
            'published' => true,
            'created_at' => now(),
            'updated_at' => now(),
            'created_by' => 1,
            'updated_by' => 1,
        ];
    }
}
