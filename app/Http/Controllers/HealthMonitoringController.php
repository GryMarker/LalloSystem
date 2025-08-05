<?php

namespace App\Http\Controllers;

use App\Models\HealthMonitoring;
use App\Models\Patient;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HealthMonitoringController extends Controller
{
    public function index()
    {
        $monitorings = HealthMonitoring::with('patient')
            ->orderBy('recorded_date', 'desc')
            ->orderBy('recorded_time', 'desc')
            ->paginate(20);

        $patients = Patient::all();

        // Summary statistics
        $totalRecords = HealthMonitoring::count();
        $criticalRecords = HealthMonitoring::where('status', 'critical')->count();
        $warningRecords = HealthMonitoring::where('status', 'warning')->count();
        $normalRecords = HealthMonitoring::where('status', 'normal')->count();

        return view('staff.health-monitoring', compact(
            'monitorings', 
            'patients', 
            'totalRecords', 
            'criticalRecords', 
            'warningRecords', 
            'normalRecords'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'recorded_date' => 'required|date',
            'recorded_time' => 'required|date_format:H:i',
            'temperature' => 'nullable|numeric|between:30,45',
            'blood_pressure_systolic' => 'nullable|integer|between:70,250',
            'blood_pressure_diastolic' => 'nullable|integer|between:40,150',
            'pulse_rate' => 'nullable|integer|between:30,200',
            'respiratory_rate' => 'nullable|integer|between:8,40',
            'oxygen_saturation' => 'nullable|numeric|between:70,100',
            'weight' => 'nullable|numeric|between:20,300',
            'height' => 'nullable|numeric|between:50,250',
            'blood_sugar' => 'nullable|numeric|between:50,600',
            'pain_level' => 'nullable|integer|between:0,10',
            'pain_location' => 'nullable|string|max:255',
            'mood_score' => 'nullable|integer|between:1,10',
            'mood_notes' => 'nullable|string',
            'symptoms' => 'nullable|array',
            'symptoms.*' => 'string|max:255',
            'observations' => 'nullable|string',
            'notes' => 'nullable|string',
            'requires_follow_up' => 'boolean',
        ]);

        $monitoring = HealthMonitoring::create($request->all());

        // Calculate BMI if weight and height are provided
        if ($request->weight && $request->height) {
            $monitoring->calculateBmi();
        }

        // Assess status based on vital signs
        $monitoring->assessStatus();
        $monitoring->save();

        return redirect()->back()->with('success', 'Health monitoring record created successfully.');
    }

    public function show($id)
    {
        $monitoring = HealthMonitoring::with('patient')->findOrFail($id);
        
        // Get patient's recent records for comparison
        $recentRecords = $monitoring->patient->healthMonitorings()
            ->where('id', '!=', $id)
            ->orderBy('recorded_date', 'desc')
            ->orderBy('recorded_time', 'desc')
            ->limit(5)
            ->get();

        return view('staff.health-monitoring-detail', compact('monitoring', 'recentRecords'));
    }

    public function update(Request $request, $id)
    {
        $monitoring = HealthMonitoring::findOrFail($id);

        $request->validate([
            'temperature' => 'nullable|numeric|between:30,45',
            'blood_pressure_systolic' => 'nullable|integer|between:70,250',
            'blood_pressure_diastolic' => 'nullable|integer|between:40,150',
            'pulse_rate' => 'nullable|integer|between:30,200',
            'respiratory_rate' => 'nullable|integer|between:8,40',
            'oxygen_saturation' => 'nullable|numeric|between:70,100',
            'weight' => 'nullable|numeric|between:20,300',
            'height' => 'nullable|numeric|between:50,250',
            'blood_sugar' => 'nullable|numeric|between:50,600',
            'pain_level' => 'nullable|integer|between:0,10',
            'pain_location' => 'nullable|string|max:255',
            'mood_score' => 'nullable|integer|between:1,10',
            'mood_notes' => 'nullable|string',
            'symptoms' => 'nullable|array',
            'symptoms.*' => 'string|max:255',
            'observations' => 'nullable|string',
            'notes' => 'nullable|string',
            'requires_follow_up' => 'boolean',
        ]);

        $monitoring->update($request->all());

        // Recalculate BMI if weight or height changed
        if ($request->weight || $request->height) {
            $monitoring->calculateBmi();
        }

        // Reassess status
        $monitoring->assessStatus();
        $monitoring->save();

        return redirect()->back()->with('success', 'Health monitoring record updated successfully.');
    }

    public function destroy($id)
    {
        $monitoring = HealthMonitoring::findOrFail($id);
        $monitoring->delete();

        return redirect()->back()->with('success', 'Health monitoring record deleted successfully.');
    }

    public function getPatientHistory($patientId)
    {
        $patient = Patient::findOrFail($patientId);
        
        $monitorings = $patient->healthMonitorings()
            ->orderBy('recorded_date', 'desc')
            ->orderBy('recorded_time', 'desc')
            ->paginate(15);

        // Get trend data for charts
        $trendData = $this->getTrendData($patientId);

        return view('staff.patient-health-history', compact('patient', 'monitorings', 'trendData'));
    }

    public function getCriticalAlerts()
    {
        $criticalRecords = HealthMonitoring::with('patient')
            ->where('status', 'critical')
            ->orderBy('recorded_date', 'desc')
            ->orderBy('recorded_time', 'desc')
            ->get();

        return view('staff.critical-alerts', compact('criticalRecords'));
    }

    public function getTrendData($patientId)
    {
        $records = HealthMonitoring::where('patient_id', $patientId)
            ->orderBy('recorded_date', 'asc')
            ->get();

        $trendData = [
            'dates' => [],
            'temperature' => [],
            'blood_pressure_systolic' => [],
            'blood_pressure_diastolic' => [],
            'pulse_rate' => [],
            'weight' => [],
            'bmi' => [],
        ];

        foreach ($records as $record) {
            $trendData['dates'][] = $record->recorded_date->format('M d');
            $trendData['temperature'][] = $record->temperature;
            $trendData['blood_pressure_systolic'][] = $record->blood_pressure_systolic;
            $trendData['blood_pressure_diastolic'][] = $record->blood_pressure_diastolic;
            $trendData['pulse_rate'][] = $record->pulse_rate;
            $trendData['weight'][] = $record->weight;
            $trendData['bmi'][] = $record->bmi;
        }

        return $trendData;
    }

    public function getTodayRecords()
    {
        $today = now()->toDateString();
        
        $records = HealthMonitoring::with('patient')
            ->where('recorded_date', $today)
            ->orderBy('recorded_time', 'desc')
            ->get();

        return view('staff.today-health-records', compact('records'));
    }

    public function generateReport(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $patient = Patient::findOrFail($request->patient_id);
        
        $records = $patient->healthMonitorings()
            ->whereBetween('recorded_date', [$request->start_date, $request->end_date])
            ->orderBy('recorded_date', 'asc')
            ->get();

        // Calculate averages
        $averages = [
            'temperature' => $records->whereNotNull('temperature')->avg('temperature'),
            'blood_pressure_systolic' => $records->whereNotNull('blood_pressure_systolic')->avg('blood_pressure_systolic'),
            'blood_pressure_diastolic' => $records->whereNotNull('blood_pressure_diastolic')->avg('blood_pressure_diastolic'),
            'pulse_rate' => $records->whereNotNull('pulse_rate')->avg('pulse_rate'),
            'weight' => $records->whereNotNull('weight')->avg('weight'),
            'bmi' => $records->whereNotNull('bmi')->avg('bmi'),
        ];

        return view('staff.health-report', compact('patient', 'records', 'averages'));
    }
}
