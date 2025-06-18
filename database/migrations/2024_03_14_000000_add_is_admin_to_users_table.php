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
        Schema::table('users', function (Blueprint $table) {
            // Drop is_admin column if it exists
            if (Schema::hasColumn('users', 'is_admin')) {
                $table->dropColumn('is_admin');
            }
            
            // Add role column if it doesn't exist
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['admin', 'petugas', 'user'])->default('user')->after('password');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop role column if it exists
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
            
            // Add back is_admin column
            if (!Schema::hasColumn('users', 'is_admin')) {
                $table->boolean('is_admin')->default(false)->after('password');
            }
        });
    }
}; 