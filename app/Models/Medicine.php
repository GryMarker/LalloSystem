<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    protected $fillable = [
        'name',
        'brand',
        'dosage',
        'stock_quantity',
        'expiration_date',
    ];

    protected $casts = [
        'expiration_date' => 'date',
    ];

    public function pickups()
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
} 