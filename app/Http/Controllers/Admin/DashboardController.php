<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProduk = Produk::count();
        $totalPelanggan = Pelanggan::count();
        $totalTransaksi = Transaksi::count();
        $totalPenjualan = Transaksi::sum('total_setelah_diskon');

        // Monthly sales data for the last 12 months
        $monthlySales = Transaksi::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(total_setelah_diskon) as total')
        )
        ->where('created_at', '>=', now()->subMonths(12))
        ->groupBy('year', 'month')
        ->orderBy('year')
        ->orderBy('month')
        ->get()
        ->map(function ($item) {
            return [
                'month' => $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT),
                'total' => (float) $item->total
            ];
        });

        // Product categories data
        $productCategories = Produk::select('kategori', DB::raw('COUNT(*) as count'))
            ->groupBy('kategori')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => $item->kategori ?: 'Uncategorized',
                    'data' => (int) $item->count
                ];
            });

        // Recent transactions
        $recentTransactions = Transaksi::with('pelanggan')
            ->latest()
            ->take(5)
            ->get();

        // Sales growth percentage (compare current month with previous month)
        $currentMonthSales = Transaksi::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_setelah_diskon');

        $previousMonthSales = Transaksi::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('total_setelah_diskon');

        $salesGrowth = $previousMonthSales > 0
            ? (($currentMonthSales - $previousMonthSales) / $previousMonthSales) * 100
            : 0;

        return view('admin.dashboard', compact(
            'totalProduk',
            'totalPelanggan',
            'totalTransaksi',
            'totalPenjualan',
            'monthlySales',
            'productCategories',
            'recentTransactions',
            'salesGrowth'
        ));
    }
}
