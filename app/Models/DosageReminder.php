<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DosageReminder extends Model
{
    protected $fillable = [
        'patient_id',
        'prescription_id',
        'medicine_id',
        'dosage_instruction',
        'reminder_time',
        'frequency',
        'custom_schedule',
        'start_date',
        'end_date',
        'is_active',
        'send_sms',
        'send_email',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'reminder_time' => 'datetime:H:i',
        'custom_schedule' => 'array',
        'is_active' => 'boolean',
        'send_sms' => 'boolean',
        'send_email' => 'boolean',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }

    public function dosageLogs()
    {
        return $this->hasMany(DosageLog::class);
    }

    public function isActive()
    {
        $today = now()->startOfDay();
        return $this->is_active && $today->between($this->start_date, $this->end_date);
    }

    public function getNextReminderTime()
    {
        $now = now();
        $today = $now->startOfDay();
        
        if (!$this->isActive()) {
            return null;
        }

        $reminderTime = Carbon::parse($this->reminder_time);
        $nextReminder = $today->copy()->setTime($reminderTime->hour, $reminderTime->minute);

        if ($nextReminder->isPast()) {
            $nextReminder->addDay();
        }

        return $nextReminder;
    }

    public function getFrequencyText()
    {
        return match($this->frequency) {
            'daily' => 'Once daily',
            'twice_daily' => 'Twice daily',
            'thrice_daily' => 'Three times daily',
            'weekly' => 'Once weekly',
            'custom' => 'Custom schedule',
            default => 'Unknown'
        };
    }
}
