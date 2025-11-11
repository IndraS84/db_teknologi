@extends('admin.layouts.master')

@section('title', 'Detail Supplier')

@section('page-title', 'Detail Supplier')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.supplier.index') }}">Supplier</a></li>
    <li class="breadcrumb-item active" aria-current="page">Detail</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title">Detail Supplier</h4>
                    <div>
                        <a href="{{ route('admin.supplier.edit', $supplier->id_supplier) }}" class="btn btn-warning">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('admin.supplier.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>

                <table class="table table-bordered">
                    <tr>
                        <th width="200">ID Supplier</th>
                        <td>{{ $supplier->id_supplier }}</td>
                    </tr>
                    <tr>
                        <th>Nama Supplier</th>
                        <td>{{ $supplier->nama_supplier }}</td>
                    </tr>
                    <tr>
                        <th>Alamat</th>
                        <td>{{ $supplier->alamat }}</td>
                    </tr>
                    <tr>
                        <th>No. Telepon</th>
                        <td>{{ $supplier->no_telp }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Dibuat</th>
                        <td>{{ $supplier->created_at->format('d M Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Diupdate</th>
                        <td>{{ $supplier->updated_at->format('d M Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

