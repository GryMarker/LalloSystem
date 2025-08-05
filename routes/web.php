<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\MedicinePickupController;
use App\Http\Controllers\DosageReminderController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/login', [AuthController::class, 'login'])->name('login');

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

// Admin Pages
Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');

// (Optional) General Patient List
// Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
