<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Keranjang;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KeranjangController extends Controller
{
    public function add(Request $request)
    {
        try {
            $request->validate([
                'id_produk' => 'required|exists:produks,id_produk',
                'jumlah' => 'required|integer|min:1'
            ]);

            $produk = Produk::findOrFail($request->id_produk);
            
            if ($produk->stok < $request->jumlah) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok tidak mencukupi'
                ], 400);
            }

            $existingCart = Keranjang::where('id_pelanggan', Auth::id())
                                   ->where('id_produk', $request->id_produk)
                                   ->first();

            if ($existingCart) {
                $existingCart->jumlah += $request->jumlah;
                $existingCart->save();
            } else {
                Keranjang::create([
                    'id_pelanggan' => Auth::id(),
                    'id_produk' => $request->id_produk,
                    'jumlah' => $request->jumlah
                ]);
            }

            $cartCount = Keranjang::where('id_pelanggan', Auth::id())->sum('jumlah');

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan ke keranjang',
                'cart_count' => $cartCount
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function index()
    {
        $cartItems = Keranjang::with('produk')
            ->where('id_pelanggan', Auth::id())
            ->get();

        $total = $cartItems->sum(function($item) {
            return $item->jumlah * $item->produk->harga;
        });

        // Get active discounts
        $diskons = \App\Models\Diskon::where('status', 'active')
            ->where('tanggal_mulai', '<=', now())
            ->where('tanggal_berakhir', '>=', now())
            ->get();

        return view('pelanggan.keranjang.index', compact('cartItems', 'total', 'diskons'));
    }

    public function update(Request $request)
    {
        try {
            $request->validate([
                'id_keranjang' => 'required|exists:keranjangs,id_keranjang',
                'jumlah' => 'required|integer|min:1'
            ]);

            $cartItem = Keranjang::findOrFail($request->id_keranjang);
            
            if ($cartItem->produk->stok < $request->jumlah) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok tidak mencukupi'
                ], 400);
            }

            $cartItem->jumlah = $request->jumlah;
            $cartItem->save();

            $total = Keranjang::where('id_pelanggan', Auth::id())
                ->join('produks', 'keranjangs.id_produk', '=', 'produks.id_produk')
                ->selectRaw('SUM(keranjangs.jumlah * produks.harga) as total')
                ->value('total');

            return response()->json([
                'success' => true,
                'message' => 'Keranjang berhasil diupdate',
                'total' => $total,
                'cart_count' => Keranjang::where('id_pelanggan', Auth::id())->sum('jumlah')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function remove(Request $request)
    {
        try {
            $cartItem = Keranjang::where('id_keranjang', $request->id_keranjang)
                                ->where('id_pelanggan', Auth::id())
                                ->firstOrFail();
            
            $cartItem->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item berhasil dihapus dari keranjang',
                'cart_count' => Keranjang::where('id_pelanggan', Auth::id())->sum('jumlah')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}