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
        if (!Schema::hasTable('medicine_pickups')) {
            Schema::create('medicine_pickups', function (Blueprint $table) {
                $table->id();
                $table->foreignId('patient_id')->constrained()->onDelete('cascade');
                $table->foreignId('medicine_id')->constrained()->onDelete('cascade');
                $table->integer('quantity');
                $table->date('scheduled_date');
                $table->date('pickup_date')->nullable();
                $table->enum('status', ['pending', 'picked_up'])->default('pending');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicine_pickups');
    }
};
