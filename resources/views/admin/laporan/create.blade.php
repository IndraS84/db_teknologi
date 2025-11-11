@extends('admin.layouts.master')

@section('title', 'Buat Laporan')

@section('page-title', 'Buat Laporan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.laporan.index') }}">Laporan</a></li>
    <li class="breadcrumb-item active" aria-current="page">Buat</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Buat Laporan Baru</h4>
                <form action="{{ route('admin.laporan.store') }}" method="POST">
                    @csrf
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

                    <div class="form-group">
                        <label for="periode">Periode <span class="text-danger">*</span></label>
                        <select class="form-control @error('periode') is-invalid @enderror" id="periode" name="periode" required>
                            <option value="">Pilih Periode</option>
                            <option value="bulan ini" {{ old('periode') == 'bulan ini' ? 'selected' : '' }}>Bulan Ini</option>
                            <option value="tahun ini" {{ old('periode') == 'tahun ini' ? 'selected' : '' }}>Tahun Ini</option>
                            <option value="semua" {{ old('periode') == 'semua' ? 'selected' : '' }}>Semua</option>
                        </select>
                        @error('periode')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="tanggal_cetak">Tanggal Cetak <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('tanggal_cetak') is-invalid @enderror" 
                               id="tanggal_cetak" name="tanggal_cetak" value="{{ old('tanggal_cetak', date('Y-m-d')) }}" required>
                        @error('tanggal_cetak')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Buat Laporan</button>
                        <a href="{{ route('admin.laporan.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

