<?php

namespace App\Http\Controllers;

use App\Models\Keranjang;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\Struk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;

class CheckoutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // pelanggan must be authenticated
    }

    // Show the checkout page (summary of cart and payment methods)
    public function index()
    {
        $pelanggan = Auth::user();
        $keranjangs = Keranjang::with('produk')->where('id_pelanggan', $pelanggan->id_pelanggan)->get();

        // Set default image URLs for products without images
        $keranjangs->each(function ($item) {
            if (!$item->produk->gambar) {
                $images = [
                    'laptop.jpg' => ['Laptop', 'Notebook'],
                    'smartphone.jpg' => ['HP', 'Smartphone', 'Handphone', 'Phone'],
                    'headphone.jpg' => ['Headphone', 'Earphone', 'TWS'],
                    'printer.jpg' => ['Printer', 'Scanner'],
                    'monitor.jpg' => ['Monitor', 'Display'],
                    'keyboard.jpg' => ['Keyboard', 'Mouse'],
                    'default.jpg' => ['Other']
                ];

                $selectedImage = 'default.jpg';
                foreach ($images as $image => $keywords) {
                    if (Str::contains(strtolower($item->produk->nama_produk), array_map('strtolower', $keywords))) {
                        $selectedImage = $image;
                        break;
                    }
                }

                $item->produk->default_image = asset('images/products/' . $selectedImage);
            }
        });

        $total = $keranjangs->sum('subtotal');

        return view('pelanggan.checkout.index', compact('keranjangs', 'total'));
    }

    // Process checkout and create transaksi + detail transaksis
    public function process(Request $request)
    {
        $pelanggan = Auth::user();
        $keranjangs = Keranjang::with('produk')->where('id_pelanggan', $pelanggan->id_pelanggan)->get();

        if ($keranjangs->isEmpty()) {
            return redirect()->back()->with('error', 'Keranjang kosong');
        }

        $request->validate([
            'metode_pembayaran' => 'required|string|in:qris,cod,cash,dana,ovo,gopay,bank_transfer',
            'id_diskon' => 'nullable|exists:diskons,id_diskon',
        ]);

        $metode = $request->input('metode_pembayaran', 'qris');
        $idDiskon = $request->input('id_diskon');

        return DB::transaction(function () use ($pelanggan, $keranjangs, $metode, $idDiskon) {
            $totalHarga = $keranjangs->sum('subtotal');

            // Hitung diskon jika ada
            $totalSetelahDiskon = $totalHarga;
            if ($idDiskon) {
                $diskon = \App\Models\Diskon::find($idDiskon);
                if ($diskon && $diskon->status === 'active' &&
                    now()->between($diskon->tanggal_mulai, $diskon->tanggal_berakhir)) {
                    if ($diskon->jenis_diskon === 'persentase') {
                        $totalSetelahDiskon = $totalHarga - ($totalHarga * $diskon->nilai_diskon / 100);
                    } else {
                        $totalSetelahDiskon = max(0, $totalHarga - $diskon->nilai_diskon);
                    }
                } else {
                    $idDiskon = null; // Reset if invalid
                }
            }

            $kodeUnik = rand(100, 999); // Generate kode unik untuk identifikasi pembayaran
            $totalPembayaran = $totalSetelahDiskon + $kodeUnik;

            $transaksi = Transaksi::create([
                'id_pelanggan' => $pelanggan->id_pelanggan,
                'id_diskon' => $idDiskon,
                'tanggal_transaksi' => now(),
                'total_harga' => $totalHarga,
                'total_setelah_diskon' => $totalPembayaran,
                'metode_pembayaran' => $metode,
                'status_transaksi' => 'menunggu_pembayaran',
            ]);

            foreach ($keranjangs as $item) {
                DetailTransaksi::create([
                    'id_transaksi' => $transaksi->id_transaksi,
                    'id_produk' => $item->id_produk,
                    'jumlah' => $item->jumlah,
                    'subtotal' => $item->subtotal,
                ]);

                $item->produk->decrement('stok', $item->jumlah);
            }

            // Build human-readable payment detail to store in struk
            $reference = 'INV' . str_pad($transaksi->id_transaksi, 6, '0', STR_PAD_LEFT);
            $detailText = '';
            switch ($metode) {
                case 'qris':
                    $detailText = "QRIS - Reference: {$reference}\nTotal: Rp " . number_format((float)($totalPembayaran ?? 0), 0, ',', '.');
                    break;
                case 'cod':
                    $detailText = "COD - Bayar di tempat (siapkan uang pas atau kembalian)\nTotal: Rp " . number_format((float)($totalPembayaran ?? 0), 0, ',', '.');
                    break;
                case 'cash':
                    $detailText = "Tunai - Silakan bayar tunai ke kasir saat pengambilan/antar.\nTotal: Rp " . number_format((float)($totalPembayaran ?? 0), 0, ',', '.');
                    break;
                case 'dana':
                case 'ovo':
                case 'gopay':
                    $detailText = strtoupper($metode) . " - Silakan lakukan pembayaran melalui aplikasi e-wallet Anda.\nTotal: Rp " . number_format((float)($totalPembayaran ?? 0), 0, ',', '.');
                    break;
                case 'bank_transfer':
                    $detailText = "Transfer Bank - Silakan transfer ke rekening kami (nomor rekening tertera pada invoice).\nTotal: Rp " . number_format((float)($totalPembayaran ?? 0), 0, ',', '.');
                    break;
                default:
                    $detailText = "Metode: " . $metode . "\nTotal: Rp " . number_format((float)($totalPembayaran ?? 0), 0, ',', '.');
            }

            // Create struk; use create-or-update logic to avoid duplicate key errors in case admin also creates/updates later
            $struk = Struk::firstOrCreate(
                ['id_transaksi' => $transaksi->id_transaksi],
                [
                    'tanggal_cetak' => now(),
                    'total_harga' => $totalPembayaran,
                    'metode_pembayaran' => $metode,
                    'detail_pembayaran' => $detailText,
                ]
            );

            // Ensure detail_pembayaran follows exact CSV format: id_struk,id_transaksi,tanggal_cetak,total_harga,metode_pembayaran
            // Some DB drivers may return string for datetime; normalize using the value or now()
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

            // Clear cart
            Keranjang::where('id_pelanggan', $pelanggan->id_pelanggan)->delete();

            // Redirect to payment instructions page where QR will be generated as SVG (no imagick required)
            return redirect()->route('pelanggan.transaksi.payment', $transaksi->id_transaksi)
                ->with('success', 'Transaksi berhasil dibuat. Silakan ikuti instruksi pembayaran pada halaman berikut.');
        });
    }

    // Show payment instruction and upload form
    public function paymentInstructions($id)
    {
        $transaksi = Transaksi::with('detailTransaksis.produk')->findOrFail($id);
        if ($transaksi->id_pelanggan !== Auth::id()) {
            abort(403);
        }

        // Prepare QR data and generate SVG QR (SVG does not require imagick)
        $qrData = [
            'id' => $transaksi->id_transaksi,
            'amount' => $transaksi->total_setelah_diskon,
            'merchant' => 'Toko Teknologi',
            'reference' => 'INV' . str_pad($transaksi->id_transaksi, 6, '0', STR_PAD_LEFT)
        ];

        // generate SVG QR string
        try {
            $qrSvg = QrCode::size(300)
                ->format('svg')
                ->errorCorrection('H')
                ->generate(json_encode($qrData));
        } catch (\Exception $e) {
            // fallback: empty svg string and log the error (laravel log will capture)
            $qrSvg = '';
        }

        return view('pelanggan.transaksi.payment', compact('transaksi', 'qrSvg'));
    }

    // Upload payment proof
    public function uploadProof(Request $request, $id)
    {
        $transaksi = Transaksi::findOrFail($id);
        if ($transaksi->id_pelanggan !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'bukti' => 'required|image|max:2048',
        ]);

        $path = $request->file('bukti')->store('payment_proofs', 'public');

        $transaksi->update([
            'bukti_pembayaran' => $path,
            'status_transaksi' => 'payment_uploaded',
        ]);

        return redirect()->route('pelanggan.transaksi.show', $transaksi->id_transaksi)
            ->with('success', 'Bukti pembayaran berhasil diunggah. Tunggu konfirmasi admin.');
    }

    // Show transaksi for pelanggan
    public function show($id)
    {
        $transaksi = Transaksi::with(['detailTransaksis.produk', 'pelanggan', 'struk'])->findOrFail($id);
        if ($transaksi->id_pelanggan !== Auth::id()) {
            abort(403);
        }

        return view('pelanggan.transaksi.show', compact('transaksi'));
    }

    // List all transaksi for the authenticated pelanggan
    public function transactions()
    {
        $pelangganId = Auth::id();
        $transaksis = Transaksi::with(['detailTransaksis.produk', 'struk'])
            ->where('id_pelanggan', $pelangganId)
            ->orderBy('tanggal_transaksi', 'desc')
            ->get();

        return view('pelanggan.transaksi.index', compact('transaksis'));
    }

    // Show/download struk for pelanggan
    public function showStruk($id)
    {
        $transaksi = Transaksi::with(['pelanggan', 'detailTransaksis.produk', 'struk'])->findOrFail($id);
        if ($transaksi->id_pelanggan !== Auth::id()) {
            abort(403);
        }

        if (!$transaksi->struk) {
            return redirect()->back()->with('error', 'Belum ada struk untuk transaksi ini.');
        }

        // Prepare QR data for verification (only for QRIS payments)
        $qrSvg = '';
        if ($transaksi->metode_pembayaran === 'qris') {
            $qrData = [
                'id' => $transaksi->id_transaksi,
                'amount' => $transaksi->struk->total_harga,
                'merchant' => 'Toko Teknologi',
                'reference' => 'INV' . str_pad($transaksi->id_transaksi, 6, '0', STR_PAD_LEFT),
                'verified' => true
            ];

            try {
                $qrSvg = QrCode::size(150)
                    ->format('svg')
                    ->errorCorrection('H')
                    ->generate(json_encode($qrData));
            } catch (\Exception $e) {
                $qrSvg = '';
            }
        }

        if (class_exists(\Barryvdh\DomPDF\Facade::class)) {
            $pdf = \PDF::loadView('pelanggan.transaksi.struk', compact('transaksi', 'qrSvg'));
            return $pdf->download("struk_{$transaksi->id_transaksi}.pdf");
        }

        return view('pelanggan.transaksi.struk', compact('transaksi', 'qrSvg'));
    }
}
