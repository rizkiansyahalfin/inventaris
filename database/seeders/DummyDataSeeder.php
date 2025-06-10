<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use App\Models\Borrow;
use Illuminate\Database\Seeder;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create regular users
        $users = User::factory()
            ->count(10)
            ->create();

        // Create categories
        $categories = Category::factory()
            ->count(8)
            ->create();

        // Create items and attach random categories
        $items = Item::factory()
            ->count(30)
            ->create()
            ->each(function ($item) use ($categories) {
                $item->categories()->attach(
                    $categories->random(rand(1, 3))->pluck('id')->toArray()
                );
            });

        // Create active borrows
        Borrow::factory()
            ->count(15)
            ->state(function () use ($users, $items) {
                return [
                    'user_id' => $users->random()->id,
                    'item_id' => $items->random()->id,
                ];
            })
            ->create();

        // Create returned borrows
        Borrow::factory()
            ->count(20)
            ->state(function () use ($users, $items) {
                return [
                    'user_id' => $users->random()->id,
                    'item_id' => $items->random()->id,
                ];
            })
            ->returned()
            ->create();

        // Create overdue borrows
        Borrow::factory()
            ->count(5)
            ->state(function () use ($users, $items) {
                return [
                    'user_id' => $users->random()->id,
                    'item_id' => $items->random()->id,
                ];
            })
            ->overdue()
            ->create();
    }
} 