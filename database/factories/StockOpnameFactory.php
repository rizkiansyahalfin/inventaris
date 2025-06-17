<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockOpnameFactory extends Factory
{
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-1 month', 'now');
        $endDate = fake()->dateTimeBetween($startDate, '+1 month');
        
        return [
            'name' => 'Stock Opname ' . fake()->date('Y-m'),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => fake()->randomElement(['pending', 'in_progress', 'completed']),
            'notes' => fake()->optional()->sentence(),
            'created_by' => User::factory()->petugas(),
        ];
    }
} 