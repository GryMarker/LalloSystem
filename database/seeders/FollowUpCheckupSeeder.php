<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FollowUpCheckup;
use App\Models\Patient;
use App\Models\HealthMonitoring;
use App\Models\User;
use Carbon\Carbon;

class FollowUpCheckupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $patients = Patient::all();
        $healthMonitorings = HealthMonitoring::all();
        $staff = User::all();

        if ($patients->isEmpty()) {
            $this->command->info('No patients found. Please run PatientSeeder first.');
            return;
        }

        // Sample follow-up checkups
        $followUps = [
            [
                'patient_id' => $patients->first()->id,
                'health_monitoring_id' => $healthMonitorings->first()->id ?? null,
                'staff_id' => $staff->first()->id ?? null,
                'reason' => 'High blood pressure monitoring',
                'description' => 'Patient has consistently elevated blood pressure readings requiring close monitoring and potential medication adjustment.',
                'priority' => 'high',
                'type' => 'general',
                'scheduled_date' => now()->addDays(3),
                'scheduled_time' => '10:00',
                'duration' => 45,
                'location' => 'Room 101',
                'status' => 'scheduled',
                'notes' => 'Patient should bring current medication list.',
            ],
            [
                'patient_id' => $patients->last()->id,
                'health_monitoring_id' => $healthMonitorings->last()->id ?? null,
                'staff_id' => $staff->first()->id ?? null,
                'reason' => 'Chest pain evaluation',
                'description' => 'Patient reported chest pain during last visit. Need to rule out cardiac issues.',
                'priority' => 'urgent',
                'type' => 'specialist',
                'scheduled_date' => now()->addDay(),
                'scheduled_time' => '14:30',
                'duration' => 60,
                'location' => 'Cardiology Clinic',
                'status' => 'scheduled',
                'is_urgent' => true,
                'urgent_reason' => 'Chest pain symptoms require immediate evaluation',
                'notes' => 'Patient should not eat 4 hours before appointment.',
            ],
            [
                'patient_id' => $patients->first()->id,
                'health_monitoring_id' => null,
                'staff_id' => $staff->first()->id ?? null,
                'reason' => 'Blood sugar control review',
                'description' => 'Regular follow-up to monitor diabetes management and adjust treatment plan.',
                'priority' => 'medium',
                'type' => 'medication_review',
                'scheduled_date' => now()->addDays(7),
                'scheduled_time' => '09:15',
                'duration' => 30,
                'location' => 'Room 102',
                'status' => 'scheduled',
                'notes' => 'Patient should bring blood sugar log.',
            ],
            [
                'patient_id' => $patients->last()->id,
                'health_monitoring_id' => null,
                'staff_id' => $staff->first()->id ?? null,
                'reason' => 'Routine lab work',
                'description' => 'Annual blood work to check cholesterol, kidney function, and liver enzymes.',
                'priority' => 'low',
                'type' => 'lab_test',
                'scheduled_date' => now()->addDays(5),
                'scheduled_time' => '08:00',
                'duration' => 20,
                'location' => 'Laboratory',
                'status' => 'scheduled',
                'notes' => 'Patient should fast for 12 hours before appointment.',
            ],
            [
                'patient_id' => $patients->first()->id,
                'health_monitoring_id' => null,
                'staff_id' => $staff->first()->id ?? null,
                'reason' => 'X-ray follow-up',
                'description' => 'Follow-up chest X-ray to monitor lung condition improvement.',
                'priority' => 'medium',
                'type' => 'imaging',
                'scheduled_date' => now()->addDays(10),
                'scheduled_time' => '11:00',
                'duration' => 30,
                'location' => 'Radiology Department',
                'status' => 'scheduled',
                'notes' => 'No special preparation required.',
            ],
            [
                'patient_id' => $patients->last()->id,
                'health_monitoring_id' => null,
                'staff_id' => $staff->first()->id ?? null,
                'reason' => 'Weight management consultation',
                'description' => 'Follow-up on weight loss progress and dietary recommendations.',
                'priority' => 'low',
                'type' => 'general',
                'scheduled_date' => now()->addDays(14),
                'scheduled_time' => '15:00',
                'duration' => 45,
                'location' => 'Room 103',
                'status' => 'scheduled',
                'notes' => 'Patient should bring food diary.',
            ],
            [
                'patient_id' => $patients->first()->id,
                'health_monitoring_id' => null,
                'staff_id' => $staff->first()->id ?? null,
                'reason' => 'Completed - Blood pressure normalized',
                'description' => 'Follow-up completed. Blood pressure has returned to normal range.',
                'priority' => 'medium',
                'type' => 'general',
                'scheduled_date' => now()->subDays(2),
                'scheduled_time' => '10:00',
                'duration' => 30,
                'location' => 'Room 101',
                'status' => 'completed',
                'completed_at' => now()->subDays(2)->setTime(10, 30),
                'findings' => 'Blood pressure: 120/80 mmHg. Patient reports feeling better.',
                'recommendations' => 'Continue current medication. Monitor blood pressure weekly.',
                'next_steps' => 'Schedule follow-up in 3 months.',
                'notes' => 'Patient compliance with medication has improved.',
            ],
            [
                'patient_id' => $patients->last()->id,
                'health_monitoring_id' => null,
                'staff_id' => $staff->first()->id ?? null,
                'reason' => 'Overdue - Missed appointment',
                'description' => 'Patient missed scheduled follow-up appointment.',
                'priority' => 'high',
                'type' => 'general',
                'scheduled_date' => now()->subDays(5),
                'scheduled_time' => '14:00',
                'duration' => 30,
                'location' => 'Room 102',
                'status' => 'no_show',
                'notes' => 'Patient called to reschedule. New appointment pending.',
            ],
        ];

        foreach ($followUps as $followUpData) {
            FollowUpCheckup::create($followUpData);
        }

        $this->command->info('Sample follow-up checkups created successfully.');
    }
}
