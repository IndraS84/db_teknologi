<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Produk::with(['admin', 'supplier', 'diskon']);

        // Filter by stok tersedia
        if ($request->has('stok_tersedia')) {
            $query->where('stok', '>', 0);
        }

        // Search by nama_produk
        if ($request->has('search')) {
            $query->where('nama_produk', 'like', '%' . $request->search . '%');
        }

        // Filter by supplier
        if ($request->has('id_supplier')) {
            $query->where('id_supplier', $request->id_supplier);
        }

        // Filter by diskon
        if ($request->has('id_diskon')) {
            $query->where('id_diskon', $request->id_diskon);
        }

        $produks = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $produks,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama_produk' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'id_admin' => 'required|exists:admins,id_admin',
            'id_supplier' => 'required|exists:suppliers,id_supplier',
            'id_diskon' => 'nullable|exists:diskons,id_diskon',
        ]);

        $produk = Produk::create($validated);
        $produk->load(['admin', 'supplier', 'diskon']);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan',
            'data' => $produk,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $produk = Produk::with(['admin', 'supplier', 'diskon'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $produk,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $produk = Produk::findOrFail($id);

        $validated = $request->validate([
            'nama_produk' => 'sometimes|required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga' => 'sometimes|required|numeric|min:0',
            'stok' => 'sometimes|required|integer|min:0',
            'id_admin' => 'sometimes|required|exists:admins,id_admin',
            'id_supplier' => 'sometimes|required|exists:suppliers,id_supplier',
            'id_diskon' => 'nullable|exists:diskons,id_diskon',
        ]);

        $produk->update($validated);
        $produk->load(['admin', 'supplier', 'diskon']);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil diupdate',
            'data' => $produk,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $produk = Produk::findOrFail($id);
        $produk->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dihapus',
        ]);
    }
}
