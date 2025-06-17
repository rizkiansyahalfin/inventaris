<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SystemConfigFactory extends Factory
{
    public function definition(): array
    {
        return [
            'key' => fake()->unique()->word(),
            'value' => fake()->word(),
            'description' => fake()->sentence(),
        ];
    }
} 