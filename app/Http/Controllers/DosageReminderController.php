<?php

namespace App\Http\Controllers;

use App\Models\DosageReminder;
use App\Models\DosageLog;
use App\Models\Patient;
use App\Models\Medicine;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DosageReminderController extends Controller
{
    public function index()
    {
        $reminders = DosageReminder::with(['patient', 'medicine', 'prescription'])
            ->orderBy('start_date', 'desc')
            ->get();

        $patients = Patient::all();
        $medicines = Medicine::all();
        $prescriptions = Prescription::all();

        return view('staff.dosage-reminders', compact('reminders', 'patients', 'medicines', 'prescriptions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'prescription_id' => 'required|exists:prescriptions,id',
            'medicine_id' => 'required|exists:medicines,id',
            'dosage_instruction' => 'required|string|max:255',
            'reminder_time' => 'required|date_format:H:i',
            'frequency' => 'required|in:daily,twice_daily,thrice_daily,weekly,custom',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'send_sms' => 'boolean',
            'send_email' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $reminder = DosageReminder::create($request->all());

        // Generate dosage logs for the reminder period
        $this->generateDosageLogs($reminder);

        return redirect()->back()->with('success', 'Dosage reminder created successfully.');
    }

    public function show($id)
    {
        $reminder = DosageReminder::with(['patient', 'medicine', 'prescription', 'dosageLogs'])
            ->findOrFail($id);

        $logs = $reminder->dosageLogs()
            ->orderBy('scheduled_date', 'desc')
            ->orderBy('scheduled_time', 'desc')
            ->paginate(20);

        return view('staff.dosage-reminder-detail', compact('reminder', 'logs'));
    }

    public function update(Request $request, $id)
    {
        $reminder = DosageReminder::findOrFail($id);

        $request->validate([
            'dosage_instruction' => 'required|string|max:255',
            'reminder_time' => 'required|date_format:H:i',
            'frequency' => 'required|in:daily,twice_daily,thrice_daily,weekly,custom',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
            'send_sms' => 'boolean',
            'send_email' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $reminder->update($request->all());

        // Regenerate dosage logs if dates changed
        if ($reminder->wasChanged(['start_date', 'end_date', 'frequency', 'reminder_time'])) {
            $reminder->dosageLogs()->delete();
            $this->generateDosageLogs($reminder);
        }

        return redirect()->back()->with('success', 'Dosage reminder updated successfully.');
    }

    public function destroy($id)
    {
        $reminder = DosageReminder::findOrFail($id);
        $reminder->dosageLogs()->delete();
        $reminder->delete();

        return redirect()->back()->with('success', 'Dosage reminder deleted successfully.');
    }

    public function markAsTaken($logId)
    {
        $log = DosageLog::findOrFail($logId);
        
        if ($log->status === 'pending') {
            $log->status = 'taken';
            $log->taken_at = now();
            $log->save();
        }

        return redirect()->back()->with('success', 'Medication marked as taken.');
    }

    public function markAsSkipped($logId)
    {
        $log = DosageLog::findOrFail($logId);
        
        if ($log->status === 'pending') {
            $log->status = 'skipped';
            $log->save();
        }

        return redirect()->back()->with('success', 'Medication marked as skipped.');
    }

    public function getTodayReminders()
    {
        $today = now()->toDateString();
        
        $logs = DosageLog::with(['patient', 'medicine', 'dosageReminder'])
            ->where('scheduled_date', $today)
            ->where('status', 'pending')
            ->orderBy('scheduled_time', 'asc')
            ->get();

        return view('staff.today-reminders', compact('logs'));
    }

    public function getPatientReminders($patientId)
    {
        $patient = Patient::findOrFail($patientId);
        
        $reminders = $patient->dosageReminders()
            ->with(['medicine', 'prescription'])
            ->where('is_active', true)
            ->get();

        $todayLogs = $patient->dosageLogs()
            ->where('scheduled_date', now()->toDateString())
            ->orderBy('scheduled_time', 'asc')
            ->get();

        return view('staff.patient-reminders', compact('patient', 'reminders', 'todayLogs'));
    }

    private function generateDosageLogs(DosageReminder $reminder)
    {
        $startDate = Carbon::parse($reminder->start_date);
        $endDate = Carbon::parse($reminder->end_date);
        $reminderTime = Carbon::parse($reminder->reminder_time);

        $currentDate = $startDate->copy();
        
        while ($currentDate <= $endDate) {
            $times = $this->getTimesForFrequency($reminder->frequency, $reminderTime);
            
            foreach ($times as $time) {
                DosageLog::create([
                    'dosage_reminder_id' => $reminder->id,
                    'patient_id' => $reminder->patient_id,
                    'medicine_id' => $reminder->medicine_id,
                    'scheduled_date' => $currentDate->toDateString(),
                    'scheduled_time' => $time,
                    'status' => 'pending',
                ]);
            }

            // Move to next day or week based on frequency
            if ($reminder->frequency === 'weekly') {
                $currentDate->addWeek();
            } else {
                $currentDate->addDay();
            }
        }
    }

    private function getTimesForFrequency($frequency, $baseTime)
    {
        $times = [];
        $baseHour = $baseTime->hour;
        $baseMinute = $baseTime->minute;

        switch ($frequency) {
            case 'daily':
                $times[] = $baseTime->format('H:i');
                break;
            case 'twice_daily':
                $times[] = $baseTime->format('H:i');
                $times[] = Carbon::createFromTime($baseHour + 12, $baseMinute)->format('H:i');
                break;
            case 'thrice_daily':
                $times[] = $baseTime->format('H:i');
                $times[] = Carbon::createFromTime($baseHour + 8, $baseMinute)->format('H:i');
                $times[] = Carbon::createFromTime($baseHour + 16, $baseMinute)->format('H:i');
                break;
            case 'weekly':
                $times[] = $baseTime->format('H:i');
                break;
            default:
                $times[] = $baseTime->format('H:i');
        }

        return $times;
    }
}
