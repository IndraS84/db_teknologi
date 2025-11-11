<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\Keranjang;
use App\Models\Produk;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function checkout(Request $request)
    {
        try {
            DB::beginTransaction();

            $pelangganId = auth()->user()->id_pelanggan;
            $keranjangItems = Keranjang::where('id_pelanggan', $pelangganId)->get();

            if ($keranjangItems->isEmpty()) {
                return redirect()->back()->with('error', 'Keranjang kosong!');
            }

            // Hitung total
            $total = 0;
            foreach ($keranjangItems as $item) {
                $total += $item->produk->harga * $item->jumlah;
            }

            // Hitung diskon jika ada
            $totalSetelahDiskon = $total;
            $idDiskon = null;

            if ($request->has('id_diskon') && $request->id_diskon) {
                $diskon = \App\Models\Diskon::find($request->id_diskon);
                if ($diskon && $diskon->status === 'active' &&
                    now()->between($diskon->tanggal_mulai, $diskon->tanggal_berakhir)) {
                    $idDiskon = $diskon->id_diskon;
                    if ($diskon->jenis_diskon === 'persentase') {
                        $totalSetelahDiskon = $total - ($total * $diskon->nilai_diskon / 100);
                    } else {
                        $totalSetelahDiskon = max(0, $total - $diskon->nilai_diskon);
                    }
                }
            }

            // Simpan transaksi
            $transaksi = new Transaksi();
            $transaksi->id_pelanggan = $pelangganId;
            $transaksi->id_diskon = $idDiskon;
            $transaksi->total_harga = $total;
            $transaksi->total_setelah_diskon = $totalSetelahDiskon;
            $transaksi->status_transaksi = 'pending';
            $transaksi->save();

            // Simpan detail transaksi
            foreach ($keranjangItems as $item) {
                $detail = new DetailTransaksi();
                $detail->id_transaksi = $transaksi->id_transaksi;
                $detail->id_produk = $item->id_produk;
                $detail->jumlah = $item->jumlah;
                $detail->harga = $item->produk->harga;
                $detail->subtotal = $item->produk->harga * $item->jumlah;
                $detail->save();

                // Update stok produk
                $produk = Produk::find($item->id_produk);
                $produk->stok -= $item->jumlah;
                $produk->save();
            }

            // Kosongkan keranjang
            Keranjang::where('id_pelanggan', $pelangganId)->delete();

            DB::commit();

            // Generate QR Code untuk pembayaran
            $paymentData = [
                'id' => $transaksi->id_transaksi,
                'total' => $transaksi->total_setelah_diskon,
                'merchant' => 'TOKO TEKNOLOGI'
            ];
            
            $qrCode = base64_encode(QrCode::format('png')
                ->size(300)
                ->errorCorrection('H')
                ->generate(json_encode($paymentData)));

            return view('pelanggan.transaksi.payment', compact('transaksi', 'qrCode'));

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function verifyPayment($id)
    {
        try {
            $transaksi = Transaksi::findOrFail($id);
            $transaksi->status_transaksi = 'paid';
            $transaksi->tanggal_bayar = now();
            $transaksi->save();

            return redirect()->route('pelanggan.transaksi.success', $id)
                           ->with('success', 'Pembayaran berhasil dikonfirmasi');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memverifikasi pembayaran');
        }
    }

    public function success($id)
    {
        $transaksi = Transaksi::findOrFail($id);
        return view('pelanggan.transaksi.success', compact('transaksi'));
    }
}