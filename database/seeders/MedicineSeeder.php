<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Medicine;

class MedicineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $medicines = [
            [
                'name' => 'Paracetamol',
                'brand' => 'Tylenol',
                'dosage' => '500mg',
                'stock_quantity' => 100,
                'expiration_date' => '2026-12-31',
            ],
            [
                'name' => 'Ibuprofen',
                'brand' => 'Advil',
                'dosage' => '400mg',
                'stock_quantity' => 75,
                'expiration_date' => '2026-10-15',
            ],
            [
                'name' => 'Amoxicillin',
                'brand' => 'Amoxil',
                'dosage' => '250mg',
                'stock_quantity' => 50,
                'expiration_date' => '2025-06-30',
            ],
            [
                'name' => 'Omeprazole',
                'brand' => 'Prilosec',
                'dosage' => '20mg',
                'stock_quantity' => 30,
                'expiration_date' => '2026-03-15',
            ],
            [
                'name' => 'Metformin',
                'brand' => 'Glucophage',
                'dosage' => '500mg',
                'stock_quantity' => 60,
                'expiration_date' => '2026-08-20',
            ],
        ];

        foreach ($medicines as $medicine) {
            Medicine::create($medicine);
        }
    }
}
