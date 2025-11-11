@extends('admin.layouts.master')

@section('title', 'Edit Laporan')

@section('page-title', 'Edit Laporan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.laporan.index') }}">Laporan</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Edit Laporan</h4>
                <form action="{{ route('admin.laporan.update', $laporan->id_laporan) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="id_admin">Admin <span class="text-danger">*</span></label>
                        <select class="form-control @error('id_admin') is-invalid @enderror" id="id_admin" name="id_admin" required>
                            <option value="">Pilih Admin</option>
                            @foreach($admins as $admin)
                                <option value="{{ $admin->id_admin }}" {{ old('id_admin', $laporan->id_admin) == $admin->id_admin ? 'selected' : '' }}>
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
                        <input type="text" class="form-control @error('periode') is-invalid @enderror" 
                               id="periode" name="periode" value="{{ old('periode', $laporan->periode) }}" required>
                        @error('periode')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="total_penjualan">Total Penjualan <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('total_penjualan') is-invalid @enderror" 
                               id="total_penjualan" name="total_penjualan" value="{{ old('total_penjualan', $laporan->total_penjualan) }}" min="0" step="0.01" required>
                        @error('total_penjualan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="tanggal_cetak">Tanggal Cetak <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('tanggal_cetak') is-invalid @enderror" 
                               id="tanggal_cetak" name="tanggal_cetak" value="{{ old('tanggal_cetak', $laporan->tanggal_cetak) }}" required>
                        @error('tanggal_cetak')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('admin.laporan.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

