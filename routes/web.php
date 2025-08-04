<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\AppointmentController;

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

// Admin Pages
Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');

// (Optional) General Patient List
// Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
