@extends('admin.layouts.master')

@section('title', 'Tambah Produk')

@section('page-title', 'Tambah Produk')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.produk.index') }}">Produk</a></li>
    <li class="breadcrumb-item active" aria-current="page">Tambah</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Tambah Produk Baru</h4>
                <form action="{{ route('admin.produk.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="nama_produk">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama_produk') is-invalid @enderror" 
                               id="nama_produk" name="nama_produk" value="{{ old('nama_produk') }}" required>
                        @error('nama_produk')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="deskripsi">Deskripsi</label>
                        <textarea class="form-control @error('deskripsi') is-invalid @enderror" 
                                  id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="harga">Harga <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('harga') is-invalid @enderror" 
                                       id="harga" name="harga" value="{{ old('harga') }}" min="0" step="0.01" required>
                                @error('harga')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="stok">Stok <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('stok') is-invalid @enderror" 
                                       id="stok" name="stok" value="{{ old('stok') }}" min="0" required>
                                @error('stok')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="id_admin">Admin <span class="text-danger">*</span></label>
                                <select class="form-control @error('id_admin') is-invalid @enderror" 
                                        id="id_admin" name="id_admin" required>
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
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="id_supplier">Supplier <span class="text-danger">*</span></label>
                                <select class="form-control @error('id_supplier') is-invalid @enderror" 
                                        id="id_supplier" name="id_supplier" required>
                                    <option value="">Pilih Supplier</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id_supplier }}" {{ old('id_supplier') == $supplier->id_supplier ? 'selected' : '' }}>
                                            {{ $supplier->nama_supplier }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_supplier')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="id_diskon">Diskon</label>
                                <select class="form-control @error('id_diskon') is-invalid @enderror" 
                                        id="id_diskon" name="id_diskon">
                                    <option value="">Tidak Ada Diskon</option>
                                    @foreach($diskons as $diskon)
                                        <option value="{{ $diskon->id_diskon }}" {{ old('id_diskon') == $diskon->id_diskon ? 'selected' : '' }}>
                                            {{ $diskon->nama_diskon }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_diskon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('admin.produk.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

