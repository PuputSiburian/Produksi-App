<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Admin
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@getronics.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'jabatan' => 'System Administrator',
            'divisi' => 'IT'
        ]);

        // Manager
        User::create([
            'name' => 'Budi Santoso',
            'email' => 'manager@getronics.com',
            'password' => Hash::make('manager123'),
            'role' => 'manager',
            'jabatan' => 'Kepala Produksi',
            'divisi' => 'Produksi'
        ]);

        // Supervisor
        User::create([
            'name' => 'Siti Nurhaliza',
            'email' => 'supervisor@getronics.com',
            'password' => Hash::make('super123'),
            'role' => 'supervisor',
            'jabatan' => 'Leader Line',
            'divisi' => 'Produksi'
        ]);

        // Operator Cutting
        User::create([
            'name' => 'Rizki Fadillah',
            'email' => 'operator1@getronics.com',
            'password' => Hash::make('op123'),
            'role' => 'operator_cutting',
            'jabatan' => 'Operator',
            'divisi' => 'Cutting'
        ]);

        // Operator Crimping
        User::create([
            'name' => 'Dewi Sartika',
            'email' => 'operator2@getronics.com',
            'password' => Hash::make('op123'),
            'role' => 'operator_crimping',
            'jabatan' => 'Operator',
            'divisi' => 'Crimping'
        ]);

        // Operator Line
        User::create([
            'name' => 'Andi Wijaya',
            'email' => 'operator3@getronics.com',
            'password' => Hash::make('op123'),
            'role' => 'operator_line',
            'jabatan' => 'Operator',
            'divisi' => 'Assembly'
        ]);
    }
}