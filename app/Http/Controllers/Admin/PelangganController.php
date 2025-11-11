<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PelangganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pelanggan::query();

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('nama_pelanggan', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%");
        }

        $pelanggans = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.pelanggan.index', compact('pelanggans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pelanggan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'alamat' => 'required|string',
            'no_hp' => 'required|string|max:20',
            'email' => 'required|email|max:255|unique:pelanggans,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        Pelanggan::create($validated);

        return redirect()->route('admin.pelanggan.index')
            ->with('success', 'Pelanggan berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pelanggan = Pelanggan::with(['transaksis', 'keranjangs'])->findOrFail($id);
        return view('admin.pelanggan.show', compact('pelanggan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pelanggan = Pelanggan::findOrFail($id);
        return view('admin.pelanggan.edit', compact('pelanggan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $pelanggan = Pelanggan::findOrFail($id);

        $validated = $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'alamat' => 'required|string',
            'no_hp' => 'required|string|max:20',
            'email' => 'required|email|max:255|unique:pelanggans,email,' . $id . ',id_pelanggan',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $pelanggan->update($validated);

        return redirect()->route('admin.pelanggan.index')
            ->with('success', 'Pelanggan berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pelanggan = Pelanggan::findOrFail($id);

        // Check if pelanggan has transactions
        if ($pelanggan->transaksis()->count() > 0) {
            return redirect()->route('admin.pelanggan.index')
                ->with('error', 'Pelanggan tidak dapat dihapus karena masih memiliki transaksi');
        }

        $pelanggan->delete();

        return redirect()->route('admin.pelanggan.index')
            ->with('success', 'Pelanggan berhasil dihapus');
    }
}
