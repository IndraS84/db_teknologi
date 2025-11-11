<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Admin;
use App\Models\Pelanggan;
use App\Models\Produk;
use App\Models\Keranjang;
use App\Models\Transaksi;
use App\Models\Supplier;
use Illuminate\Support\Facades\Hash;

class CreateTestData extends Command
{
    protected $signature = 'test:data';
    protected $description = 'Create test data for payment flow verification';

    public function handle()
    {
        // Create test admin if not exists
        $admin = Admin::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'username' => 'testadmin',
                'nama_admin' => 'Test Admin',
                'password' => Hash::make('password'),
                'no_telp' => '08123456789',
            ]
        );

        // Create test customer if not exists
        $pelanggan = Pelanggan::firstOrCreate(
            ['email' => 'customer@test.com'],
            [
                'nama_pelanggan' => 'Test Customer',
                'password' => Hash::make('password'),
                'no_hp' => '08123456789',
                'alamat' => 'Test Address',
            ]
        );

        // Create test supplier if not exists
        $supplier = Supplier::firstOrCreate(
            ['nama_supplier' => 'Test Supplier'],
            [
                'no_telp' => '08123456789',
                'alamat' => 'Test Supplier Address',
            ]
        );

        // Create test product if not exists
        $produk = Produk::firstOrCreate(
            ['nama_produk' => 'Test Product'],
            [
                'id_admin' => $admin->id_admin,
                'id_supplier' => $supplier->id_supplier,
                'harga' => 100000,
                'stok' => 10,
                'deskripsi' => 'Test product description',
                'gambar' => 'test.jpg',
            ]
        );

        // Create cart item
        Keranjang::firstOrCreate(
            [
                'id_pelanggan' => $pelanggan->id_pelanggan,
                'id_produk' => $produk->id_produk,
            ],
            [
                'jumlah' => 1,
                'subtotal' => $produk->harga * 1,
            ]
        );

        $this->info('Test data created successfully!');
        $this->info('Admin login: admin@test.com / password');
        $this->info('Customer login: customer@test.com / password');
    }
}