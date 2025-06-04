<?php

namespace Database\Factories;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition(): array
    {
        $conditions = ['baik', 'rusak ringan', 'rusak berat'];
        
        return [
            'name' => fake()->words(3, true),
            'code' => strtoupper(fake()->unique()->bothify('INV-####??')),
            'description' => fake()->paragraph(),
            'quantity' => fake()->numberBetween(0, 50),
            'condition' => fake()->randomElement($conditions),
            'location' => fake()->words(2, true),
            'purchase_price' => fake()->optional()->randomFloat(2, 10000, 10000000),
            'purchase_date' => fake()->optional()->dateTimeBetween('-2 years', 'now'),
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'updated_at' => function (array $attributes) {
                return fake()->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }

    /**
     * Indicate that the item is in good condition.
     */
    public function goodCondition(): static
    {
        return $this->state(fn (array $attributes) => [
            'condition' => 'baik',
        ]);
    }

    /**
     * Indicate that the item has low stock.
     */
    public function lowStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'quantity' => fake()->numberBetween(0, 4),
        ]);
    }
} 