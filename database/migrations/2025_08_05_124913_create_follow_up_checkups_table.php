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
        Schema::create('follow_up_checkups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('health_monitoring_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('appointment_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('staff_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Follow-up Details
            $table->string('reason'); // e.g., "High blood pressure", "Abnormal test results"
            $table->text('description')->nullable(); // Detailed description of why follow-up is needed
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('type', ['general', 'specialist', 'lab_test', 'imaging', 'medication_review'])->default('general');
            
            // Scheduling
            $table->date('scheduled_date');
            $table->time('scheduled_time');
            $table->integer('duration')->default(30); // in minutes
            $table->string('location')->nullable();
            
            // Status Tracking
            $table->enum('status', ['scheduled', 'confirmed', 'in_progress', 'completed', 'cancelled', 'no_show'])->default('scheduled');
            $table->dateTime('completed_at')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            
            // Follow-up Results
            $table->text('findings')->nullable(); // Clinical findings
            $table->text('recommendations')->nullable(); // Treatment recommendations
            $table->text('next_steps')->nullable(); // What happens next
            $table->boolean('requires_additional_follow_up')->default(false);
            $table->date('next_follow_up_date')->nullable();
            
            // Additional Information
            $table->text('notes')->nullable();
            $table->json('attachments')->nullable(); // For lab results, images, etc.
            $table->boolean('is_urgent')->default(false);
            $table->text('urgent_reason')->nullable();
            
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['patient_id', 'scheduled_date']);
            $table->index(['status', 'priority']);
            $table->index('is_urgent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('follow_up_checkups');
    }
};
