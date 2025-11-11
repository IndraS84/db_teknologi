<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi Berhasil #{{ str_pad($transaksi->id_transaksi, 6, '0', STR_PAD_LEFT) }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">
    <style>
        .success-checkmark {
            width: 80px;
            height: 80px;
            margin: 0 auto;
            color: #198754;
        }
        .transaction-details {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            .transaction-details {
                border: 1px solid #dee2e6;
            }
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card shadow-sm">
                    <div class="card-body text-center p-5">
                        <div class="success-checkmark mb-4">
                            <i class="bx bx-check-circle" style="font-size: 80px;"></i>
                        </div>
                        <h2 class="mb-4">Transaksi Berhasil!</h2>
                        
                        <div class="transaction-details mb-4 text-start">
                            <h5 class="border-bottom pb-2 mb-3">Detail Transaksi</h5>
                            <div class="row mb-2">
                                <div class="col-6">ID Transaksi:</div>
                                <div class="col-6 text-end">#{{ str_pad($transaksi->id_transaksi, 6, '0', STR_PAD_LEFT) }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6">Tanggal:</div>
                                <div class="col-6 text-end">{{ $transaksi->tanggal_bayar->format('d M Y H:i') }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6">Status:</div>
                                <div class="col-6 text-end">
                                    <span class="badge bg-success">Lunas</span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6">Total Pembayaran:</div>
                                <div class="col-6 text-end fw-bold">
                                    Rp {{ number_format($transaksi->total_setelah_diskon, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-center no-print">
                            <button class="btn btn-outline-success me-md-2" onclick="window.print()">
                                <i class="bx bx-printer"></i> Cetak Bukti
                            </button>
                            <a href="{{ route('pelanggan.dashboard') }}" class="btn btn-success">
                                <i class="bx bx-arrow-back"></i> Kembali ke Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>