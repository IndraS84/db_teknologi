@extends('admin.layouts.master')

@section('title', 'Daftar Produk')

@section('page-title', 'Daftar Produk')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Produk</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h4 class="card-title">Daftar Produk</h4>
                    </div>
                    <div class="col-md-6 text-right">
                        <a href="{{ route('admin.produk.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus"></i> Tambah Produk
                        </a>
                    </div>
                </div>

                <form method="GET" action="{{ route('admin.produk.index') }}" class="mb-3">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Cari produk..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="id_supplier" class="form-control">
                                <option value="">Semua Supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id_supplier }}" {{ request('id_supplier') == $supplier->id_supplier ? 'selected' : '' }}>
                                        {{ $supplier->nama_supplier }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="stok_min" class="form-control" placeholder="Stok Min" value="{{ request('stok_min') }}">
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
                                <th>Nama Produk</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Supplier</th>
                                <th>Diskon</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($produks as $produk)
                                <tr>
                                    <td>{{ $produk->id_produk }}</td>
                                    <td>{{ $produk->nama_produk }}</td>
                                    <td>Rp {{ number_format($produk->harga, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge {{ $produk->stok > 0 ? 'badge-success' : 'badge-danger' }}">
                                            {{ $produk->stok }}
                                        </span>
                                    </td>
                                    <td>{{ $produk->supplier->nama_supplier ?? '-' }}</td>
                                    <td>{{ $produk->diskon ? $produk->diskon->nama_diskon : '-' }}</td>
                                    <td>
                                        <a href="{{ route('admin.produk.show', $produk->id_produk) }}" class="btn btn-info btn-sm">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.produk.edit', $produk->id_produk) }}" class="btn btn-warning btn-sm">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.produk.destroy', $produk->id_produk) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">
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
                                    <td colspan="7" class="text-center">Tidak ada data produk</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $produks->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

