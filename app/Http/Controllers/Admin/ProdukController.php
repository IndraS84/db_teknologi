<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\Admin;
use App\Models\Supplier;
use App\Models\Diskon;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Produk::with(['admin', 'supplier', 'diskon']);

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('nama_produk', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
        }

        // Filter by supplier
        if ($request->has('id_supplier')) {
            $query->where('id_supplier', $request->id_supplier);
        }

        // Filter by stok
        if ($request->has('stok_min')) {
            $query->where('stok', '>=', $request->stok_min);
        }

        $produks = $query->orderBy('created_at', 'desc')->paginate(10);
        $suppliers = Supplier::all();

        return view('admin.produk.index', compact('produks', 'suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $admins = Admin::all();
        $suppliers = Supplier::all();
        $diskons = Diskon::where('status', 'active')->get();

        return view('admin.produk.create', compact('admins', 'suppliers', 'diskons'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
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

        Produk::create($validated);

        return redirect()->route('admin.produk.index')
            ->with('success', 'Produk berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $produk = Produk::with(['admin', 'supplier', 'diskon', 'barangMasuks', 'barangKeluars'])
            ->findOrFail($id);
        return view('admin.produk.show', compact('produk'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $produk = Produk::findOrFail($id);
        $admins = Admin::all();
        $suppliers = Supplier::all();
        $diskons = Diskon::where('status', 'active')->get();

        return view('admin.produk.edit', compact('produk', 'admins', 'suppliers', 'diskons'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $produk = Produk::findOrFail($id);

        $validated = $request->validate([
            'nama_produk' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'id_admin' => 'required|exists:admins,id_admin',
            'id_supplier' => 'required|exists:suppliers,id_supplier',
            'id_diskon' => 'nullable|exists:diskons,id_diskon',
        ]);

        $produk->update($validated);

        return redirect()->route('admin.produk.index')
            ->with('success', 'Produk berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $produk = Produk::findOrFail($id);

        // Check if produk has transactions or cart items
        if ($produk->detailTransaksis()->count() > 0 || $produk->keranjangs()->count() > 0) {
            return redirect()->route('admin.produk.index')
                ->with('error', 'Produk tidak dapat dihapus karena masih digunakan dalam transaksi atau keranjang');
        }

        $produk->delete();

        return redirect()->route('admin.produk.index')
            ->with('success', 'Produk berhasil dihapus');
    }
}
