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
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->comment('User who performed the maintenance')->constrained()->onDelete('set null');
            $table->string('type'); // e.g. 'Perawatan', 'Perbaikan', 'Penggantian'
            $table->string('title');
            $table->text('notes')->nullable();
            $table->decimal('cost', 15, 2)->nullable();
            $table->date('start_date');
            $table->date('completion_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenances');
    }
};
