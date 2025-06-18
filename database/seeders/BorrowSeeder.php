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
        $santri = User::where('role', 'user')->get();
        $staff = User::whereIn('role', ['admin', 'petugas'])->get();
        
        // Only select items that are available and not severely damaged
        $items = Item::where('status', 'Tersedia')
                     ->where('condition', '!=', 'Rusak Berat')
                     ->where('stock', '>', 0)
                     ->get();

        if ($santri->isEmpty() || $items->isEmpty()) {
            $this->command->info('Tidak ada santri atau barang yang tersedia untuk dibuatkan data peminjaman.');
            return;
        }

        // Create realistic borrowing scenarios
        $this->createKitabBorrows($santri, $items);
        $this->createAtkBorrows($santri, $items);
        $this->createElektronikBorrows($santri, $staff, $items);
        $this->createOlahragaBorrows($santri, $items);
        $this->createDapurBorrows($staff, $items);
    }

    private function createKitabBorrows($santri, $items)
    {
        $kitabItems = $items->where('category.code', 'KTB');
        
        foreach ($kitabItems as $item) {
            // Multiple santri can borrow the same kitab
            $borrowCount = min(rand(3, 8), $item->stock);
            
            for ($i = 0; $i < $borrowCount; $i++) {
                $user = $santri->random();
                $borrowDate = Carbon::now()->subDays(rand(1, 30));
                $dueDate = $borrowDate->copy()->addDays(rand(7, 14));
                
                // 80% chance of being returned
                $isReturned = rand(1, 100) <= 80;
                $status = $isReturned ? 'returned' : 'borrowed';
                $returnDate = $isReturned ? $dueDate->copy()->subDays(rand(0, 3)) : null;
                
                $this->createBorrowRecord($user, $item, $borrowDate, $dueDate, $returnDate, $status, 'Peminjaman kitab untuk belajar');
            }
        }
    }

    private function createAtkBorrows($santri, $items)
    {
        $atkItems = $items->where('category.code', 'ATK');
        
        foreach ($atkItems as $item) {
            $borrowCount = min(rand(5, 15), $item->stock);
            
            for ($i = 0; $i < $borrowCount; $i++) {
                $user = $santri->random();
                $borrowDate = Carbon::now()->subDays(rand(1, 60));
                $dueDate = $borrowDate->copy()->addDays(rand(1, 7));
                
                // 90% chance of being returned
                $isReturned = rand(1, 100) <= 90;
                $status = $isReturned ? 'returned' : 'borrowed';
                $returnDate = $isReturned ? $dueDate->copy()->subDays(rand(0, 2)) : null;
                
                $this->createBorrowRecord($user, $item, $borrowDate, $dueDate, $returnDate, $status, 'Peminjaman alat tulis untuk tugas');
            }
        }
    }

    private function createElektronikBorrows($santri, $staff, $items)
    {
        $elektronikItems = $items->where('category.code', 'ELK');
        
        foreach ($elektronikItems as $item) {
            $borrowCount = min(rand(1, 3), $item->stock);
            
            for ($i = 0; $i < $borrowCount; $i++) {
                // Staff more likely to borrow electronics
                $user = rand(1, 100) <= 70 ? $staff->random() : $santri->random();
                $borrowDate = Carbon::now()->subDays(rand(1, 90));
                $dueDate = $borrowDate->copy()->addDays(rand(1, 3));
                
                // 95% chance of being returned
                $isReturned = rand(1, 100) <= 95;
                $status = $isReturned ? 'returned' : 'borrowed';
                $returnDate = $isReturned ? $dueDate->copy()->subDays(rand(0, 1)) : null;
                
                $this->createBorrowRecord($user, $item, $borrowDate, $dueDate, $returnDate, $status, 'Peminjaman peralatan elektronik');
            }
        }
    }

    private function createOlahragaBorrows($santri, $items)
    {
        $olahragaItems = $items->where('category.code', 'OLG');
        
        foreach ($olahragaItems as $item) {
            $borrowCount = min(rand(2, 5), $item->stock);
            
            for ($i = 0; $i < $borrowCount; $i++) {
                $user = $santri->random();
                $borrowDate = Carbon::now()->subDays(rand(1, 45));
                $dueDate = $borrowDate->copy()->addDays(rand(1, 7));
                
                // 85% chance of being returned
                $isReturned = rand(1, 100) <= 85;
                $status = $isReturned ? 'returned' : 'borrowed';
                $returnDate = $isReturned ? $dueDate->copy()->subDays(rand(0, 2)) : null;
                
                $this->createBorrowRecord($user, $item, $borrowDate, $dueDate, $returnDate, $status, 'Peminjaman peralatan olahraga');
            }
        }
    }

    private function createDapurBorrows($staff, $items)
    {
        $dapurItems = $items->where('category.code', 'DKP');
        
        foreach ($dapurItems as $item) {
            $borrowCount = min(rand(1, 2), $item->stock);
            
            for ($i = 0; $i < $borrowCount; $i++) {
                $user = $staff->random();
                $borrowDate = Carbon::now()->subDays(rand(1, 120));
                $dueDate = $borrowDate->copy()->addDays(rand(1, 30));
                
                // 98% chance of being returned
                $isReturned = rand(1, 100) <= 98;
                $status = $isReturned ? 'returned' : 'borrowed';
                $returnDate = $isReturned ? $dueDate->copy()->subDays(rand(0, 5)) : null;
                
                $this->createBorrowRecord($user, $item, $borrowDate, $dueDate, $returnDate, $status, 'Peminjaman peralatan dapur');
            }
        }
    }

    private function createBorrowRecord($user, $item, $borrowDate, $dueDate, $returnDate, $status, $notes)
    {
        // Check if item is still available
        if ($item->stock <= 0) return;

        $quantity = min(rand(1, 3), $item->stock);
        
        Borrow::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'quantity' => $quantity,
            'borrow_date' => $borrowDate,
            'due_date' => $dueDate,
            'return_date' => $returnDate,
            'status' => $status,
            'condition_at_borrow' => $item->condition,
            'condition_on_return' => $status === 'returned' ? $item->condition : null,
            'notes' => $notes,
            'approved_by' => $status !== 'pending' ? User::where('role', 'admin')->first()->id : null,
            'approved_at' => $status !== 'pending' ? $borrowDate : null,
        ]);

        // Update item stock
        if ($status === 'borrowed') {
            $item->decrement('stock', $quantity);
        }
    }
} 