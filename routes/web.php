<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PatientController;

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





//Appointments

Route::get('/staff/appointments', function () {
    return view('staff.appointments');
})->name('staff.appointments');

Route::get('/staff/medicine', function () {
    return view('staff.medicine');
})->name('staff.medicine');

// Admin Pages
Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');

// (Optional) General Patient List
// Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
