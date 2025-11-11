<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\Pelanggan;
use App\Models\Diskon;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Transaksi::with(['pelanggan', 'diskon', 'detailTransaksis.produk']);

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('pelanggan', function($q) use ($search) {
                $q->where('nama_pelanggan', 'like', "%{$search}%");
            })->orWhere('id_transaksi', 'like', "%{$search}%");
        }

        // Filter by status
        if ($request->has('status_transaksi')) {
            $query->where('status_transaksi', $request->status_transaksi);
        }

        // Filter by date
        if ($request->has('tanggal_mulai')) {
            $query->where('tanggal_transaksi', '>=', $request->tanggal_mulai);
        }

        if ($request->has('tanggal_akhir')) {
            $query->where('tanggal_transaksi', '<=', $request->tanggal_akhir);
        }

        $transaksis = $query->orderBy('tanggal_transaksi', 'desc')->paginate(10);

        return view('admin.transaksi.index', compact('transaksis'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pelanggans = Pelanggan::all();
        $diskons = Diskon::where('status', 'active')->get();
        $produks = Produk::where('stok', '>', 0)->get();

        return view('admin.transaksi.create', compact('pelanggans', 'diskons', 'produks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_pelanggan' => 'required|exists:pelanggans,id_pelanggan',
            'id_diskon' => 'nullable|exists:diskons,id_diskon',
            'metode_pembayaran' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.id_produk' => 'required|exists:produks,id_produk',
            'items.*.jumlah' => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($validated) {
            $totalHarga = 0;
            $detailItems = [];

            foreach ($validated['items'] as $item) {
                $produk = Produk::findOrFail($item['id_produk']);

                if ($produk->stok < $item['jumlah']) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', "Stok produk {$produk->nama_produk} tidak mencukupi. Stok tersedia: {$produk->stok}");
                }

                $subtotal = $produk->harga * $item['jumlah'];
                $totalHarga += $subtotal;

                $detailItems[] = [
                    'produk' => $produk,
                    'jumlah' => $item['jumlah'],
                    'subtotal' => $subtotal,
                ];
            }

            $totalSetelahDiskon = $totalHarga;

            if (!empty($validated['id_diskon'])) {
                $diskon = Diskon::findOrFail($validated['id_diskon']);
                $today = now()->toDateString();
                
                if ($diskon->status !== 'active' || 
                    $today < $diskon->tanggal_mulai || 
                    $today > $diskon->tanggal_berakhir) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Diskon tidak aktif atau telah kadaluarsa');
                }

                if ($diskon->jenis_diskon === 'persentase') {
                    $totalSetelahDiskon = $totalHarga - ($totalHarga * $diskon->nilai_diskon / 100);
                } else {
                    $totalSetelahDiskon = max(0, $totalHarga - $diskon->nilai_diskon);
                }
            }

            $transaksi = Transaksi::create([
                'id_pelanggan' => $validated['id_pelanggan'],
                'id_diskon' => $validated['id_diskon'] ?? null,
                'tanggal_transaksi' => now(),
                'total_harga' => $totalHarga,
                'total_setelah_diskon' => $totalSetelahDiskon,
                'metode_pembayaran' => $validated['metode_pembayaran'],
                'status_transaksi' => 'pending',
            ]);

            foreach ($detailItems as $detailItem) {
                \App\Models\DetailTransaksi::create([
                    'id_transaksi' => $transaksi->id_transaksi,
                    'id_produk' => $detailItem['produk']->id_produk,
                    'jumlah' => $detailItem['jumlah'],
                    'subtotal' => $detailItem['subtotal'],
                ]);

                $detailItem['produk']->decrement('stok', $detailItem['jumlah']);
            }

            return redirect()->route('admin.transaksi.index')
                ->with('success', 'Transaksi berhasil dibuat');
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $transaksi = Transaksi::with([
            'pelanggan',
            'diskon',
            'detailTransaksis.produk',
            'struk'
        ])->findOrFail($id);

        return view('admin.transaksi.show', compact('transaksi'));
    }

    /**
     * Confirm payment and create struk (admin action).
     */
    public function confirmPayment(Request $request, string $id)
    {
        $transaksi = Transaksi::with('detailTransaksis.produk')->findOrFail($id);

        // Accept optional bukti upload from admin (allows attaching proof here)
        if ($request->hasFile('bukti')) {
            $request->validate([
                'bukti' => 'image|max:2048',
            ]);

            $path = $request->file('bukti')->store('payment_proofs', 'public');
            $transaksi->bukti_pembayaran = $path;
            $transaksi->save();
        }

        // Allow admin to force confirm even if bukti_pembayaran empty by checking the 'force_confirm' flag
        $forceConfirm = (bool) $request->input('force_confirm', false);

        // Only allow confirmation if payment proof exists or COD status or admin forced confirmation
        if (empty($transaksi->bukti_pembayaran) && $transaksi->metode_pembayaran !== 'cod' && !$forceConfirm) {
            return redirect()->back()->with('error', 'Bukti pembayaran belum diterima. Jika ingin memaksa konfirmasi, centang "Konfirmasi tanpa bukti" atau unggah bukti di bawah.');
        }

        // Build or update human-readable payment detail
        $metode = $transaksi->metode_pembayaran;
        $reference = 'INV' . str_pad($transaksi->id_transaksi, 6, '0', STR_PAD_LEFT);
        $detailText = '';
        switch ($metode) {
            case 'qris':
                $detailText = "QRIS - Reference: {$reference}\nTotal: Rp " . number_format((float)($transaksi->total_setelah_diskon ?? 0), 0, ',', '.');
                break;
            case 'cod':
                $detailText = "COD - Bayar di tempat\nTotal: Rp " . number_format((float)($transaksi->total_setelah_diskon ?? 0), 0, ',', '.');
                break;
            case 'cash':
                $detailText = "Tunai - Bayar tunai ke kurir/kasir\nTotal: Rp " . number_format((float)($transaksi->total_setelah_diskon ?? 0), 0, ',', '.');
                break;
            case 'dana':
            case 'ovo':
            case 'gopay':
                $detailText = strtoupper($metode) . " - Pembayaran via e-wallet\nTotal: Rp " . number_format((float)($transaksi->total_setelah_diskon ?? 0), 0, ',', '.');
                break;
            case 'bank_transfer':
                $detailText = "Transfer Bank - Silakan transfer sesuai instruksi\nTotal: Rp " . number_format((float)($transaksi->total_setelah_diskon ?? 0), 0, ',', '.');
                break;
            default:
                $detailText = "Metode: " . $metode . "\nTotal: Rp " . number_format((float)($transaksi->total_setelah_diskon ?? 0), 0, ',', '.');
        }

        // If admin uploaded bukti, include it in the detail
        if (!empty($transaksi->bukti_pembayaran)) {
            $detailText .= "\nBukti pembayaran: " . $transaksi->bukti_pembayaran;
        }

        // Create or update the struk record (avoid duplicate key errors)
        $struk = \App\Models\Struk::updateOrCreate(
            ['id_transaksi' => $transaksi->id_transaksi],
            [
                'tanggal_cetak' => now(),
                'total_harga' => $transaksi->total_setelah_diskon,
                'metode_pembayaran' => $transaksi->metode_pembayaran,
                'detail_pembayaran' => $detailText,
            ]
        );

        // After creating/updating, overwrite detail_pembayaran to exact CSV format: id_struk,id_transaksi,tanggal_cetak,total_harga,metode_pembayaran
        $tanggalCetak = $struk->tanggal_cetak instanceof \Illuminate\Support\Carbon ? $struk->tanggal_cetak->toDateTimeString() : (string)($struk->tanggal_cetak ?? now()->toDateTimeString());
        $csv = implode(',', [
            $struk->id_struk,
            $transaksi->id_transaksi,
            $tanggalCetak,
            $struk->total_harga,
            $struk->metode_pembayaran,
        ]);

        if ($struk->detail_pembayaran !== $csv) {
            $struk->detail_pembayaran = $csv;
            $struk->save();
        }

        $transaksi->update([
            'status_transaksi' => 'completed',
        ]);

        // Send notification to customer about payment confirmation and receipt availability
        // You can add email notification here if needed

        return redirect()->route('admin.transaksi.show', $transaksi->id_transaksi)
            ->with('success', 'Transaksi telah dikonfirmasi dan struk dibuat. Pelanggan dapat mengakses struk pembayaran.');
    }

    /**
     * Generate or show struk PDF (download if PDF lib available)
     */
    public function generateStruk(string $id)
    {
        $transaksi = Transaksi::with(['pelanggan', 'detailTransaksis.produk', 'struk'])->findOrFail($id);

        if (!$transaksi->struk) {
            return redirect()->back()->with('error', 'Belum ada struk untuk transaksi ini.');
        }

        // If barryvdh/laravel-dompdf is installed, generate PDF
        if (class_exists(\Barryvdh\DomPDF\Facade::class)) {
            $pdf = \PDF::loadView('admin.transaksi.struk_pdf', compact('transaksi'));
            return $pdf->download("struk_{$transaksi->id_transaksi}.pdf");
        }

        // Otherwise show HTML view and instruct to install dompdf for PDF download
        return view('admin.transaksi.struk', compact('transaksi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $transaksi = Transaksi::with('detailTransaksis.produk')->findOrFail($id);
        
        return view('admin.transaksi.edit', compact('transaksi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $transaksi = Transaksi::findOrFail($id);

        $validated = $request->validate([
            'status_transaksi' => 'required|string|in:pending,processing,completed,cancelled',
            'metode_pembayaran' => 'sometimes|required|string|max:255',
        ]);

        $transaksi->update($validated);

        return redirect()->route('admin.transaksi.index')
            ->with('success', 'Transaksi berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return DB::transaction(function () use ($id) {
            $transaksi = Transaksi::with('detailTransaksis')->findOrFail($id);

            // Restore stock if transaction is not cancelled
            if ($transaksi->status_transaksi !== 'cancelled') {
                foreach ($transaksi->detailTransaksis as $detail) {
                    Produk::where('id_produk', $detail->id_produk)
                        ->increment('stok', $detail->jumlah);
                }
            }

            $transaksi->detailTransaksis()->delete();
            $transaksi->delete();

            return redirect()->route('admin.transaksi.index')
                ->with('success', 'Transaksi berhasil dihapus');
        });
    }
}
