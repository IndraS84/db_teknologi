<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran #{{ str_pad($transaksi->id_transaksi, 6, '0', STR_PAD_LEFT) }} - Toko Teknologi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">
    <style>
        .qr-container {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        .payment-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .timer {
            font-size: 28px;
            font-weight: bold;
            color: #dc3545;
            margin: 20px 0;
        }
        @media print {
            body * {
                visibility: hidden;
            }
            .qr-container, .qr-container * {
                visibility: visible;
            }
            .qr-container {
                position: absolute;
                left: 50%;
                top: 50%;
                transform: translate(-50%, -50%);
            }
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                @if(session('success'))
                    <div class="alert alert-success">
                        <i class='bx bx-check-circle'></i> {{ session('success') }}
                    </div>
                @endif

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white py-3">
                        <h4 class="mb-0">
                            <i class='bx bx-qr'></i> 
                            Pembayaran #{{ str_pad($transaksi->id_transaksi, 6, '0', STR_PAD_LEFT) }}
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="payment-info mb-4">
                            <div class="row">
                                <div class="col-sm-6">
                                    <p class="mb-1"><strong>Total Pembayaran:</strong></p>
                                    <h3 class="text-success">Rp {{ number_format($transaksi->total_setelah_diskon, 0, ',', '.') }}</h3>
                                </div>
                                <div class="col-sm-6 text-sm-end">
                                    <p class="mb-1"><strong>Batas Waktu:</strong></p>
                                    <div class="timer" id="countdown">15:00</div>
                                </div>
                            </div>
                        </div>

                        <div class="qr-container mb-4">
                            @if($transaksi->metode_pembayaran === 'qris')
                                @if(!empty($qrSvg))
                                    {{-- Render SVG directly (no imagick required) --}}
                                    <div style="max-width:300px;margin:0 auto;">
                                        {!! $qrSvg !!}
                                    </div>
                                @else
                                    <div class="alert alert-warning">QR Code tidak tersedia saat ini. Silakan hubungi admin.</div>
                                @endif

                                <div class="mt-3 text-center">
                                    <button class="btn btn-outline-success" onclick="window.print()">
                                        <i class='bx bx-printer'></i> Cetak QR Code
                                    </button>
                                </div>
                            @elseif(in_array($transaksi->metode_pembayaran, ['dana', 'ovo', 'gopay']))
                                {{-- E-wallet methods: show payment instructions --}}
                                <div class="text-center">
                                    <i class='bx bx-wallet' style="font-size: 64px; color: #28a745;"></i>
                                    <h5 class="mt-3">{{ strtoupper($transaksi->metode_pembayaran) }}</h5>
                                    <p class="text-muted">Silakan buka aplikasi {{ strtoupper($transaksi->metode_pembayaran) }} Anda</p>
                                </div>
                            @elseif($transaksi->metode_pembayaran === 'bank_transfer')
                                {{-- Bank transfer method --}}
                                <div class="text-center">
                                    <i class='bx bx-credit-card' style="font-size: 64px; color: #007bff;"></i>
                                    <h5 class="mt-3">Transfer Bank</h5>
                                    <div class="mt-4">
                                        <p><strong>Nomor Rekening:</strong> 1234567890</p>
                                        <p><strong>Bank:</strong> BCA</p>
                                        <p><strong>Atas Nama:</strong> Toko Teknologi</p>
                                    </div>
                                </div>
                            @elseif($transaksi->metode_pembayaran === 'cod')
                                {{-- COD method --}}
                                <div class="text-center">
                                    <i class='bx bx-package' style="font-size: 64px; color: #ffc107;"></i>
                                    <h5 class="mt-3">Bayar di Tempat</h5>
                                    <p class="text-muted">Pembayaran dilakukan saat barang diterima</p>
                                </div>
                            @elseif($transaksi->metode_pembayaran === 'cash')
                                {{-- Cash method --}}
                                <div class="text-center">
                                    <i class='bx bx-money' style="font-size: 64px; color: #28a745;"></i>
                                    <h5 class="mt-3">Tunai</h5>
                                    <p class="text-muted">Bayar tunai ke kasir saat pengambilan</p>
                                </div>
                            @else
                                {{-- Other methods: show the stored textual payment detail if present, otherwise show generic instructions --}}
                                @if(optional($transaksi->struk)->detail_pembayaran)
                                    <div class="text-start">
                                        <h5>Instruksi Pembayaran</h5>
                                        <pre style="white-space: pre-wrap;">{{ optional($transaksi->struk)->detail_pembayaran }}</pre>
                                    </div>
                                @else
                                    <div class="alert alert-info">Instruksi pembayaran untuk metode '{{ $transaksi->metode_pembayaran }}' akan dikirimkan. Jika perlu segera, hubungi admin.</div>
                                @endif
                            @endif
                        </div>

                        <div class="alert alert-info d-flex">
                            <i class='bx bx-info-circle fs-4 me-2'></i>
                            <div>
                                <h6 class="alert-heading">Cara Pembayaran:</h6>
                                @if($transaksi->metode_pembayaran === 'qris')
                                    <ol class="mb-0">
                                        <li>Buka aplikasi e-wallet atau m-banking Anda</li>
                                        <li>Pilih menu Scan QR atau QRIS</li>
                                        <li>Scan QR Code di atas</li>
                                        <li>Periksa detail transaksi</li>
                                        <li>Masukkan PIN dan konfirmasi pembayaran</li>
                                    </ol>
                                @elseif(in_array($transaksi->metode_pembayaran, ['dana', 'ovo', 'gopay']))
                                    <ol class="mb-0">
                                        <li>Buka aplikasi {{ strtoupper($transaksi->metode_pembayaran) }}</li>
                                        <li>Pilih menu Transfer atau Bayar</li>
                                        <li>Masukkan nominal: <strong>Rp {{ number_format($transaksi->total_setelah_diskon, 0, ',', '.') }}</strong></li>
                                        <li>Masukkan nomor referensi: <strong>{{ str_pad($transaksi->id_transaksi, 6, '0', STR_PAD_LEFT) }}</strong></li>
                                        <li>Konfirmasi pembayaran</li>
                                    </ol>
                                @elseif($transaksi->metode_pembayaran === 'bank_transfer')
                                    <ol class="mb-0">
                                        <li>Buka aplikasi m-banking atau kunjungi ATM</li>
                                        <li>Pilih menu Transfer</li>
                                        <li>Masukkan nomor rekening: <strong>1234567890 (BCA)</strong></li>
                                        <li>Masukkan nominal: <strong>Rp {{ number_format($transaksi->total_setelah_diskon, 0, ',', '.') }}</strong></li>
                                        <li>Konfirmasi transfer</li>
                                    </ol>
                                @elseif($transaksi->metode_pembayaran === 'cod')
                                    <ol class="mb-0">
                                        <li>Tunggu kurir datang ke alamat Anda</li>
                                        <li>Periksa kondisi barang</li>
                                        <li>Bayar sesuai nominal yang tertera</li>
                                        <li>Terima struk pembayaran</li>
                                    </ol>
                                @elseif($transaksi->metode_pembayaran === 'cash')
                                    <ol class="mb-0">
                                        <li>Datang ke toko atau tempat pengambilan</li>
                                        <li>Tunjukkan nomor transaksi: <strong>{{ str_pad($transaksi->id_transaksi, 6, '0', STR_PAD_LEFT) }}</strong></li>
                                        <li>Bayar tunai ke kasir</li>
                                        <li>Terima struk dan barang</li>
                                    </ol>
                                @else
                                    <p class="mb-0">Silakan ikuti instruksi yang diberikan untuk metode pembayaran ini.</p>
                                @endif
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <a href="{{ route('pelanggan.dashboard') }}" class="btn btn-outline-secondary">
                                <i class='bx bx-arrow-back'></i> Kembali ke Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function startTimer(duration, display) {
            var timer = duration, minutes, seconds;
            var countdown = setInterval(function () {
                minutes = parseInt(timer / 60, 10);
                seconds = parseInt(timer % 60, 10);

                minutes = minutes < 10 ? "0" + minutes : minutes;
                seconds = seconds < 10 ? "0" + seconds : seconds;

                display.textContent = minutes + ":" + seconds;

                if (--timer < 0) {
                    clearInterval(countdown);
                    display.textContent = "Waktu Habis!";
                    display.style.color = "#dc3545";
                }
            }, 1000);
        }

        window.onload = function () {
            var fifteenMinutes = 60 * 15,
                display = document.querySelector('#countdown');
            startTimer(fifteenMinutes, display);
        };
    </script>
</body>
</html>