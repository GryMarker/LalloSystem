<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DosageLog extends Model
{
    protected $fillable = [
        'dosage_reminder_id',
        'patient_id',
        'medicine_id',
        'scheduled_date',
        'scheduled_time',
        'taken_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'scheduled_time' => 'datetime:H:i',
        'taken_at' => 'datetime',
    ];

    public function dosageReminder()
    {
        return $this->belongsTo(DosageReminder::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }

    public function isOverdue()
    {
        $now = now();
        $scheduledDateTime = $this->scheduled_date->setTimeFrom($this->scheduled_time);
        
        return $this->status === 'pending' && $now->isAfter($scheduledDateTime->addHours(2));
    }

    public function getStatusBadgeClass()
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'taken' => 'bg-green-100 text-green-800',
            'missed' => 'bg-red-100 text-red-800',
            'skipped' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }
}
