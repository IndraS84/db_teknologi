<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Diskon;
use Carbon\Carbon;

class DiskonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Diskon::create([
            'nama_diskon' => 'Diskon Hari Raya 10%',
            'jenis_diskon' => 'persentase',
            'nilai_diskon' => 10,
            'tanggal_mulai' => Carbon::now()->subDays(1),
            'tanggal_berakhir' => Carbon::now()->addDays(30),
            'status' => 'active',
        ]);

        Diskon::create([
            'nama_diskon' => 'Diskon Potongan Rp 50.000',
            'jenis_diskon' => 'nominal',
            'nilai_diskon' => 50000,
            'tanggal_mulai' => Carbon::now()->subDays(1),
            'tanggal_berakhir' => Carbon::now()->addDays(30),
            'status' => 'active',
        ]);

        Diskon::create([
            'nama_diskon' => 'Diskon Spesial 5%',
            'jenis_diskon' => 'persentase',
            'nilai_diskon' => 5,
            'tanggal_mulai' => Carbon::now()->subDays(1),
            'tanggal_berakhir' => Carbon::now()->addDays(15),
            'status' => 'active',
        ]);
    }
}
