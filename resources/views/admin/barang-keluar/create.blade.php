@extends('admin.layouts.master')

@section('title', 'Tambah Barang Keluar')

@section('page-title', 'Tambah Barang Keluar')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.barang-keluar.index') }}">Barang Keluar</a></li>
    <li class="breadcrumb-item active" aria-current="page">Tambah</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Tambah Barang Keluar</h4>
                <form action="{{ route('admin.barang-keluar.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="id_produk">Produk <span class="text-danger">*</span></label>
                        <select class="form-control @error('id_produk') is-invalid @enderror" id="id_produk" name="id_produk" required>
                            <option value="">Pilih Produk</option>
                            @foreach($produks as $produk)
                                <option value="{{ $produk->id_produk }}" {{ old('id_produk') == $produk->id_produk ? 'selected' : '' }}>
                                    {{ $produk->nama_produk }} (Stok: {{ $produk->stok }})
                                </option>
                            @endforeach
                        </select>
                        @error('id_produk')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="id_pelanggan">Pelanggan <span class="text-danger">*</span></label>
                        <select class="form-control @error('id_pelanggan') is-invalid @enderror" id="id_pelanggan" name="id_pelanggan" required>
                            <option value="">Pilih Pelanggan</option>
                            @foreach($pelanggans as $pelanggan)
                                <option value="{{ $pelanggan->id_pelanggan }}" {{ old('id_pelanggan') == $pelanggan->id_pelanggan ? 'selected' : '' }}>
                                    {{ $pelanggan->nama_pelanggan }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_pelanggan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="id_admin">Admin <span class="text-danger">*</span></label>
                        <select class="form-control @error('id_admin') is-invalid @enderror" id="id_admin" name="id_admin" required>
                            <option value="">Pilih Admin</option>
                            @foreach($admins as $admin)
                                <option value="{{ $admin->id_admin }}" {{ old('id_admin') == $admin->id_admin ? 'selected' : '' }}>
                                    {{ $admin->nama_admin }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_admin')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="jumlah">Jumlah <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('jumlah') is-invalid @enderror" 
                                       id="jumlah" name="jumlah" value="{{ old('jumlah') }}" min="1" required>
                                @error('jumlah')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tanggal_keluar">Tanggal Keluar <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('tanggal_keluar') is-invalid @enderror" 
                                       id="tanggal_keluar" name="tanggal_keluar" value="{{ old('tanggal_keluar', date('Y-m-d')) }}" required>
                                @error('tanggal_keluar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('admin.barang-keluar.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

