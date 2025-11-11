<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BarangKeluar;
use App\Models\Produk;
use App\Models\Pelanggan;
use App\Models\Admin;
use Illuminate\Http\Request;

class BarangKeluarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = BarangKeluar::with(['produk', 'pelanggan', 'admin']);

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('produk', function($q) use ($search) {
                $q->where('nama_produk', 'like', "%{$search}%");
            })->orWhereHas('pelanggan', function($q) use ($search) {
                $q->where('nama_pelanggan', 'like', "%{$search}%");
            });
        }

        // Filter by date
        if ($request->has('tanggal_mulai')) {
            $query->where('tanggal_keluar', '>=', $request->tanggal_mulai);
        }

        if ($request->has('tanggal_akhir')) {
            $query->where('tanggal_keluar', '<=', $request->tanggal_akhir);
        }

        $barangKeluars = $query->orderBy('tanggal_keluar', 'desc')->paginate(10);

        return view('admin.barang-keluar.index', compact('barangKeluars'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $produks = Produk::where('stok', '>', 0)->get();
        $pelanggans = Pelanggan::all();
        $admins = Admin::all();

        return view('admin.barang-keluar.create', compact('produks', 'pelanggans', 'admins'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_produk' => 'required|exists:produks,id_produk',
            'id_pelanggan' => 'required|exists:pelanggans,id_pelanggan',
            'id_admin' => 'required|exists:admins,id_admin',
            'jumlah' => 'required|integer|min:1',
            'tanggal_keluar' => 'required|date',
        ]);

        // Check stock availability
        $produk = Produk::findOrFail($validated['id_produk']);
        if ($produk->stok < $validated['jumlah']) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Stok produk tidak mencukupi. Stok tersedia: ' . $produk->stok);
        }

        // Create barang keluar
        BarangKeluar::create($validated);

        // Update product stock
        $produk->decrement('stok', $validated['jumlah']);

        return redirect()->route('admin.barang-keluar.index')
            ->with('success', 'Barang keluar berhasil ditambahkan dan stok produk diperbarui');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $barangKeluar = BarangKeluar::with(['produk', 'pelanggan', 'admin'])->findOrFail($id);
        return view('admin.barang-keluar.show', compact('barangKeluar'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $barangKeluar = BarangKeluar::findOrFail($id);
        $produks = Produk::all();
        $pelanggans = Pelanggan::all();
        $admins = Admin::all();

        return view('admin.barang-keluar.edit', compact('barangKeluar', 'produks', 'pelanggans', 'admins'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $barangKeluar = BarangKeluar::findOrFail($id);
        $oldJumlah = $barangKeluar->jumlah;
        $oldProdukId = $barangKeluar->id_produk;

        $validated = $request->validate([
            'id_produk' => 'required|exists:produks,id_produk',
            'id_pelanggan' => 'required|exists:pelanggans,id_pelanggan',
            'id_admin' => 'required|exists:admins,id_admin',
            'jumlah' => 'required|integer|min:1',
            'tanggal_keluar' => 'required|date',
        ]);

        // Update stock if product or amount changed
        if ($oldProdukId != $validated['id_produk'] || $oldJumlah != $validated['jumlah']) {
            // Check new stock availability
            $newProduk = Produk::findOrFail($validated['id_produk']);
            $currentStock = $newProduk->stok;
            
            if ($oldProdukId == $validated['id_produk']) {
                // Same product, adjust stock
                $stockDifference = $validated['jumlah'] - $oldJumlah;
                if ($currentStock < $stockDifference) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Stok produk tidak mencukupi');
                }
                $newProduk->decrement('stok', $stockDifference);
            } else {
                // Different product
                if ($currentStock < $validated['jumlah']) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Stok produk tidak mencukupi');
                }
                // Restore old stock
                $oldProduk = Produk::findOrFail($oldProdukId);
                $oldProduk->increment('stok', $oldJumlah);
                // Update new stock
                $newProduk->decrement('stok', $validated['jumlah']);
            }
        }

        $barangKeluar->update($validated);

        return redirect()->route('admin.barang-keluar.index')
            ->with('success', 'Barang keluar berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $barangKeluar = BarangKeluar::findOrFail($id);
        $produk = Produk::findOrFail($barangKeluar->id_produk);

        // Restore stock
        $produk->increment('stok', $barangKeluar->jumlah);

        $barangKeluar->delete();

        return redirect()->route('admin.barang-keluar.index')
            ->with('success', 'Barang keluar berhasil dihapus dan stok produk diperbarui');
    }
}
