<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class FollowUpCheckup extends Model
{
    protected $fillable = [
        'patient_id',
        'health_monitoring_id',
        'appointment_id',
        'staff_id',
        'reason',
        'description',
        'priority',
        'type',
        'scheduled_date',
        'scheduled_time',
        'duration',
        'location',
        'status',
        'completed_at',
        'cancelled_at',
        'findings',
        'recommendations',
        'next_steps',
        'requires_additional_follow_up',
        'next_follow_up_date',
        'notes',
        'attachments',
        'is_urgent',
        'urgent_reason',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'scheduled_time' => 'datetime:H:i',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'next_follow_up_date' => 'date',
        'attachments' => 'array',
        'requires_additional_follow_up' => 'boolean',
        'is_urgent' => 'boolean',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function healthMonitoring()
    {
        return $this->belongsTo(HealthMonitoring::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function getStatusBadgeClass()
    {
        return match($this->status) {
            'scheduled' => 'bg-blue-100 text-blue-800',
            'confirmed' => 'bg-green-100 text-green-800',
            'in_progress' => 'bg-yellow-100 text-yellow-800',
            'completed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            'no_show' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getPriorityBadgeClass()
    {
        return match($this->priority) {
            'low' => 'bg-green-100 text-green-800',
            'medium' => 'bg-yellow-100 text-yellow-800',
            'high' => 'bg-orange-100 text-orange-800',
            'urgent' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getTypeText()
    {
        return match($this->type) {
            'general' => 'General Checkup',
            'specialist' => 'Specialist Consultation',
            'lab_test' => 'Laboratory Test',
            'imaging' => 'Imaging Study',
            'medication_review' => 'Medication Review',
            default => 'Unknown'
        };
    }

    public function getPriorityText()
    {
        return match($this->priority) {
            'low' => 'Low Priority',
            'medium' => 'Medium Priority',
            'high' => 'High Priority',
            'urgent' => 'Urgent',
            default => 'Unknown'
        };
    }

    public function isOverdue()
    {
        $scheduledDateTime = Carbon::parse($this->scheduled_date)->setTimeFrom($this->scheduled_time);
        return $this->status === 'scheduled' && now()->isAfter($scheduledDateTime);
    }

    public function isToday()
    {
        return $this->scheduled_date->isToday();
    }

    public function isUpcoming()
    {
        return $this->scheduled_date->isFuture() && $this->status === 'scheduled';
    }

    public function getScheduledDateTime()
    {
        return Carbon::parse($this->scheduled_date)->setTimeFrom($this->scheduled_time);
    }

    public function getDurationText()
    {
        if ($this->duration < 60) {
            return "{$this->duration} minutes";
        }
        
        $hours = floor($this->duration / 60);
        $minutes = $this->duration % 60;
        
        if ($minutes === 0) {
            return "{$hours} hour" . ($hours > 1 ? 's' : '');
        }
        
        return "{$hours}h {$minutes}m";
    }

    public function markAsCompleted()
    {
        $this->status = 'completed';
        $this->completed_at = now();
        $this->save();
    }

    public function markAsCancelled($reason = null)
    {
        $this->status = 'cancelled';
        $this->cancelled_at = now();
        if ($reason) {
            $this->notes = ($this->notes ? $this->notes . "\n" : '') . "Cancelled: " . $reason;
        }
        $this->save();
    }

    public function markAsNoShow()
    {
        $this->status = 'no_show';
        $this->save();
    }

    public function isUrgent()
    {
        return $this->is_urgent || $this->priority === 'urgent';
    }

    public function getAttachmentsText()
    {
        if (!$this->attachments || empty($this->attachments)) {
            return 'No attachments';
        }
        return implode(', ', $this->attachments);
    }

    public function scopeUrgent($query)
    {
        return $query->where('is_urgent', true)->orWhere('priority', 'urgent');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'scheduled')
                    ->where('scheduled_date', '<', now()->toDateString());
    }

    public function scopeToday($query)
    {
        return $query->where('scheduled_date', now()->toDateString());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_date', '>=', now()->toDateString())
                    ->where('status', 'scheduled');
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
