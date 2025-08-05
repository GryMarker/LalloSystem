<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class HealthMonitoring extends Model
{
    protected $fillable = [
        'patient_id',
        'recorded_date',
        'recorded_time',
        'temperature',
        'blood_pressure_systolic',
        'blood_pressure_diastolic',
        'pulse_rate',
        'respiratory_rate',
        'oxygen_saturation',
        'weight',
        'height',
        'bmi',
        'blood_sugar',
        'pain_level',
        'pain_location',
        'mood_score',
        'mood_notes',
        'symptoms',
        'observations',
        'notes',
        'status',
        'requires_follow_up',
        'alert_notes',
    ];

    protected $casts = [
        'recorded_date' => 'date',
        'recorded_time' => 'datetime:H:i',
        'temperature' => 'decimal:1',
        'weight' => 'decimal:2',
        'height' => 'decimal:2',
        'bmi' => 'decimal:2',
        'blood_sugar' => 'decimal:2',
        'oxygen_saturation' => 'decimal:1',
        'symptoms' => 'array',
        'requires_follow_up' => 'boolean',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function followUpCheckups()
    {
        return $this->hasMany(FollowUpCheckup::class);
    }

    public function getStatusBadgeClass()
    {
        return match($this->status) {
            'normal' => 'bg-green-100 text-green-800',
            'warning' => 'bg-yellow-100 text-yellow-800',
            'critical' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getBloodPressureText()
    {
        if ($this->blood_pressure_systolic && $this->blood_pressure_diastolic) {
            return "{$this->blood_pressure_systolic}/{$this->blood_pressure_diastolic} mmHg";
        }
        return 'Not recorded';
    }

    public function getBmiCategory()
    {
        if (!$this->bmi) return 'Not calculated';
        
        return match(true) {
            $this->bmi < 18.5 => 'Underweight',
            $this->bmi < 25 => 'Normal weight',
            $this->bmi < 30 => 'Overweight',
            default => 'Obese'
        };
    }

    public function getBmiCategoryClass()
    {
        if (!$this->bmi) return 'text-gray-500';
        
        return match(true) {
            $this->bmi < 18.5 => 'text-blue-600',
            $this->bmi < 25 => 'text-green-600',
            $this->bmi < 30 => 'text-yellow-600',
            default => 'text-red-600'
        };
    }

    public function isTemperatureNormal()
    {
        if (!$this->temperature) return null;
        return $this->temperature >= 36.0 && $this->temperature <= 37.5;
    }

    public function isBloodPressureNormal()
    {
        if (!$this->blood_pressure_systolic || !$this->blood_pressure_diastolic) return null;
        return $this->blood_pressure_systolic < 140 && $this->blood_pressure_diastolic < 90;
    }

    public function isPulseRateNormal()
    {
        if (!$this->pulse_rate) return null;
        return $this->pulse_rate >= 60 && $this->pulse_rate <= 100;
    }

    public function isOxygenSaturationNormal()
    {
        if (!$this->oxygen_saturation) return null;
        return $this->oxygen_saturation >= 95.0;
    }

    public function getPainLevelText()
    {
        if (!$this->pain_level) return 'Not recorded';
        
        return match($this->pain_level) {
            0 => 'No pain',
            1, 2 => 'Mild pain',
            3, 4 => 'Moderate pain',
            5, 6 => 'Moderately severe pain',
            7, 8 => 'Severe pain',
            9, 10 => 'Very severe pain',
            default => 'Unknown'
        };
    }

    public function getMoodText()
    {
        if (!$this->mood_score) return 'Not recorded';
        
        return match($this->mood_score) {
            1, 2 => 'Very poor',
            3, 4 => 'Poor',
            5, 6 => 'Fair',
            7, 8 => 'Good',
            9, 10 => 'Excellent',
            default => 'Unknown'
        };
    }

    public function getSymptomsText()
    {
        if (!$this->symptoms || empty($this->symptoms)) {
            return 'None reported';
        }
        return implode(', ', $this->symptoms);
    }

    public function calculateBmi()
    {
        if ($this->weight && $this->height) {
            $heightInMeters = $this->height / 100;
            $this->bmi = round($this->weight / ($heightInMeters * $heightInMeters), 2);
            return $this->bmi;
        }
        return null;
    }

    public function assessStatus()
    {
        $warnings = [];
        $critical = [];

        // Temperature assessment
        if ($this->temperature) {
            if ($this->temperature > 38.0 || $this->temperature < 35.0) {
                $critical[] = 'Abnormal temperature';
            } elseif ($this->temperature > 37.5 || $this->temperature < 36.0) {
                $warnings[] = 'Slightly elevated temperature';
            }
        }

        // Blood pressure assessment
        if ($this->blood_pressure_systolic && $this->blood_pressure_diastolic) {
            if ($this->blood_pressure_systolic >= 180 || $this->blood_pressure_diastolic >= 110) {
                $critical[] = 'Severe hypertension';
            } elseif ($this->blood_pressure_systolic >= 140 || $this->blood_pressure_diastolic >= 90) {
                $warnings[] = 'Elevated blood pressure';
            }
        }

        // Pulse rate assessment
        if ($this->pulse_rate) {
            if ($this->pulse_rate > 120 || $this->pulse_rate < 50) {
                $critical[] = 'Abnormal pulse rate';
            } elseif ($this->pulse_rate > 100 || $this->pulse_rate < 60) {
                $warnings[] = 'Slightly abnormal pulse rate';
            }
        }

        // Oxygen saturation assessment
        if ($this->oxygen_saturation) {
            if ($this->oxygen_saturation < 90) {
                $critical[] = 'Low oxygen saturation';
            } elseif ($this->oxygen_saturation < 95) {
                $warnings[] = 'Slightly low oxygen saturation';
            }
        }

        // Pain level assessment
        if ($this->pain_level && $this->pain_level >= 7) {
            $warnings[] = 'High pain level';
        }

        // Update status based on assessments
        if (!empty($critical)) {
            $this->status = 'critical';
            $this->alert_notes = 'Critical: ' . implode(', ', $critical);
        } elseif (!empty($warnings)) {
            $this->status = 'warning';
            $this->alert_notes = 'Warning: ' . implode(', ', $warnings);
        } else {
            $this->status = 'normal';
            $this->alert_notes = null;
        }

        return $this->status;
    }
}
