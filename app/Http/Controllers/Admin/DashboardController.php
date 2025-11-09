<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProduk = Produk::count();
        $totalPelanggan = Pelanggan::count();
        $totalTransaksi = Transaksi::count();
        $totalPenjualan = Transaksi::sum('total_setelah_diskon');

        return view('admin.dashboard', compact(
            'totalProduk',
            'totalPelanggan',
            'totalTransaksi',
            'totalPenjualan'
        ));
    }
}
