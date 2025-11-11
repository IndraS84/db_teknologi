<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembayaran - Toko Teknologi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">
    <style>
        .struk-container {
            max-width: 400px;
            margin: 0 auto;
            background: white;
            border: 1px solid #dee2e6;
            padding: 20px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.4;
        }
        .struk-header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .struk-header h3 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
        }
        .struk-body {
            margin-bottom: 15px;
        }
        .struk-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            padding: 2px 0;
        }
        .struk-total {
            border-top: 1px dashed #000;
            padding-top: 10px;
            margin-top: 10px;
            font-weight: bold;
        }
        .struk-footer {
            text-align: center;
            border-top: 1px dashed #000;
            padding-top: 10px;
            margin-top: 15px;
            font-size: 10px;
        }
        .struk-qr {
            text-align: center;
            margin: 15px 0;
        }
        @media print {
            body * {
                visibility: hidden;
            }
            .struk-container, .struk-container * {
                visibility: visible;
            }
            .struk-container {
                position: absolute;
                left: 50%;
                top: 0;
                transform: translateX(-50%);
                box-shadow: none;
                border: none;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="row">
            <div class="col-12">
                <div class="text-center mb-4 no-print">
                    <button class="btn btn-outline-primary me-2" onclick="window.print()">
                        <i class="bx bx-printer"></i> Cetak Struk
                    </button>
                    <a href="{{ route('pelanggan.transaksi.show', $transaksi->id_transaksi) }}" class="btn btn-secondary">
                        <i class="bx bx-arrow-back"></i> Kembali
                    </a>
                </div>

                <div class="struk-container">
                    <div class="struk-header">
                        <h3>TOKO TEKNOLOGI</h3>
                        <p class="mb-1">Jl. Teknologi No. 123</p>
                        <p class="mb-1">Telp: (021) 12345678</p>
                        <p class="mb-0">Email: info@tokoteknologi.com</p>
                    </div>

                    <div class="struk-body">
                        <div class="struk-item">
                            <span>No. Transaksi:</span>
                            <span>#{{ str_pad($transaksi->id_transaksi, 6, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="struk-item">
                            <span>Tanggal:</span>
                            <span>{{ $transaksi->struk->tanggal_cetak->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="struk-item">
                            <span>Pelanggan:</span>
                            <span>{{ $transaksi->pelanggan->nama_pelanggan }}</span>
                        </div>
                        <div class="struk-item">
                            <span>Metode Bayar:</span>
                            <span>{{ strtoupper(str_replace('_', ' ', $transaksi->metode_pembayaran)) }}</span>
                        </div>

                        <hr style="border: 1px dashed #000; margin: 10px 0;">

                        @foreach($transaksi->detailTransaksis as $detail)
                        <div class="struk-item">
                            <span>{{ Str::limit($detail->produk->nama_produk, 20) }}</span>
                            <span>{{ $detail->jumlah }}x</span>
                        </div>
                        <div class="struk-item" style="padding-left: 10px;">
                            <span>@ Rp {{ number_format($detail->produk->harga, 0, ',', '.') }}</span>
                            <span>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</span>
                        </div>
                        @endforeach

                        <div class="struk-total">
                            <div class="struk-item">
                                <span>TOTAL:</span>
                                <span>Rp {{ number_format($transaksi->struk->total_harga, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="struk-item">
                            <span>Status:</span>
                            <span>LUNAS</span>
                        </div>
                    </div>

                    <div class="struk-qr">
                        @if($transaksi->metode_pembayaran === 'qris' && !empty($qrSvg ?? ''))
                            <div style="max-width:150px;margin:0 auto;">
                                {!! $qrSvg !!}
                            </div>
                            <small>Scan untuk verifikasi</small>
                        @else
                            <div style="width: 100px; height: 100px; border: 1px solid #000; margin: 0 auto; display: flex; align-items: center; justify-content: center; font-size: 8px;">
                                VERIFIED
                            </div>
                        @endif
                    </div>

                    <div class="struk-footer">
                        <p class="mb-1">Terima Kasih Atas Kunjungan Anda</p>
                        <p class="mb-1">Barang yang sudah dibeli tidak dapat dikembalikan</p>
                        <p class="mb-0">Struk ini adalah bukti pembayaran yang sah</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
