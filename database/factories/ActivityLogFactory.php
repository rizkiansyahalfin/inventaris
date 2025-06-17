<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityLogFactory extends Factory
{
    public function definition(): array
    {
        $action = fake()->randomElement(['create', 'update', 'delete', 'login', 'logout']);
        
        // Tentukan module berdasarkan action
        $module = match($action) {
            'login', 'logout' => 'auth',
            'create', 'update', 'delete' => fake()->randomElement(['items', 'categories', 'borrows', 'users']),
            default => 'system'
        };

        return [
            'user_id' => User::factory(),
            'action' => $action,
            'module' => $module,
            'description' => fake()->sentence(),
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
        ];
    }
} 