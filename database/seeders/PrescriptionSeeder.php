<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Prescription;
use App\Models\DosageReminder;
use App\Models\Patient;
use App\Models\Medicine;
use Carbon\Carbon;

class PrescriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $patients = Patient::all();
        $medicines = Medicine::all();

        if ($patients->isEmpty() || $medicines->isEmpty()) {
            $this->command->info('No patients or medicines found. Please run PatientSeeder and MedicineSeeder first.');
            return;
        }

        // Create sample prescriptions
        $prescriptions = [
            [
                'patient_id' => $patients->first()->id,
                'medicine_id' => $medicines->first()->id,
                'dosage' => '1 tablet twice daily',
                'duration' => 7,
                'start_date' => now()->subDays(2),
            ],
            [
                'patient_id' => $patients->first()->id,
                'medicine_id' => $medicines->get(1)->id,
                'dosage' => '1 tablet once daily',
                'duration' => 14,
                'start_date' => now()->subDays(5),
            ],
            [
                'patient_id' => $patients->last()->id,
                'medicine_id' => $medicines->get(2)->id,
                'dosage' => '1 tablet three times daily',
                'duration' => 10,
                'start_date' => now()->subDays(1),
            ],
        ];

        foreach ($prescriptions as $prescriptionData) {
            $prescription = Prescription::create($prescriptionData);

            // Create dosage reminders for each prescription
            $this->createDosageReminders($prescription);
        }

        $this->command->info('Sample prescriptions and dosage reminders created successfully.');
    }

    private function createDosageReminders($prescription)
    {
        $frequency = match($prescription->dosage) {
            '1 tablet twice daily' => 'twice_daily',
            '1 tablet three times daily' => 'thrice_daily',
            default => 'daily'
        };

        $reminderTime = match($frequency) {
            'twice_daily' => '08:00',
            'thrice_daily' => '08:00',
            default => '09:00'
        };

        $endDate = Carbon::parse($prescription->start_date)->addDays($prescription->duration);

        DosageReminder::create([
            'patient_id' => $prescription->patient_id,
            'prescription_id' => $prescription->id,
            'medicine_id' => $prescription->medicine_id,
            'dosage_instruction' => $prescription->dosage,
            'reminder_time' => $reminderTime,
            'frequency' => $frequency,
            'start_date' => $prescription->start_date,
            'end_date' => $endDate,
            'is_active' => true,
            'send_sms' => false,
            'send_email' => false,
            'notes' => 'Sample reminder for testing',
        ]);
    }
}
