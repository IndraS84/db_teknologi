@extends('admin.layouts.master')

@section('title', 'Detail Barang Keluar')

@section('page-title', 'Detail Barang Keluar')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.barang-keluar.index') }}">Barang Keluar</a></li>
    <li class="breadcrumb-item active" aria-current="page">Detail</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title">Detail Barang Keluar</h4>
                    <div>
                        <a href="{{ route('admin.barang-keluar.edit', $barangKeluar->id_barang_keluar) }}" class="btn btn-warning">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('admin.barang-keluar.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>

                <table class="table table-bordered">
                    <tr>
                        <th width="200">ID Barang Keluar</th>
                        <td>{{ $barangKeluar->id_barang_keluar }}</td>
                    </tr>
                    <tr>
                        <th>Produk</th>
                        <td>{{ $barangKeluar->produk->nama_produk ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Pelanggan</th>
                        <td>{{ $barangKeluar->pelanggan->nama_pelanggan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Admin</th>
                        <td>{{ $barangKeluar->admin->nama_admin ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Jumlah</th>
                        <td>{{ $barangKeluar->jumlah }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Keluar</th>
                        <td>{{ \Carbon\Carbon::parse($barangKeluar->tanggal_keluar)->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Dibuat</th>
                        <td>{{ $barangKeluar->created_at->format('d M Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

