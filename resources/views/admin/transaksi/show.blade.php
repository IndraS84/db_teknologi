@extends('admin.layouts.master')

@section('title', 'Detail Transaksi')

@section('page-title', 'Detail Transaksi')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.transaksi.index') }}">Transaksi</a></li>
    <li class="breadcrumb-item active" aria-current="page">Detail</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title">Detail Transaksi #{{ $transaksi->id_transaksi }}</h4>
                    <div>
                        <a href="{{ route('admin.transaksi.edit', $transaksi->id_transaksi) }}" class="btn btn-warning">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('admin.transaksi.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <th width="200">ID Transaksi</th>
                                <td>#{{ $transaksi->id_transaksi }}</td>
                            </tr>
                            <tr>
                                <th>Pelanggan</th>
                                <td>{{ $transaksi->pelanggan->nama_pelanggan ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Diskon</th>
                                <td>{{ $transaksi->diskon ? $transaksi->diskon->nama_diskon : 'Tidak Ada' }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Transaksi</th>
                                <td>{{ \Carbon\Carbon::parse($transaksi->tanggal_transaksi)->format('d M Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Metode Pembayaran</th>
                                <td>{{ $transaksi->metode_pembayaran }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    <span class="badge 
                                        @if($transaksi->status_transaksi == 'completed') badge-success
                                        @elseif($transaksi->status_transaksi == 'processing') badge-warning
                                        @elseif($transaksi->status_transaksi == 'cancelled') badge-danger
                                        @else badge-info
                                        @endif">
                                        {{ ucfirst($transaksi->status_transaksi) }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <th width="200">Total Harga</th>
                                <td>Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th>Total Setelah Diskon</th>
                                <td><strong>Rp {{ number_format($transaksi->total_setelah_diskon, 0, ',', '.') }}</strong></td>
                            </tr>
                            @if($transaksi->diskon)
                                <tr>
                                    <th>Diskon</th>
                                    <td>
                                        {{ $transaksi->diskon->jenis_diskon == 'persentase' ? $transaksi->diskon->nilai_diskon . '%' : 'Rp ' . number_format($transaksi->diskon->nilai_diskon, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>

                <hr>

                    <div class="mb-3">
                        @if($transaksi->status_transaksi !== 'completed')
                            <form action="{{ route('admin.transaksi.confirm', $transaksi->id_transaksi) }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="mb-2">
                                    <label for="bukti" class="form-label">Unggah Bukti Pembayaran (opsional)</label>
                                    <input type="file" name="bukti" id="bukti" accept="image/*" class="form-control">
                                    <div class="form-text">Jika pelanggan sudah mengirim bukti melalui chat/wa, Anda bisa unggah di sini sebelum mengonfirmasi.</div>
                                </div>

                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" value="1" id="force_confirm" name="force_confirm">
                                    <label class="form-check-label" for="force_confirm">
                                        Konfirmasi tanpa bukti (paksa konfirmasi)
                                    </label>
                                </div>

                                <button type="submit" class="btn btn-success">Konfirmasi Pembayaran & Buat Struk</button>
                            </form>
                        @else
                            <a href="{{ route('admin.transaksi.struk', $transaksi->id_transaksi) }}" class="btn btn-primary">Download Struk</a>
                        @endif
                    </div>


                <h5>Detail Items</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
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
                                    <td>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

