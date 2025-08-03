<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@lallo.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // RHU Staff user
        User::create([
            'name' => 'RHU Staff',
            'email' => 'staff@lallo.com',
            'password' => Hash::make('staff123'),
            'role' => 'staff',
        ]);

        
    }
}
