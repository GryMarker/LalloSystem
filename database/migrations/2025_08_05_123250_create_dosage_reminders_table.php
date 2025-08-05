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
        Schema::create('dosage_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('prescription_id')->constrained()->onDelete('cascade');
            $table->foreignId('medicine_id')->constrained()->onDelete('cascade');
            $table->string('dosage_instruction'); // e.g., "1 tablet twice daily"
            $table->time('reminder_time'); // Time of day for reminder
            $table->enum('frequency', ['daily', 'twice_daily', 'thrice_daily', 'weekly', 'custom'])->default('daily');
            $table->json('custom_schedule')->nullable(); // For custom schedules
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(true);
            $table->boolean('send_sms')->default(false);
            $table->boolean('send_email')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Create dosage_logs table to track when medications were taken
        Schema::create('dosage_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dosage_reminder_id')->constrained()->onDelete('cascade');
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('medicine_id')->constrained()->onDelete('cascade');
            $table->date('scheduled_date');
            $table->time('scheduled_time');
            $table->dateTime('taken_at')->nullable();
            $table->enum('status', ['pending', 'taken', 'missed', 'skipped'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dosage_logs');
        Schema::dropIfExists('dosage_reminders');
    }
};
