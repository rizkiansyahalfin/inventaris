<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Borrow;
use App\Models\User;
use App\Models\Item;
use Carbon\Carbon;

class BorrowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::where('is_admin', false)->get();
        // Only select items that are available and not severely damaged
        $items = Item::where('status', 'Tersedia')
                     ->where('condition', '!=', 'Rusak Berat')
                     ->get();

        if ($users->isEmpty() || $items->isEmpty()) {
            $this->command->info('Tidak ada user atau barang yang tersedia untuk dibuatkan data peminjaman.');
            return;
        }

        for ($i = 0; $i < 15; $i++) {
            // Ensure we don't run out of items
            if ($items->isEmpty()) break;

            $item = $items->pop(); // Use pop to ensure item is not borrowed twice
            $user = $users->random();
            $borrowDate = Carbon::now()->subDays(rand(5, 60));
            $dueDate = $borrowDate->copy()->addDays(rand(7, 14));
            
            // Randomly decide if the item has been returned
            $isReturned = rand(0, 1);
            $status = 'Dipinjam';
            $returnDate = null;
            $conditionOnReturn = null;
            
            if ($isReturned) {
                $status = 'Tersedia'; // Item is available again
                $returnDate = $dueDate->copy()->subDays(rand(1, 3));
                $conditionOnReturn = $item->condition; // Assume condition is the same for simplicity
            }

            Borrow::create([
                'user_id' => $user->id,
                'item_id' => $item->id,
                'borrow_date' => $borrowDate,
                'due_date' => $dueDate,
                'return_date' => $returnDate,
                'status' => $isReturned ? 'Dikembalikan' : 'Dipinjam',
                'condition_at_borrow' => $item->condition,
                'condition_on_return' => $conditionOnReturn,
                'notes' => 'Catatan peminjaman dummy.',
            ]);

            // Update item status
            $item->update(['status' => $status]);
        }
    }
} 