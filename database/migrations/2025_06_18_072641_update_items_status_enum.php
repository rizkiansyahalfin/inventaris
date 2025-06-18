<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            // Drop the existing enum column
            $table->dropColumn('status');
        });

        Schema::table('items', function (Blueprint $table) {
            // Recreate the status column with new enum values
            $table->enum('status', ['Tersedia', 'Dipinjam', 'Dalam Perbaikan', 'Rusak', 'Hilang'])
                ->default('Tersedia')
                ->after('condition');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            // Drop the new enum column
            $table->dropColumn('status');
        });

        Schema::table('items', function (Blueprint $table) {
            // Recreate the original enum column
            $table->enum('status', ['active', 'inactive'])->default('active')->after('condition');
        });
    }
};
