@extends('admin.layouts.master')

@section('title', 'Detail Pelanggan')

@section('page-title', 'Detail Pelanggan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.pelanggan.index') }}">Pelanggan</a></li>
    <li class="breadcrumb-item active" aria-current="page">Detail</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title">Detail Pelanggan</h4>
                    <div>
                        <a href="{{ route('admin.pelanggan.edit', $pelanggan->id_pelanggan) }}" class="btn btn-warning">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('admin.pelanggan.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>

                <table class="table table-bordered">
                    <tr>
                        <th width="200">ID Pelanggan</th>
                        <td>{{ $pelanggan->id_pelanggan }}</td>
                    </tr>
                    <tr>
                        <th>Nama Pelanggan</th>
                        <td>{{ $pelanggan->nama_pelanggan }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>{{ $pelanggan->email }}</td>
                    </tr>
                    <tr>
                        <th>No. HP</th>
                        <td>{{ $pelanggan->no_hp }}</td>
                    </tr>
                    <tr>
                        <th>Alamat</th>
                        <td>{{ $pelanggan->alamat }}</td>
                    </tr>
                    <tr>
                        <th>Total Transaksi</th>
                        <td>{{ $pelanggan->transaksis->count() }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Dibuat</th>
                        <td>{{ $pelanggan->created_at->format('d M Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

