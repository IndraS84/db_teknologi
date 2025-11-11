@extends('admin.layouts.master')

@section('title', 'Detail Barang Masuk')

@section('page-title', 'Detail Barang Masuk')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.barang-masuk.index') }}">Barang Masuk</a></li>
    <li class="breadcrumb-item active" aria-current="page">Detail</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title">Detail Barang Masuk</h4>
                    <div>
                        <a href="{{ route('admin.barang-masuk.edit', $barangMasuk->id_barang_masuk) }}" class="btn btn-warning">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('admin.barang-masuk.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>

                <table class="table table-bordered">
                    <tr>
                        <th width="200">ID Barang Masuk</th>
                        <td>{{ $barangMasuk->id_barang_masuk }}</td>
                    </tr>
                    <tr>
                        <th>Produk</th>
                        <td>{{ $barangMasuk->produk->nama_produk ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Supplier</th>
                        <td>{{ $barangMasuk->supplier->nama_supplier ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Admin</th>
                        <td>{{ $barangMasuk->admin->nama_admin ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Jumlah</th>
                        <td>{{ $barangMasuk->jumlah }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Masuk</th>
                        <td>{{ \Carbon\Carbon::parse($barangMasuk->tanggal_masuk)->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Dibuat</th>
                        <td>{{ $barangMasuk->created_at->format('d M Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

