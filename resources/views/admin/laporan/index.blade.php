@extends('admin.layouts.master')

@section('title', 'Daftar Laporan')

@section('page-title', 'Daftar Laporan')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Laporan</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h4 class="card-title">Daftar Laporan</h4>
                    </div>
                    <div class="col-md-6 text-right">
                        <a href="{{ route('admin.laporan.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus"></i> Buat Laporan
                        </a>
                    </div>
                </div>

                <form method="GET" action="{{ route('admin.laporan.index') }}" class="mb-3">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Cari laporan..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="tanggal_mulai" class="form-control" placeholder="Tanggal Mulai" value="{{ request('tanggal_mulai') }}">
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="tanggal_akhir" class="form-control" placeholder="Tanggal Akhir" value="{{ request('tanggal_akhir') }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-info btn-block">Cari</button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Periode</th>
                                <th>Total Penjualan</th>
                                <th>Tanggal Cetak</th>
                                <th>Admin</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($laporans as $laporan)
                                <tr>
                                    <td>{{ $laporan->id_laporan }}</td>
                                    <td>{{ $laporan->periode }}</td>
                                    <td>Rp {{ number_format($laporan->total_penjualan, 0, ',', '.') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($laporan->tanggal_cetak)->format('d M Y') }}</td>
                                    <td>{{ $laporan->admin->nama_admin ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('admin.laporan.show', $laporan->id_laporan) }}" class="btn btn-info btn-sm">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.laporan.edit', $laporan->id_laporan) }}" class="btn btn-warning btn-sm">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.laporan.destroy', $laporan->id_laporan) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus laporan ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada data laporan</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $laporans->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

