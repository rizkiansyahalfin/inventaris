<?php

namespace Database\Factories;

use App\Models\Borrow;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BorrowFactory extends Factory
{
    protected $model = Borrow::class;

    public function definition(): array
    {
        $borrowDate = fake()->dateTimeBetween('-6 months', 'now');
        $dueDate = fake()->dateTimeBetween($borrowDate, '+2 months');
        
        return [
            'user_id' => User::factory(),
            'item_id' => Item::factory(),
            'quantity' => fake()->numberBetween(1, 5),
            'borrow_date' => $borrowDate,
            'due_date' => $dueDate,
            'return_date' => null,
            'status' => 'borrowed',
            'notes' => fake()->optional()->sentence(),
            'created_at' => $borrowDate,
            'updated_at' => $borrowDate,
        ];
    }

    /**
     * Indicate that the borrow has been returned.
     */
    public function returned(): static
    {
        return $this->state(function (array $attributes) {
            $returnDate = fake()->dateTimeBetween($attributes['borrow_date'], 'now');
            
            return [
                'return_date' => $returnDate,
                'status' => 'returned',
                'updated_at' => $returnDate,
            ];
        });
    }

    /**
     * Indicate that the borrow is overdue.
     */
    public function overdue(): static
    {
        return $this->state(function (array $attributes) {
            $borrowDate = fake()->dateTimeBetween('-3 months', '-2 months');
            $dueDate = fake()->dateTimeBetween('-1 month', '-1 week');
            
            return [
                'borrow_date' => $borrowDate,
                'due_date' => $dueDate,
                'created_at' => $borrowDate,
                'updated_at' => $borrowDate,
            ];
        });
    }
} 