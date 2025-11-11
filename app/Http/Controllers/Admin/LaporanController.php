<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\Admin;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Laporan::with('admin');

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('periode', 'like', "%{$search}%")
                  ->orWhereHas('admin', function($q) use ($search) {
                      $q->where('nama_admin', 'like', "%{$search}%");
                  });
        }

        // Filter by date
        if ($request->has('tanggal_mulai')) {
            $query->where('tanggal_cetak', '>=', $request->tanggal_mulai);
        }

        if ($request->has('tanggal_akhir')) {
            $query->where('tanggal_cetak', '<=', $request->tanggal_akhir);
        }

        $laporans = $query->orderBy('tanggal_cetak', 'desc')->paginate(10);

        return view('admin.laporan.index', compact('laporans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $admins = Admin::all();
        return view('admin.laporan.create', compact('admins'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_admin' => 'required|exists:admins,id_admin',
            'periode' => 'required|string|max:255',
            'tanggal_cetak' => 'required|date',
        ]);

        // Calculate total penjualan based on periode
        $totalPenjualan = 0;
        
        if ($validated['periode'] === 'bulan ini') {
            $totalPenjualan = Transaksi::whereMonth('tanggal_transaksi', now()->month)
                ->whereYear('tanggal_transaksi', now()->year)
                ->sum('total_setelah_diskon');
        } elseif ($validated['periode'] === 'tahun ini') {
            $totalPenjualan = Transaksi::whereYear('tanggal_transaksi', now()->year)
                ->sum('total_setelah_diskon');
        } elseif ($validated['periode'] === 'semua') {
            $totalPenjualan = Transaksi::sum('total_setelah_diskon');
        } else {
            // Custom periode calculation
            $totalPenjualan = Transaksi::sum('total_setelah_diskon');
        }

        $validated['total_penjualan'] = $totalPenjualan;

        Laporan::create($validated);

        return redirect()->route('admin.laporan.index')
            ->with('success', 'Laporan berhasil dibuat');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $laporan = Laporan::with('admin')->findOrFail($id);
        return view('admin.laporan.show', compact('laporan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $laporan = Laporan::findOrFail($id);
        $admins = Admin::all();
        
        return view('admin.laporan.edit', compact('laporan', 'admins'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $laporan = Laporan::findOrFail($id);

        $validated = $request->validate([
            'id_admin' => 'required|exists:admins,id_admin',
            'periode' => 'required|string|max:255',
            'tanggal_cetak' => 'required|date',
            'total_penjualan' => 'required|numeric|min:0',
        ]);

        $laporan->update($validated);

        return redirect()->route('admin.laporan.index')
            ->with('success', 'Laporan berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $laporan = Laporan::findOrFail($id);
        $laporan->delete();

        return redirect()->route('admin.laporan.index')
            ->with('success', 'Laporan berhasil dihapus');
    }
}
