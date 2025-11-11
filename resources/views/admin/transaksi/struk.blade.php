@extends('admin.layouts.master')

@section('title', 'Struk Transaksi')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4>Struk Transaksi #{{ $transaksi->id_transaksi }}</h4>
                <p>Tanggal Cetak: {{ optional($transaksi->struk)->tanggal_cetak }}</p>

                <h5>Pelanggan</h5>
                <p>{{ $transaksi->pelanggan->nama_pelanggan ?? '-' }} - {{ $transaksi->pelanggan->email ?? '-' }}</p>

                @if(optional($transaksi->struk)->detail_pembayaran)
                    <h5>Detail Pembayaran</h5>
                    <pre style="white-space: pre-wrap;">{{ optional($transaksi->struk)->detail_pembayaran }}</pre>
                @endif

                <h5>Items</h5>
                <ul>
                    @foreach($transaksi->detailTransaksis as $detail)
                        <li>{{ $detail->produk->nama_produk ?? '-' }} x {{ $detail->jumlah }} - Rp {{ number_format($detail->subtotal,0,',','.') }}</li>
                    @endforeach
                </ul>

                <h5>Total: Rp {{ number_format($transaksi->total_setelah_diskon,0,',','.') }}</h5>

                <a href="{{ url()->previous() }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </div>
</div>
@endsection
