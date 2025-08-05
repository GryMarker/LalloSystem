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
}
