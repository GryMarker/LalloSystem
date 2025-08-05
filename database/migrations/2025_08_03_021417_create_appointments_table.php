<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('staff_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('type', ['medicine_pickup', 'checkup', 'consultation', 'follow_up']);
            $table->dateTime('scheduled_at');
            $table->string('status')->default('pending'); // pending, confirmed, completed, cancelled, missed
            $table->text('notes')->nullable();
            $table->integer('duration')->default(30); // in minutes
            $table->string('location')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Dropped in patients migration for proper order
    }
};
