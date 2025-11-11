<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DetailTransaksi;
use App\Models\Keranjang;
use App\Models\Produk;
use App\Models\Transaksi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Transaksi::with(['pelanggan', 'diskon', 'detailTransaksis.produk']);

        // Filter by pelanggan
        if ($request->has('id_pelanggan')) {
            $query->where('id_pelanggan', $request->id_pelanggan);
        }

        // Filter by status
        if ($request->has('status_transaksi')) {
            $query->where('status_transaksi', $request->status_transaksi);
        }

        // Filter by date range
        if ($request->has('tanggal_mulai')) {
            $query->where('tanggal_transaksi', '>=', $request->tanggal_mulai);
        }

        if ($request->has('tanggal_akhir')) {
            $query->where('tanggal_transaksi', '<=', $request->tanggal_akhir);
        }

        $transaksis = $query->orderBy('tanggal_transaksi', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $transaksis,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
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
            // Calculate total
            $totalHarga = 0;
            $detailItems = [];

            foreach ($validated['items'] as $item) {
                $produk = Produk::findOrFail($item['id_produk']);

                // Check stock availability
                if ($produk->stok < $item['jumlah']) {
                    return response()->json([
                        'success' => false,
                        'message' => "Stok produk {$produk->nama_produk} tidak mencukupi. Stok tersedia: {$produk->stok}",
                    ], 400);
                }

                $subtotal = $produk->harga * $item['jumlah'];
                $totalHarga += $subtotal;

                $detailItems[] = [
                    'produk' => $produk,
                    'jumlah' => $item['jumlah'],
                    'subtotal' => $subtotal,
                ];
            }

            // Calculate discount if provided
            $totalSetelahDiskon = $totalHarga;
            $diskon = null;

            if (!empty($validated['id_diskon'])) {
                $diskon = \App\Models\Diskon::findOrFail($validated['id_diskon']);
                
                // Check if discount is active
                $today = now()->toDateString();
                if ($diskon->status !== 'active' || 
                    $today < $diskon->tanggal_mulai || 
                    $today > $diskon->tanggal_berakhir) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Diskon tidak aktif atau telah kadaluarsa',
                    ], 400);
                }

                // Apply discount based on jenis_diskon
                if ($diskon->jenis_diskon === 'persentase') {
                    $totalSetelahDiskon = $totalHarga - ($totalHarga * $diskon->nilai_diskon / 100);
                } else {
                    // Fixed amount
                    $totalSetelahDiskon = max(0, $totalHarga - $diskon->nilai_diskon);
                }
            }

            // Create transaction
            $transaksi = Transaksi::create([
                'id_pelanggan' => $validated['id_pelanggan'],
                'id_diskon' => $validated['id_diskon'] ?? null,
                'tanggal_transaksi' => now(),
                'total_harga' => $totalHarga,
                'total_setelah_diskon' => $totalSetelahDiskon,
                'metode_pembayaran' => $validated['metode_pembayaran'],
                'status_transaksi' => 'pending',
            ]);

            // Create detail transactions and update stock
            foreach ($detailItems as $detailItem) {
                DetailTransaksi::create([
                    'id_transaksi' => $transaksi->id_transaksi,
                    'id_produk' => $detailItem['produk']->id_produk,
                    'jumlah' => $detailItem['jumlah'],
                    'subtotal' => $detailItem['subtotal'],
                ]);

                // Update product stock
                $detailItem['produk']->decrement('stok', $detailItem['jumlah']);
            }

            // Clear cart for this pelanggan if items come from cart
            // Keranjang::where('id_pelanggan', $validated['id_pelanggan'])->delete();

            $transaksi->load(['pelanggan', 'diskon', 'detailTransaksis.produk']);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dibuat',
                'data' => $transaksi,
            ], 201);
        });
    }

    /**
     * Create transaction from cart.
     */
    public function storeFromCart(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id_pelanggan' => 'required|exists:pelanggans,id_pelanggan',
            'id_diskon' => 'nullable|exists:diskons,id_diskon',
            'metode_pembayaran' => 'required|string|max:255',
        ]);

        // Get cart items
        $keranjangs = Keranjang::with('produk')
            ->where('id_pelanggan', $validated['id_pelanggan'])
            ->get();

        if ($keranjangs->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang kosong',
            ], 400);
        }

        // Prepare items for transaction
        $items = $keranjangs->map(function ($keranjang) {
            return [
                'id_produk' => $keranjang->id_produk,
                'jumlah' => $keranjang->jumlah,
            ];
        })->toArray();

        $request->merge(['items' => $items]);
        
        // Create transaction
        $transaksi = $this->store($request);
        
        // Clear cart after successful transaction
        if ($transaksi->getStatusCode() === 201) {
            Keranjang::where('id_pelanggan', $validated['id_pelanggan'])->delete();
        }

        return $transaksi;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $transaksi = Transaksi::with([
            'pelanggan',
            'diskon',
            'detailTransaksis.produk.admin',
            'detailTransaksis.produk.supplier',
            'struk'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $transaksi,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $transaksi = Transaksi::findOrFail($id);

        $validated = $request->validate([
            'status_transaksi' => 'sometimes|required|string|in:pending,processing,completed,cancelled',
            'metode_pembayaran' => 'sometimes|required|string|max:255',
        ]);

        $transaksi->update($validated);
        $transaksi->load(['pelanggan', 'diskon', 'detailTransaksis.produk']);

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil diupdate',
            'data' => $transaksi,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        return DB::transaction(function () use ($id) {
            $transaksi = Transaksi::with('detailTransaksis')->findOrFail($id);

            // Restore stock if transaction is cancelled
            if ($transaksi->status_transaksi !== 'cancelled') {
                foreach ($transaksi->detailTransaksis as $detail) {
                    Produk::where('id_produk', $detail->id_produk)
                        ->increment('stok', $detail->jumlah);
                }
            }

            // Delete detail transactions
            $transaksi->detailTransaksis()->delete();

            // Delete transaction
            $transaksi->delete();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dihapus',
            ]);
        });
    }
}
