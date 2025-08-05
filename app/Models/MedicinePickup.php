<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicinePickup extends Model
{
    protected $fillable = [
        'patient_id',
        'medicine_id',
        'quantity',
        'scheduled_date',
        'pickup_date',
        'status',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'pickup_date' => 'date',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }
}
