<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Keranjang;
use App\Models\Produk;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KeranjangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'id_pelanggan' => 'required|exists:pelanggans,id_pelanggan',
        ]);

        $keranjangs = Keranjang::with(['pelanggan', 'produk.admin', 'produk.supplier', 'produk.diskon'])
            ->where('id_pelanggan', $request->id_pelanggan)
            ->get();

        $total = $keranjangs->sum('subtotal');

        return response()->json([
            'success' => true,
            'data' => $keranjangs,
            'total' => $total,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id_pelanggan' => 'required|exists:pelanggans,id_pelanggan',
            'id_produk' => 'required|exists:produks,id_produk',
            'jumlah' => 'required|integer|min:1',
        ]);

        // Check if produk exists and has enough stock
        $produk = Produk::findOrFail($validated['id_produk']);
        
        if ($produk->stok < $validated['jumlah']) {
            return response()->json([
                'success' => false,
                'message' => 'Stok produk tidak mencukupi',
            ], 400);
        }

        // Check if item already exists in cart
        $existingKeranjang = Keranjang::where('id_pelanggan', $validated['id_pelanggan'])
            ->where('id_produk', $validated['id_produk'])
            ->first();

        if ($existingKeranjang) {
            // Update existing item
            $newJumlah = $existingKeranjang->jumlah + $validated['jumlah'];
            
            if ($produk->stok < $newJumlah) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok produk tidak mencukupi',
                ], 400);
            }

            $existingKeranjang->jumlah = $newJumlah;
            $existingKeranjang->subtotal = $produk->harga * $newJumlah;
            $existingKeranjang->save();
            $existingKeranjang->load(['pelanggan', 'produk']);

            return response()->json([
                'success' => true,
                'message' => 'Item keranjang berhasil diupdate',
                'data' => $existingKeranjang,
            ]);
        }

        // Calculate subtotal
        $subtotal = $produk->harga * $validated['jumlah'];

        $keranjang = Keranjang::create([
            'id_pelanggan' => $validated['id_pelanggan'],
            'id_produk' => $validated['id_produk'],
            'jumlah' => $validated['jumlah'],
            'subtotal' => $subtotal,
        ]);

        $keranjang->load(['pelanggan', 'produk']);

        return response()->json([
            'success' => true,
            'message' => 'Item berhasil ditambahkan ke keranjang',
            'data' => $keranjang,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $keranjang = Keranjang::with(['pelanggan', 'produk.admin', 'produk.supplier', 'produk.diskon'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $keranjang,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $keranjang = Keranjang::findOrFail($id);

        $validated = $request->validate([
            'jumlah' => 'required|integer|min:1',
        ]);

        // Check if produk has enough stock
        $produk = Produk::findOrFail($keranjang->id_produk);
        
        if ($produk->stok < $validated['jumlah']) {
            return response()->json([
                'success' => false,
                'message' => 'Stok produk tidak mencukupi',
            ], 400);
        }

        // Update jumlah and recalculate subtotal
        $keranjang->jumlah = $validated['jumlah'];
        $keranjang->subtotal = $produk->harga * $validated['jumlah'];
        $keranjang->save();
        $keranjang->load(['pelanggan', 'produk']);

        return response()->json([
            'success' => true,
            'message' => 'Keranjang berhasil diupdate',
            'data' => $keranjang,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $keranjang = Keranjang::findOrFail($id);
        $keranjang->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item keranjang berhasil dihapus',
        ]);
    }

    /**
     * Clear all items from cart for a pelanggan.
     */
    public function clear(Request $request): JsonResponse
    {
        $request->validate([
            'id_pelanggan' => 'required|exists:pelanggans,id_pelanggan',
        ]);

        Keranjang::where('id_pelanggan', $request->id_pelanggan)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Keranjang berhasil dikosongkan',
        ]);
    }
}
