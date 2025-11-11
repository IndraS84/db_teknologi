<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BarangMasuk;
use App\Models\Produk;
use App\Models\Supplier;
use App\Models\Admin;
use Illuminate\Http\Request;

class BarangMasukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = BarangMasuk::with(['produk', 'supplier', 'admin']);

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('produk', function($q) use ($search) {
                $q->where('nama_produk', 'like', "%{$search}%");
            })->orWhereHas('supplier', function($q) use ($search) {
                $q->where('nama_supplier', 'like', "%{$search}%");
            });
        }

        // Filter by date
        if ($request->has('tanggal_mulai')) {
            $query->where('tanggal_masuk', '>=', $request->tanggal_mulai);
        }

        if ($request->has('tanggal_akhir')) {
            $query->where('tanggal_masuk', '<=', $request->tanggal_akhir);
        }

        $barangMasuks = $query->orderBy('tanggal_masuk', 'desc')->paginate(10);

        return view('admin.barang-masuk.index', compact('barangMasuks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $produks = Produk::all();
        $suppliers = Supplier::all();
        $admins = Admin::all();

        return view('admin.barang-masuk.create', compact('produks', 'suppliers', 'admins'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_produk' => 'required|exists:produks,id_produk',
            'id_supplier' => 'required|exists:suppliers,id_supplier',
            'id_admin' => 'required|exists:admins,id_admin',
            'jumlah' => 'required|integer|min:1',
            'tanggal_masuk' => 'required|date',
        ]);

        // Create barang masuk
        BarangMasuk::create($validated);

        // Update product stock
        $produk = Produk::findOrFail($validated['id_produk']);
        $produk->increment('stok', $validated['jumlah']);

        return redirect()->route('admin.barang-masuk.index')
            ->with('success', 'Barang masuk berhasil ditambahkan dan stok produk diperbarui');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $barangMasuk = BarangMasuk::with(['produk', 'supplier', 'admin'])->findOrFail($id);
        return view('admin.barang-masuk.show', compact('barangMasuk'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $barangMasuk = BarangMasuk::findOrFail($id);
        $produks = Produk::all();
        $suppliers = Supplier::all();
        $admins = Admin::all();

        return view('admin.barang-masuk.edit', compact('barangMasuk', 'produks', 'suppliers', 'admins'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $barangMasuk = BarangMasuk::findOrFail($id);
        $oldJumlah = $barangMasuk->jumlah;
        $oldProdukId = $barangMasuk->id_produk;

        $validated = $request->validate([
            'id_produk' => 'required|exists:produks,id_produk',
            'id_supplier' => 'required|exists:suppliers,id_supplier',
            'id_admin' => 'required|exists:admins,id_admin',
            'jumlah' => 'required|integer|min:1',
            'tanggal_masuk' => 'required|date',
        ]);

        // Update stock if product or amount changed
        if ($oldProdukId != $validated['id_produk'] || $oldJumlah != $validated['jumlah']) {
            // Restore old stock
            $oldProduk = Produk::findOrFail($oldProdukId);
            $oldProduk->decrement('stok', $oldJumlah);

            // Update new stock
            $newProduk = Produk::findOrFail($validated['id_produk']);
            $newProduk->increment('stok', $validated['jumlah']);
        }

        $barangMasuk->update($validated);

        return redirect()->route('admin.barang-masuk.index')
            ->with('success', 'Barang masuk berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $barangMasuk = BarangMasuk::findOrFail($id);
        $produk = Produk::findOrFail($barangMasuk->id_produk);

        // Restore stock
        $produk->decrement('stok', $barangMasuk->jumlah);

        $barangMasuk->delete();

        return redirect()->route('admin.barang-masuk.index')
            ->with('success', 'Barang masuk berhasil dihapus dan stok produk diperbarui');
    }
}
