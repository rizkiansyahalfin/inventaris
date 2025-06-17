<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemRequestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'item_id' => Item::factory(),
            'name' => fake()->words(3, true),
            'description' => fake()->paragraph(),
            'reason' => fake()->paragraph(),
            'quantity' => fake()->numberBetween(1, 10),
            'status' => fake()->randomElement(['pending', 'approved', 'rejected']),
            'notes' => fake()->optional()->sentence(),
            'approved_by' => function (array $attributes) {
                return $attributes['status'] !== 'pending' ? User::factory()->admin() : null;
            },
            'approved_at' => function (array $attributes) {
                return $attributes['status'] !== 'pending' ? fake()->dateTimeBetween('-1 month', 'now') : null;
            },
        ];
    }
} 