<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            // Add the status column if it doesn't exist
            if (!Schema::hasColumn('items', 'status')) {
                $table->enum('status', ['Tersedia', 'Dipinjam', 'Dalam Perbaikan', 'Rusak', 'Hilang'])
                    ->default('Tersedia')
                    ->after('condition');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (Schema::hasColumn('items', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
