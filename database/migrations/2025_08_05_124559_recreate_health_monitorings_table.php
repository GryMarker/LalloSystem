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
        // Drop existing table if it exists
        Schema::dropIfExists('health_monitorings');

        // Create new table with correct structure
        Schema::create('health_monitorings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->date('recorded_date');
            $table->time('recorded_time');
            
            // Vital Signs
            $table->decimal('temperature', 4, 1)->nullable(); // in Celsius
            $table->integer('blood_pressure_systolic')->nullable(); // mmHg
            $table->integer('blood_pressure_diastolic')->nullable(); // mmHg
            $table->integer('pulse_rate')->nullable(); // beats per minute
            $table->integer('respiratory_rate')->nullable(); // breaths per minute
            $table->decimal('oxygen_saturation', 4, 1)->nullable(); // SpO2 %
            
            // Body Measurements
            $table->decimal('weight', 5, 2)->nullable(); // in kg
            $table->decimal('height', 5, 2)->nullable(); // in cm
            $table->decimal('bmi', 4, 2)->nullable(); // calculated BMI
            
            // Additional Health Metrics
            $table->decimal('blood_sugar', 5, 2)->nullable(); // mg/dL
            $table->integer('pain_level')->nullable(); // 0-10 scale
            $table->string('pain_location')->nullable();
            
            // Mental Health
            $table->integer('mood_score')->nullable(); // 1-10 scale
            $table->text('mood_notes')->nullable();
            
            // Symptoms and Observations
            $table->json('symptoms')->nullable(); // Array of symptoms
            $table->text('observations')->nullable();
            $table->text('notes')->nullable();
            
            // Status and Alerts
            $table->enum('status', ['normal', 'warning', 'critical'])->default('normal');
            $table->boolean('requires_follow_up')->default(false);
            $table->text('alert_notes')->nullable();
            
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['patient_id', 'recorded_date']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('health_monitorings');
    }
};
