@extends('admin.layouts.master')

@section('title', 'Detail Laporan')

@section('page-title', 'Detail Laporan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.laporan.index') }}">Laporan</a></li>
    <li class="breadcrumb-item active" aria-current="page">Detail</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title">Detail Laporan</h4>
                    <div>
                        <a href="{{ route('admin.laporan.edit', $laporan->id_laporan) }}" class="btn btn-warning">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('admin.laporan.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>

                <table class="table table-bordered">
                    <tr>
                        <th width="200">ID Laporan</th>
                        <td>{{ $laporan->id_laporan }}</td>
                    </tr>
                    <tr>
                        <th>Admin</th>
                        <td>{{ $laporan->admin->nama_admin ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Periode</th>
                        <td>{{ $laporan->periode }}</td>
                    </tr>
                    <tr>
                        <th>Total Penjualan</th>
                        <td><strong>Rp {{ number_format($laporan->total_penjualan, 0, ',', '.') }}</strong></td>
                    </tr>
                    <tr>
                        <th>Tanggal Cetak</th>
                        <td>{{ \Carbon\Carbon::parse($laporan->tanggal_cetak)->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Dibuat</th>
                        <td>{{ $laporan->created_at->format('d M Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

