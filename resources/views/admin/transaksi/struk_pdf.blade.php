<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Struk #{{ $transaksi->id_transaksi }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .items { width: 100%; border-collapse: collapse; }
        .items th, .items td { border: 1px solid #ddd; padding: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Toko Teknologi</h2>
        <p>Struk Transaksi #{{ $transaksi->id_transaksi }}</p>
    </div>

    <p>Pelanggan: {{ $transaksi->pelanggan->nama_pelanggan ?? '-' }} ({{ $transaksi->pelanggan->email ?? '-' }})</p>
    <p>Tanggal: {{ optional($transaksi->struk)->tanggal_cetak ?? $transaksi->tanggal_transaksi }}</p>

    <table class="items">
        <thead>
            <tr>
                <th>Produk</th>
                <th>Jumlah</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaksi->detailTransaksis as $detail)
                <tr>
                    <td>{{ $detail->produk->nama_produk ?? '-' }}</td>
                    <td>{{ $detail->jumlah }}</td>
                    <td>Rp {{ number_format($detail->subtotal,0,',','.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Total: Rp {{ number_format($transaksi->total_setelah_diskon,0,',','.') }}</h3>
    @if(optional($transaksi->struk)->detail_pembayaran)
        <h4>Detail Pembayaran</h4>
        <pre style="white-space: pre-wrap;">{{ optional($transaksi->struk)->detail_pembayaran }}</pre>
    @endif
</body>
</html>