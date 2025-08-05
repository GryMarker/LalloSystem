<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $table = 'patients';

    // Add this to allow mass assignment for form inputs
    protected $fillable = [
        'name',
        'birthdate',
        'gender',
        'address',
        'contact_number',
    ];

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function medicinePickups()
    {
        return $this->hasMany(MedicinePickup::class);
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    public function dosageReminders()
    {
        return $this->hasMany(DosageReminder::class);
    }

    public function dosageLogs()
    {
        return $this->hasMany(DosageLog::class);
    }

    public function healthMonitorings()
    {
        return $this->hasMany(HealthMonitoring::class);
    }
}
