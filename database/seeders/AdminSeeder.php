<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::create([
            'nama_admin' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@totek.com',
            'password' => Hash::make('admin123'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Admin::create([
            'nama_admin' => 'Super Admin',
            'username' => 'superadmin',
            'email' => 'superadmin@totek.com',
            'password' => Hash::make('superadmin123'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}