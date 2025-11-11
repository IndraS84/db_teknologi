@extends('admin.layouts.master')

@section('title', 'Daftar Barang Masuk')

@section('page-title', 'Daftar Barang Masuk')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Barang Masuk</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h4 class="card-title">Daftar Barang Masuk</h4>
                    </div>
                    <div class="col-md-6 text-right">
                        <a href="{{ route('admin.barang-masuk.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus"></i> Tambah Barang Masuk
                        </a>
                    </div>
                </div>

                <form method="GET" action="{{ route('admin.barang-masuk.index') }}" class="mb-3">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Cari produk atau supplier..." value="{{ request('search') }}">
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
                                <th>Produk</th>
                                <th>Supplier</th>
                                <th>Jumlah</th>
                                <th>Tanggal Masuk</th>
                                <th>Admin</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($barangMasuks as $barangMasuk)
                                <tr>
                                    <td>{{ $barangMasuk->id_barang_masuk }}</td>
                                    <td>{{ $barangMasuk->produk->nama_produk ?? '-' }}</td>
                                    <td>{{ $barangMasuk->supplier->nama_supplier ?? '-' }}</td>
                                    <td>{{ $barangMasuk->jumlah }}</td>
                                    <td>{{ \Carbon\Carbon::parse($barangMasuk->tanggal_masuk)->format('d M Y') }}</td>
                                    <td>{{ $barangMasuk->admin->nama_admin ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('admin.barang-masuk.show', $barangMasuk->id_barang_masuk) }}" class="btn btn-info btn-sm">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.barang-masuk.edit', $barangMasuk->id_barang_masuk) }}" class="btn btn-warning btn-sm">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.barang-masuk.destroy', $barangMasuk->id_barang_masuk) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
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
                                    <td colspan="7" class="text-center">Tidak ada data barang masuk</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $barangMasuks->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

