@extends('admin.layouts.master')

@section('title', 'Daftar Pelanggan')

@section('page-title', 'Daftar Pelanggan')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Pelanggan</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h4 class="card-title">Daftar Pelanggan</h4>
                    </div>
                    <div class="col-md-6 text-right">
                        <a href="{{ route('admin.pelanggan.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus"></i> Tambah Pelanggan
                        </a>
                    </div>
                </div>

                <form method="GET" action="{{ route('admin.pelanggan.index') }}" class="mb-3">
                    <div class="row">
                        <div class="col-md-10">
                            <input type="text" name="search" class="form-control" placeholder="Cari pelanggan..." value="{{ request('search') }}">
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
                                <th>Nama Pelanggan</th>
                                <th>Email</th>
                                <th>No. HP</th>
                                <th>Alamat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pelanggans as $pelanggan)
                                <tr>
                                    <td>{{ $pelanggan->id_pelanggan }}</td>
                                    <td>{{ $pelanggan->nama_pelanggan }}</td>
                                    <td>{{ $pelanggan->email }}</td>
                                    <td>{{ $pelanggan->no_hp }}</td>
                                    <td>{{ Str::limit($pelanggan->alamat, 50) }}</td>
                                    <td>
                                        <a href="{{ route('admin.pelanggan.show', $pelanggan->id_pelanggan) }}" class="btn btn-info btn-sm">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.pelanggan.edit', $pelanggan->id_pelanggan) }}" class="btn btn-warning btn-sm">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.pelanggan.destroy', $pelanggan->id_pelanggan) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pelanggan ini?');">
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
                                    <td colspan="6" class="text-center">Tidak ada data pelanggan</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $pelanggans->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

