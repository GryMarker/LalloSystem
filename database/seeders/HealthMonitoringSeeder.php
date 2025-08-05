<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\HealthMonitoring;
use App\Models\Patient;
use Carbon\Carbon;

class HealthMonitoringSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $patients = Patient::all();

        if ($patients->isEmpty()) {
            $this->command->info('No patients found. Please run PatientSeeder first.');
            return;
        }

        // Sample health monitoring records
        $records = [
            [
                'patient_id' => $patients->first()->id,
                'recorded_date' => now()->subDays(2),
                'recorded_time' => '09:30',
                'temperature' => 36.8,
                'blood_pressure_systolic' => 120,
                'blood_pressure_diastolic' => 80,
                'pulse_rate' => 72,
                'respiratory_rate' => 16,
                'oxygen_saturation' => 98.5,
                'weight' => 70.5,
                'height' => 170.0,
                'blood_sugar' => 95.0,
                'pain_level' => 2,
                'pain_location' => 'Lower back',
                'mood_score' => 7,
                'mood_notes' => 'Feeling better today',
                'symptoms' => ['Fatigue', 'Lower back pain'],
                'observations' => 'Patient reports improved sleep quality. Lower back pain is manageable.',
                'notes' => 'Continue with current treatment plan.',
                'requires_follow_up' => false,
            ],
            [
                'patient_id' => $patients->first()->id,
                'recorded_date' => now()->subDays(1),
                'recorded_time' => '14:15',
                'temperature' => 37.2,
                'blood_pressure_systolic' => 135,
                'blood_pressure_diastolic' => 85,
                'pulse_rate' => 78,
                'respiratory_rate' => 18,
                'oxygen_saturation' => 97.0,
                'weight' => 70.2,
                'height' => 170.0,
                'blood_sugar' => 102.0,
                'pain_level' => 1,
                'pain_location' => 'Lower back',
                'mood_score' => 8,
                'mood_notes' => 'Very positive mood',
                'symptoms' => ['Fatigue'],
                'observations' => 'Significant improvement in pain levels. Patient is more active.',
                'notes' => 'Consider reducing pain medication dosage.',
                'requires_follow_up' => false,
            ],
            [
                'patient_id' => $patients->last()->id,
                'recorded_date' => now()->subDays(3),
                'recorded_time' => '10:45',
                'temperature' => 38.5,
                'blood_pressure_systolic' => 145,
                'blood_pressure_diastolic' => 95,
                'pulse_rate' => 95,
                'respiratory_rate' => 22,
                'oxygen_saturation' => 94.0,
                'weight' => 65.0,
                'height' => 165.0,
                'blood_sugar' => 110.0,
                'pain_level' => 6,
                'pain_location' => 'Chest',
                'mood_score' => 4,
                'mood_notes' => 'Feeling anxious about symptoms',
                'symptoms' => ['Fever', 'Cough', 'Chest Pain', 'Fatigue'],
                'observations' => 'Patient presents with fever and chest discomfort. Respiratory rate elevated.',
                'notes' => 'Monitor closely. Consider chest X-ray if symptoms persist.',
                'requires_follow_up' => true,
            ],
            [
                'patient_id' => $patients->last()->id,
                'recorded_date' => now()->subDays(1),
                'recorded_time' => '16:20',
                'temperature' => 37.8,
                'blood_pressure_systolic' => 140,
                'blood_pressure_diastolic' => 90,
                'pulse_rate' => 88,
                'respiratory_rate' => 20,
                'oxygen_saturation' => 95.5,
                'weight' => 64.8,
                'height' => 165.0,
                'blood_sugar' => 105.0,
                'pain_level' => 4,
                'pain_location' => 'Chest',
                'mood_score' => 6,
                'mood_notes' => 'Slightly improved mood',
                'symptoms' => ['Cough', 'Fatigue'],
                'observations' => 'Fever has reduced. Chest pain is less severe. Patient is responding to treatment.',
                'notes' => 'Continue current medication. Schedule follow-up in 3 days.',
                'requires_follow_up' => true,
            ],
            [
                'patient_id' => $patients->first()->id,
                'recorded_date' => now(),
                'recorded_time' => '08:00',
                'temperature' => 36.5,
                'blood_pressure_systolic' => 118,
                'blood_pressure_diastolic' => 78,
                'pulse_rate' => 70,
                'respiratory_rate' => 15,
                'oxygen_saturation' => 99.0,
                'weight' => 70.0,
                'height' => 170.0,
                'blood_sugar' => 92.0,
                'pain_level' => 0,
                'pain_location' => null,
                'mood_score' => 9,
                'mood_notes' => 'Excellent mood, feeling great',
                'symptoms' => [],
                'observations' => 'Patient is in excellent health. All vital signs are normal.',
                'notes' => 'Patient has made full recovery. No follow-up required.',
                'requires_follow_up' => false,
            ],
        ];

        foreach ($records as $recordData) {
            $monitoring = HealthMonitoring::create($recordData);

            // Calculate BMI
            if ($monitoring->weight && $monitoring->height) {
                $monitoring->calculateBmi();
            }

            // Assess status
            $monitoring->assessStatus();
            $monitoring->save();
        }

        $this->command->info('Sample health monitoring records created successfully.');
    }
}
