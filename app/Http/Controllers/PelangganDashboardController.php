<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Keranjang;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PelangganDashboardController extends Controller
{
    public function index(Request $request)
    {
        $query = Produk::query();

        // Apply search filter
        if ($request->has('search')) {
            $query->where('nama_produk', 'like', '%' . $request->search . '%')
                  ->orWhere('deskripsi', 'like', '%' . $request->search . '%');
        }

        // Apply price filter
        if ($request->has('min_price')) {
            $query->where('harga', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('harga', '<=', $request->max_price);
        }

        // Only show products with stock available
        $query->where('stok', '>', 0);

        // Get products with pagination
        $products = $query->orderBy('nama_produk')->paginate(12);

        // Get stats for the dashboard
        $pelanggan = Auth::guard('web')->user();
        $activeOrders = Transaksi::where('id_pelanggan', $pelanggan->id_pelanggan)
                                ->whereIn('status_transaksi', ['pending', 'processing', 'awaiting_payment', 'payment_uploaded'])
                                ->count();
        
        $cartItems = Keranjang::where('id_pelanggan', $pelanggan->id_pelanggan)->sum('jumlah');

        return view('pelanggan.dashboard', compact('products', 'activeOrders', 'cartItems'));
    }

    public function addToCart(Request $request, $id)
    {
        $produk = Produk::findOrFail($id);
        $pelanggan = Auth::guard('web')->user();

        // Check if product already in cart
        $keranjang = Keranjang::where('id_pelanggan', $pelanggan->id_pelanggan)
                             ->where('id_produk', $produk->id_produk)
                             ->first();

        if ($keranjang) {
            // Update quantity if already in cart
            $keranjang->increment('jumlah');
            $keranjang->update([
                'subtotal' => $keranjang->jumlah * $produk->harga
            ]);
        } else {
            // Add new item to cart
            Keranjang::create([
                'id_pelanggan' => $pelanggan->id_pelanggan,
                'id_produk' => $produk->id_produk,
                'jumlah' => 1,
                'subtotal' => $produk->harga
            ]);
        }

        return back()->with('success', 'Produk berhasil ditambahkan ke keranjang');
    }
}