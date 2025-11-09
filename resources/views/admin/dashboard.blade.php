@extends('admin.layouts.master')

@section('title', 'Dashboard Admin')

@section('page-title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Dashboard Admin</h4>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Total Produk</th>
                            <th>Total Pelanggan</th>
                            <th>Total Transaksi</th>
                            <th>Total Penjualan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $totalProduk ?? 0 }}</td>
                            <td>{{ $totalPelanggan ?? 0 }}</td>
                            <td>{{ $totalTransaksi ?? 0 }}</td>
                            <td>Rp {{ number_format($totalPenjualan ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
