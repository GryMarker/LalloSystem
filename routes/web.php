<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\MedicinePickupController;
use App\Http\Controllers\DosageReminderController;
use App\Http\Controllers\HealthMonitoringController;
use App\Http\Controllers\FollowUpCheckupController;

Route::get('/', function () {
    return view('welcome');
});
//login
Route::post('/login', [AuthController::class, 'login'])->name('login');

//logout
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/')->with('success', 'You have been logged out.');
})->name('logout');

// Staff Pages
Route::get('/staff/dashboard', function () {
    return view('staff.dashboard');
})->name('staff.dashboard');
// Patient
Route::get('/staff/patients', [PatientController::class, 'index'])->name('staff.patients');
Route::resource('patients', PatientController::class)->except(['create', 'edit', 'show']);
Route::get('/patients/{id}/edit', [PatientController::class, 'edit'])->name('patients.edit');
Route::put('/patients/{id}', [PatientController::class, 'update'])->name('patients.update');
Route::get('/patients/search', [PatientController::class, 'search'])->name('patients.search');


// Appointments
Route::get('/staff/appointments', [AppointmentController::class, 'index'])->name('staff.appointments');
Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
Route::get('/appointments/{appointment}/edit', [AppointmentController::class, 'edit'])->name('appointments.edit');
Route::put('/appointments/{appointment}', [AppointmentController::class, 'update'])->name('appointments.update');
Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy'])->name('appointments.destroy');
Route::patch('/appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.updateStatus');
Route::get('/appointments/today', [AppointmentController::class, 'getTodayAppointments'])->name('appointments.today');
Route::get('/appointments/upcoming', [AppointmentController::class, 'getUpcomingAppointments'])->name('appointments.upcoming');

Route::get('/staff/medicine', function () {
    return view('staff.medicine');
})->name('staff.medicine');

// Medicine Pickups
Route::get('/staff/medicine-pickups', [MedicinePickupController::class, 'index'])->name('staff.medicine-pickups');
Route::post('/medicine-pickups', [MedicinePickupController::class, 'store'])->name('medicine-pickups.store');
Route::patch('/medicine-pickups/{id}/pickup', [MedicinePickupController::class, 'markAsPickedUp'])->name('medicine-pickups.pickup');
Route::delete('/medicine-pickups/{id}', [MedicinePickupController::class, 'destroy'])->name('medicine-pickups.destroy');

// Dosage Reminders
Route::get('/staff/dosage-reminders', [DosageReminderController::class, 'index'])->name('staff.dosage-reminders');
Route::post('/dosage-reminders', [DosageReminderController::class, 'store'])->name('dosage-reminders.store');
Route::get('/dosage-reminders/{id}', [DosageReminderController::class, 'show'])->name('dosage-reminders.show');
Route::put('/dosage-reminders/{id}', [DosageReminderController::class, 'update'])->name('dosage-reminders.update');
Route::delete('/dosage-reminders/{id}', [DosageReminderController::class, 'destroy'])->name('dosage-reminders.destroy');
Route::patch('/dosage-logs/{id}/taken', [DosageReminderController::class, 'markAsTaken'])->name('dosage-logs.taken');
Route::patch('/dosage-logs/{id}/skipped', [DosageReminderController::class, 'markAsSkipped'])->name('dosage-logs.skipped');
Route::get('/staff/today-reminders', [DosageReminderController::class, 'getTodayReminders'])->name('staff.today-reminders');
Route::get('/staff/patient-reminders/{patientId}', [DosageReminderController::class, 'getPatientReminders'])->name('staff.patient-reminders');

// Health Monitoring
Route::get('/staff/health-monitoring', [HealthMonitoringController::class, 'index'])->name('staff.health-monitoring');
Route::post('/health-monitoring', [HealthMonitoringController::class, 'store'])->name('health-monitoring.store');
Route::get('/health-monitoring/{id}', [HealthMonitoringController::class, 'show'])->name('health-monitoring.show');
Route::put('/health-monitoring/{id}', [HealthMonitoringController::class, 'update'])->name('health-monitoring.update');
Route::delete('/health-monitoring/{id}', [HealthMonitoringController::class, 'destroy'])->name('health-monitoring.destroy');
Route::get('/staff/patient-health-history/{patientId}', [HealthMonitoringController::class, 'getPatientHistory'])->name('staff.patient-health-history');
Route::get('/staff/critical-alerts', [HealthMonitoringController::class, 'getCriticalAlerts'])->name('staff.critical-alerts');
Route::get('/staff/today-health-records', [HealthMonitoringController::class, 'getTodayRecords'])->name('staff.today-health-records');
Route::post('/health-monitoring/report', [HealthMonitoringController::class, 'generateReport'])->name('health-monitoring.report');

// Follow-Up Checkups
Route::get('/staff/follow-up-checkups', [FollowUpCheckupController::class, 'index'])->name('staff.follow-up-checkups');
Route::post('/follow-up-checkups', [FollowUpCheckupController::class, 'store'])->name('follow-up-checkups.store');
Route::get('/follow-up-checkups/{id}', [FollowUpCheckupController::class, 'show'])->name('follow-up-checkups.show');
Route::put('/follow-up-checkups/{id}', [FollowUpCheckupController::class, 'update'])->name('follow-up-checkups.update');
Route::delete('/follow-up-checkups/{id}', [FollowUpCheckupController::class, 'destroy'])->name('follow-up-checkups.destroy');
Route::patch('/follow-up-checkups/{id}/complete', [FollowUpCheckupController::class, 'markAsCompleted'])->name('follow-up-checkups.complete');
Route::patch('/follow-up-checkups/{id}/cancel', [FollowUpCheckupController::class, 'markAsCancelled'])->name('follow-up-checkups.cancel');
Route::patch('/follow-up-checkups/{id}/no-show', [FollowUpCheckupController::class, 'markAsNoShow'])->name('follow-up-checkups.no-show');
Route::get('/staff/urgent-follow-ups', [FollowUpCheckupController::class, 'getUrgentFollowUps'])->name('staff.urgent-follow-ups');
Route::get('/staff/overdue-follow-ups', [FollowUpCheckupController::class, 'getOverdueFollowUps'])->name('staff.overdue-follow-ups');
Route::get('/staff/today-follow-ups', [FollowUpCheckupController::class, 'getTodayFollowUps'])->name('staff.today-follow-ups');
Route::get('/staff/patient-follow-ups/{patientId}', [FollowUpCheckupController::class, 'getPatientFollowUps'])->name('staff.patient-follow-ups');
Route::get('/staff/create-follow-up-from-health/{healthMonitoringId}', [FollowUpCheckupController::class, 'createFromHealthMonitoring'])->name('staff.create-follow-up-from-health');
Route::post('/follow-up-checkups/report', [FollowUpCheckupController::class, 'generateReport'])->name('follow-up-checkups.report');

// Admin Pages
Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');

// (Optional) General Patient List
// Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
