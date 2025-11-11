@extends('admin.layouts.master')

@section('title', 'Tambah Diskon')

@section('page-title', 'Tambah Diskon')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.diskon.index') }}">Diskon</a></li>
    <li class="breadcrumb-item active" aria-current="page">Tambah</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Tambah Diskon Baru</h4>
                <form action="{{ route('admin.diskon.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="nama_diskon">Nama Diskon <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama_diskon') is-invalid @enderror" 
                               id="nama_diskon" name="nama_diskon" value="{{ old('nama_diskon') }}" required>
                        @error('nama_diskon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="jenis_diskon">Jenis Diskon <span class="text-danger">*</span></label>
                                <select class="form-control @error('jenis_diskon') is-invalid @enderror" 
                                        id="jenis_diskon" name="jenis_diskon" required>
                                    <option value="">Pilih Jenis</option>
                                    <option value="persentase" {{ old('jenis_diskon') == 'persentase' ? 'selected' : '' }}>Persentase</option>
                                    <option value="fixed" {{ old('jenis_diskon') == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                                </select>
                                @error('jenis_diskon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nilai_diskon">Nilai Diskon <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('nilai_diskon') is-invalid @enderror" 
                                       id="nilai_diskon" name="nilai_diskon" value="{{ old('nilai_diskon') }}" min="0" step="0.01" required>
                                @error('nilai_diskon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Masukkan persentase (0-100) atau jumlah tetap</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="tanggal_mulai">Tanggal Mulai <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('tanggal_mulai') is-invalid @enderror" 
                                       id="tanggal_mulai" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" required>
                                @error('tanggal_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="tanggal_berakhir">Tanggal Berakhir <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('tanggal_berakhir') is-invalid @enderror" 
                                       id="tanggal_berakhir" name="tanggal_berakhir" value="{{ old('tanggal_berakhir') }}" required>
                                @error('tanggal_berakhir')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status">Status <span class="text-danger">*</span></label>
                                <select class="form-control @error('status') is-invalid @enderror" 
                                        id="status" name="status" required>
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                  id="keterangan" name="keterangan" rows="3">{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('admin.diskon.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

