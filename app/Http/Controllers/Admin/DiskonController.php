<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Diskon;
use Illuminate\Http\Request;

class DiskonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Diskon::query();

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('nama_diskon', 'like', "%{$search}%")
                  ->orWhere('jenis_diskon', 'like', "%{$search}%");
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $diskons = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.diskon.index', compact('diskons'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.diskon.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_diskon' => 'required|string|max:255',
            'jenis_diskon' => 'required|string|in:persentase,fixed',
            'nilai_diskon' => 'required|numeric|min:0',
            'tanggal_mulai' => 'required|date',
            'tanggal_berakhir' => 'required|date|after:tanggal_mulai',
            'status' => 'required|string|in:active,inactive',
            'keterangan' => 'nullable|string',
        ]);

        Diskon::create($validated);

        return redirect()->route('admin.diskon.index')
            ->with('success', 'Diskon berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $diskon = Diskon::with(['produks', 'transaksis'])->findOrFail($id);
        return view('admin.diskon.show', compact('diskon'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $diskon = Diskon::findOrFail($id);
        return view('admin.diskon.edit', compact('diskon'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $diskon = Diskon::findOrFail($id);

        $validated = $request->validate([
            'nama_diskon' => 'required|string|max:255',
            'jenis_diskon' => 'required|string|in:persentase,fixed',
            'nilai_diskon' => 'required|numeric|min:0',
            'tanggal_mulai' => 'required|date',
            'tanggal_berakhir' => 'required|date|after:tanggal_mulai',
            'status' => 'required|string|in:active,inactive',
            'keterangan' => 'nullable|string',
        ]);

        $diskon->update($validated);

        return redirect()->route('admin.diskon.index')
            ->with('success', 'Diskon berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $diskon = Diskon::findOrFail($id);

        // Check if diskon has products or transactions
        if ($diskon->produks()->count() > 0 || $diskon->transaksis()->count() > 0) {
            return redirect()->route('admin.diskon.index')
                ->with('error', 'Diskon tidak dapat dihapus karena masih digunakan');
        }

        $diskon->delete();

        return redirect()->route('admin.diskon.index')
            ->with('success', 'Diskon berhasil dihapus');
    }
}
