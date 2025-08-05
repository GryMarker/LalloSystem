<?php

namespace App\Http\Controllers;

use App\Models\FollowUpCheckup;
use App\Models\Patient;
use App\Models\HealthMonitoring;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FollowUpCheckupController extends Controller
{
    public function index()
    {
        $followUps = FollowUpCheckup::with(['patient', 'staff', 'healthMonitoring'])
            ->orderBy('scheduled_date', 'asc')
            ->orderBy('scheduled_time', 'asc')
            ->paginate(20);

        $patients = Patient::all();
        $staff = User::all();
        $healthMonitorings = HealthMonitoring::where('requires_follow_up', true)->get();

        // Summary statistics
        $totalFollowUps = FollowUpCheckup::count();
        $urgentFollowUps = FollowUpCheckup::urgent()->count();
        $overdueFollowUps = FollowUpCheckup::overdue()->count();
        $todayFollowUps = FollowUpCheckup::today()->count();

        return view('staff.follow-up-checkups', compact(
            'followUps', 
            'patients', 
            'staff', 
            'healthMonitorings',
            'totalFollowUps',
            'urgentFollowUps',
            'overdueFollowUps',
            'todayFollowUps'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'health_monitoring_id' => 'nullable|exists:health_monitorings,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'staff_id' => 'nullable|exists:users,id',
            'reason' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'type' => 'required|in:general,specialist,lab_test,imaging,medication_review',
            'scheduled_date' => 'required|date|after_or_equal:today',
            'scheduled_time' => 'required|date_format:H:i',
            'duration' => 'nullable|integer|min:15|max:240',
            'location' => 'nullable|string|max:255',
            'is_urgent' => 'boolean',
            'urgent_reason' => 'nullable|string|required_if:is_urgent,1',
            'notes' => 'nullable|string',
        ]);

        $followUp = FollowUpCheckup::create($request->all());

        // If linked to health monitoring, update its follow-up status
        if ($request->health_monitoring_id) {
            $healthMonitoring = HealthMonitoring::find($request->health_monitoring_id);
            if ($healthMonitoring) {
                $healthMonitoring->requires_follow_up = false;
                $healthMonitoring->save();
            }
        }

        return redirect()->back()->with('success', 'Follow-up checkup scheduled successfully.');
    }

    public function show($id)
    {
        $followUp = FollowUpCheckup::with(['patient', 'staff', 'healthMonitoring', 'appointment'])
            ->findOrFail($id);

        // Get patient's recent health records for context
        $recentHealthRecords = $followUp->patient->healthMonitorings()
            ->orderBy('recorded_date', 'desc')
            ->limit(5)
            ->get();

        // Get patient's previous follow-ups
        $previousFollowUps = $followUp->patient->followUpCheckups()
            ->where('id', '!=', $id)
            ->orderBy('scheduled_date', 'desc')
            ->limit(5)
            ->get();

        return view('staff.follow-up-checkup-detail', compact('followUp', 'recentHealthRecords', 'previousFollowUps'));
    }

    public function update(Request $request, $id)
    {
        $followUp = FollowUpCheckup::findOrFail($id);

        $request->validate([
            'reason' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'type' => 'required|in:general,specialist,lab_test,imaging,medication_review',
            'scheduled_date' => 'required|date',
            'scheduled_time' => 'required|date_format:H:i',
            'duration' => 'nullable|integer|min:15|max:240',
            'location' => 'nullable|string|max:255',
            'status' => 'required|in:scheduled,confirmed,in_progress,completed,cancelled,no_show',
            'findings' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'next_steps' => 'nullable|string',
            'requires_additional_follow_up' => 'boolean',
            'next_follow_up_date' => 'nullable|date|required_if:requires_additional_follow_up,1',
            'is_urgent' => 'boolean',
            'urgent_reason' => 'nullable|string|required_if:is_urgent,1',
            'notes' => 'nullable|string',
        ]);

        $followUp->update($request->all());

        // If marked as completed, set completed_at timestamp
        if ($request->status === 'completed' && $followUp->status !== 'completed') {
            $followUp->completed_at = now();
            $followUp->save();
        }

        return redirect()->back()->with('success', 'Follow-up checkup updated successfully.');
    }

    public function destroy($id)
    {
        $followUp = FollowUpCheckup::findOrFail($id);
        $followUp->delete();

        return redirect()->back()->with('success', 'Follow-up checkup deleted successfully.');
    }

    public function markAsCompleted($id)
    {
        $followUp = FollowUpCheckup::findOrFail($id);
        $followUp->markAsCompleted();

        return redirect()->back()->with('success', 'Follow-up checkup marked as completed.');
    }

    public function markAsCancelled(Request $request, $id)
    {
        $request->validate([
            'cancellation_reason' => 'required|string'
        ]);

        $followUp = FollowUpCheckup::findOrFail($id);
        $followUp->markAsCancelled($request->cancellation_reason);

        return redirect()->back()->with('success', 'Follow-up checkup cancelled successfully.');
    }

    public function markAsNoShow($id)
    {
        $followUp = FollowUpCheckup::findOrFail($id);
        $followUp->markAsNoShow();

        return redirect()->back()->with('success', 'Follow-up checkup marked as no-show.');
    }

    public function getUrgentFollowUps()
    {
        $urgentFollowUps = FollowUpCheckup::with(['patient', 'staff'])
            ->urgent()
            ->orderBy('scheduled_date', 'asc')
            ->orderBy('scheduled_time', 'asc')
            ->get();

        return view('staff.urgent-follow-ups', compact('urgentFollowUps'));
    }

    public function getOverdueFollowUps()
    {
        $overdueFollowUps = FollowUpCheckup::with(['patient', 'staff'])
            ->overdue()
            ->orderBy('scheduled_date', 'asc')
            ->orderBy('scheduled_time', 'asc')
            ->get();

        return view('staff.overdue-follow-ups', compact('overdueFollowUps'));
    }

    public function getTodayFollowUps()
    {
        $todayFollowUps = FollowUpCheckup::with(['patient', 'staff'])
            ->today()
            ->orderBy('scheduled_time', 'asc')
            ->get();

        return view('staff.today-follow-ups', compact('todayFollowUps'));
    }

    public function getPatientFollowUps($patientId)
    {
        $patient = Patient::findOrFail($patientId);
        
        $followUps = $patient->followUpCheckups()
            ->with(['staff', 'healthMonitoring'])
            ->orderBy('scheduled_date', 'desc')
            ->paginate(15);

        return view('staff.patient-follow-ups', compact('patient', 'followUps'));
    }

    public function createFromHealthMonitoring($healthMonitoringId)
    {
        $healthMonitoring = HealthMonitoring::with('patient')->findOrFail($healthMonitoringId);
        $patients = Patient::all();
        $staff = User::all();

        return view('staff.create-follow-up-from-health', compact('healthMonitoring', 'patients', 'staff'));
    }

    public function generateReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'nullable|in:scheduled,confirmed,in_progress,completed,cancelled,no_show',
            'priority' => 'nullable|in:low,medium,high,urgent',
        ]);

        $query = FollowUpCheckup::with(['patient', 'staff'])
            ->whereBetween('scheduled_date', [$request->start_date, $request->end_date]);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->priority) {
            $query->where('priority', $request->priority);
        }

        $followUps = $query->orderBy('scheduled_date', 'asc')->get();

        // Calculate statistics
        $stats = [
            'total' => $followUps->count(),
            'completed' => $followUps->where('status', 'completed')->count(),
            'cancelled' => $followUps->where('status', 'cancelled')->count(),
            'no_show' => $followUps->where('status', 'no_show')->count(),
            'urgent' => $followUps->where('is_urgent', true)->count(),
        ];

        return view('staff.follow-up-report', compact('followUps', 'stats'));
    }
}
