<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::with(['patient', 'staff'])
            ->orderBy('scheduled_at', 'asc')
            ->get();

        $patients = Patient::all();
        $staff = User::all();

        return view('staff.appointments', compact('appointments', 'patients', 'staff'));
    }

    public function store(Request $request)
    {
        // Debug: Log the incoming request data
        Log::info('Appointment creation request:', $request->all());
        
        try {
            $validated = $request->validate([
                'patient_id' => 'required|exists:patients,id',
                'type' => 'required|in:medicine_pickup,checkup,consultation,follow_up',
                'scheduled_at' => 'required|date',
                'duration' => 'nullable|integer|min:15|max:240',
                'location' => 'nullable|string|max:255',
                'notes' => 'nullable|string',
            ]);
            
            Log::info('Validation passed');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed:', $e->errors());
            if ($request->expectsJson()) {
                return response()->json(['errors' => $e->errors()], 422);
            }
            throw $e;
        }

        // Check if user is authenticated
        if (!Auth::check()) {
            Log::error('User not authenticated');
            if ($request->expectsJson()) {
                return response()->json(['errors' => ['auth' => ['User not authenticated']]], 401);
            }
            return redirect()->route('login');
        }

        Log::info('Creating appointment with user ID: ' . Auth::id());

        $appointment = Appointment::create([
            'patient_id' => $request->patient_id,
            'staff_id' => Auth::id(),
            'type' => $request->type,
            'scheduled_at' => $request->scheduled_at,
            'duration' => $request->duration ?? 30,
            'location' => $request->location,
            'notes' => $request->notes,
            'status' => 'pending'
        ]);

        Log::info('Appointment created successfully:', ['id' => $appointment->id]);

        if ($request->expectsJson()) {
            // Load the appointment with its relationships for immediate display
            $appointment->load('patient');
            return response()->json(['success' => true, 'appointment' => $appointment]);
        }

        return redirect()->route('staff.appointments')->with('success', 'Appointment created successfully!');
    }

    public function edit(Appointment $appointment)
    {
        // Format the scheduled_at datetime for HTML datetime-local input
        $appointment->scheduled_at = $appointment->scheduled_at->format('Y-m-d\TH:i');
        
        // Return the appointment data as JSON for the modal
        return response()->json($appointment);
    }

    public function update(Request $request, Appointment $appointment)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'type' => 'required|in:medicine_pickup,checkup,consultation,follow_up',
            'scheduled_at' => 'required|date',
            'status' => 'required|in:pending,confirmed,completed,cancelled,missed',
            'duration' => 'nullable|integer|min:15|max:240',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $appointment->update([
            'patient_id' => $request->patient_id,
            'type' => $request->type,
            'scheduled_at' => $request->scheduled_at,
            'status' => $request->status,
            'duration' => $request->duration ?? 30,
            'location' => $request->location,
            'notes' => $request->notes,
        ]);

        return redirect()->route('staff.appointments')->with('success', 'Appointment updated successfully!');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        return redirect()->route('staff.appointments')->with('success', 'Appointment deleted successfully!');
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled,missed'
        ]);

        $appointment->update(['status' => $request->status]);

        return response()->json(['success' => true, 'message' => 'Status updated successfully']);
    }

    public function getTodayAppointments()
    {
        $appointments = Appointment::with(['patient', 'staff'])
            ->whereDate('scheduled_at', today())
            ->orderBy('scheduled_at', 'asc')
            ->get();

        return response()->json($appointments);
    }

    public function getUpcomingAppointments()
    {
        $appointments = Appointment::with(['patient', 'staff'])
            ->where('scheduled_at', '>', now())
            ->where('status', '!=', 'cancelled')
            ->orderBy('scheduled_at', 'asc')
            ->limit(10)
            ->get();

        return response()->json($appointments);
    }
} 