<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    protected $fillable = [
        'patient_id',
        'medicine_id',
        'dosage',
        'duration',
        'start_date',
    ];

    protected $casts = [
        'start_date' => 'date',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }

    public function dosageReminders()
    {
        return $this->hasMany(DosageReminder::class);
    }

    public function getEndDateAttribute()
    {
        return $this->start_date->addDays($this->duration);
    }

    public function isActive()
    {
        $today = now()->startOfDay();
        return $today->between($this->start_date, $this->end_date);
    }
}
