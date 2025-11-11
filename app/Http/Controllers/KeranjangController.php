<?php

namespace App\Http\Controllers;

use App\Models\Keranjang;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KeranjangController extends Controller
{
    /**
     * Display the customer's cart page.
     */
    public function index()
    {
        $pelanggan = Auth::guard('web')->user();
        $cartItems = Keranjang::with('produk')
            ->where('id_pelanggan', $pelanggan->id_pelanggan)
            ->get();

        $total = $cartItems->sum('subtotal');

        // Get active discounts
        $diskons = \App\Models\Diskon::where('status', 'active')
            ->where('tanggal_mulai', '<=', now())
            ->where('tanggal_berakhir', '>=', now())
            ->get();

        return view('pelanggan.keranjang.index', [
            'cartItems' => $cartItems,
            'total' => $total,
            'diskons' => $diskons,
        ]);
    }
    public function updateCart(Request $request, $id)
    {
        $keranjang = Keranjang::findOrFail($id);
        $produk = Produk::findOrFail($keranjang->id_produk);

        if ($keranjang->id_pelanggan !== Auth::guard('web')->user()->id_pelanggan) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $jumlah = max(1, min($request->jumlah, $produk->stok));
        $subtotal = $jumlah * $produk->harga;

        $keranjang->update([
            'jumlah' => $jumlah,
            'subtotal' => $subtotal
        ]);

        return response()->json([
            'success' => true,
            'jumlah' => $jumlah,
            'subtotal' => $subtotal,
            'subtotal_formatted' => 'Rp ' . number_format($subtotal, 0, ',', '.'),
            'total' => Auth::user()->keranjangs->sum('subtotal'),
            'total_formatted' => 'Rp ' . number_format(Auth::user()->keranjangs->sum('subtotal'), 0, ',', '.')
        ]);
    }

    public function removeItem($id)
    {
        $keranjang = Keranjang::findOrFail($id);

        if ($keranjang->id_pelanggan !== Auth::guard('web')->user()->id_pelanggan) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $keranjang->delete();

        $total = Auth::user()->keranjangs->sum('subtotal');
        
        return response()->json([
            'success' => true,
            'total' => $total,
            'total_formatted' => 'Rp ' . number_format($total, 0, ',', '.')
        ]);
    }

    public function addToCart(Request $request, $id)
    {
        $produk = Produk::findOrFail($id);
        $pelanggan = Auth::guard('web')->user();

        if ($produk->stok < 1) {
            return response()->json(['error' => 'Stok habis'], 400);
        }

        $keranjang = Keranjang::where('id_pelanggan', $pelanggan->id_pelanggan)
                             ->where('id_produk', $produk->id_produk)
                             ->first();

        if ($keranjang) {
            if ($keranjang->jumlah < $produk->stok) {
                $keranjang->increment('jumlah');
                $keranjang->update(['subtotal' => $keranjang->jumlah * $produk->harga]);
            } else {
                return response()->json(['error' => 'Stok tidak mencukupi'], 400);
            }
        } else {
            $keranjang = Keranjang::create([
                'id_pelanggan' => $pelanggan->id_pelanggan,
                'id_produk' => $produk->id_produk,
                'jumlah' => 1,
                'subtotal' => $produk->harga
            ]);
        }

        $cartCount = Auth::user()->keranjangs->sum('jumlah');

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan ke keranjang',
            'cart_count' => $cartCount
        ]);
    }

    /**
     * Return the cart item count for the authenticated pelanggan.
     */
    public function getCartCount()
    {
        $pelanggan = Auth::guard('web')->user();
        $cartCount = Keranjang::where('id_pelanggan', $pelanggan->id_pelanggan)->sum('jumlah');

        return response()->json([
            'cart_count' => $cartCount
        ]);
    }
}