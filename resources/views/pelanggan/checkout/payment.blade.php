<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instruksi Pembayaran - Toko Teknologi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-4">
        <h3>Instruksi Pembayaran untuk Transaksi #{{ $transaksi->id_transaksi }}</h3>

        <div class="card mb-3">
            <div class="card-body">
                <p>Metode: {{ $transaksi->metode_pembayaran }}</p>
                <p>Total: Rp {{ number_format($transaksi->total_setelah_diskon, 0, ',', '.') }}</p>

                @if($transaksi->metode_pembayaran == 'transfer_bank')
                    <h5>Transfer Bank</h5>
                    <p>Silakan transfer ke rekening berikut:</p>
                    <ul>
                        <li>Bank: BCA</li>
                        <li>Nomor Rekening: 123-456-789</li>
                        <li>Atas Nama: Toko Teknologi</li>
                    </ul>
                @elseif($transaksi->metode_pembayaran == 'e_wallet')
                    <h5>E-Wallet</h5>
                    <p>Silakan transfer menggunakan QR/ID berikut di aplikasi e-wallet Anda.</p>
                @endif

                <hr>

                <h5>Unggah Bukti Pembayaran</h5>
                <form action="{{ route('pelanggan.transaksi.uploadProof', $transaksi->id_transaksi) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="bukti" class="form-label">Pilih gambar bukti pembayaran (max 2MB)</label>
                        <input type="file" name="bukti" id="bukti" class="form-control" accept="image/*" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Unggah Bukti</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
