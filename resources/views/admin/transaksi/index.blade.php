@extends('admin.layouts.master')

@section('title', 'Daftar Transaksi')

@section('page-title', 'Daftar Transaksi')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Transaksi</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h4 class="card-title">Daftar Transaksi</h4>
                    </div>
                    <div class="col-md-6 text-right">
                        <a href="{{ route('admin.transaksi.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus"></i> Tambah Transaksi
                        </a>
                    </div>
                </div>

                <form method="GET" action="{{ route('admin.transaksi.index') }}" class="mb-3">
                    <div class="row">
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control" placeholder="Cari transaksi..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <select name="status_transaksi" class="form-control">
                                <option value="">Semua Status</option>
                                <option value="pending" {{ request('status_transaksi') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="processing" {{ request('status_transaksi') == 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="completed" {{ request('status_transaksi') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ request('status_transaksi') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="tanggal_mulai" class="form-control" placeholder="Tanggal Mulai" value="{{ request('tanggal_mulai') }}">
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="tanggal_akhir" class="form-control" placeholder="Tanggal Akhir" value="{{ request('tanggal_akhir') }}">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-info btn-block">Cari</button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Pelanggan</th>
                                <th>Tanggal</th>
                                <th>Total Harga</th>
                                <th>Total Setelah Diskon</th>
                                <th>Metode Pembayaran</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transaksis as $transaksi)
                                <tr>
                                    <td>#{{ $transaksi->id_transaksi }}</td>
                                    <td>{{ $transaksi->pelanggan->nama_pelanggan ?? '-' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($transaksi->tanggal_transaksi)->format('d M Y H:i') }}</td>
                                    <td>Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($transaksi->total_setelah_diskon, 0, ',', '.') }}</td>
                                    <td>{{ $transaksi->metode_pembayaran }}</td>
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
                                    <td>
                                        <a href="{{ route('admin.transaksi.show', $transaksi->id_transaksi) }}" class="btn btn-info btn-sm">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.transaksi.edit', $transaksi->id_transaksi) }}" class="btn btn-warning btn-sm">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.transaksi.destroy', $transaksi->id_transaksi) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?');">
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
                                    <td colspan="8" class="text-center">Tidak ada data transaksi</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $transaksis->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

