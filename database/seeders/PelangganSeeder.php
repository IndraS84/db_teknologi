<?php

namespace Database\Seeders;

use App\Models\Pelanggan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PelangganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pelanggans = [
            [
                'nama_pelanggan' => 'John Doe',
                'alamat' => 'Jl. Merdeka No. 123',
                'no_hp' => '08123456789',
                'email' => 'john.doe@example.com',
                'password' => Hash::make('customer123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_pelanggan' => 'Jane Smith',
                'alamat' => 'Jl. Sudirman No. 45',
                'no_hp' => '08987654321',
                'email' => 'jane.smith@example.com',
                'password' => Hash::make('customer123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_pelanggan' => 'Budi Santoso',
                'alamat' => 'Jl. Ahmad Yani No. 67',
                'no_hp' => '08567891234',
                'email' => 'budi.santoso@example.com',
                'password' => Hash::make('customer123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($pelanggans as $pelanggan) {
            Pelanggan::create($pelanggan);
        }
    }
}