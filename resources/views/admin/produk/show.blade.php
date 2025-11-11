@extends('admin.layouts.master')

@section('title', 'Detail Produk')

@section('page-title', 'Detail Produk')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.produk.index') }}">Produk</a></li>
    <li class="breadcrumb-item active" aria-current="page">Detail</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title">Detail Produk</h4>
                    <div>
                        <a href="{{ route('admin.produk.edit', $produk->id_produk) }}" class="btn btn-warning">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('admin.produk.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>

                <table class="table table-bordered">
                    <tr>
                        <th width="200">ID Produk</th>
                        <td>{{ $produk->id_produk }}</td>
                    </tr>
                    <tr>
                        <th>Nama Produk</th>
                        <td>{{ $produk->nama_produk }}</td>
                    </tr>
                    <tr>
                        <th>Deskripsi</th>
                        <td>{{ $produk->deskripsi ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Harga</th>
                        <td>Rp {{ number_format($produk->harga, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Stok</th>
                        <td>
                            <span class="badge {{ $produk->stok > 0 ? 'badge-success' : 'badge-danger' }}">
                                {{ $produk->stok }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Admin</th>
                        <td>{{ $produk->admin->nama_admin ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Supplier</th>
                        <td>{{ $produk->supplier->nama_supplier ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Diskon</th>
                        <td>{{ $produk->diskon ? $produk->diskon->nama_diskon : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Dibuat</th>
                        <td>{{ $produk->created_at->format('d M Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

