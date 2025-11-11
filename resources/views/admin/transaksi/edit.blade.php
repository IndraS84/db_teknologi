@extends('admin.layouts.master')

@section('title', 'Edit Transaksi')

@section('page-title', 'Edit Transaksi')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.transaksi.index') }}">Transaksi</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Edit Transaksi #{{ $transaksi->id_transaksi }}</h4>
                <form action="{{ route('admin.transaksi.update', $transaksi->id_transaksi) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status_transaksi">Status Transaksi <span class="text-danger">*</span></label>
                                <select class="form-control @error('status_transaksi') is-invalid @enderror" id="status_transaksi" name="status_transaksi" required>
                                    <option value="pending" {{ old('status_transaksi', $transaksi->status_transaksi) == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="processing" {{ old('status_transaksi', $transaksi->status_transaksi) == 'processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="completed" {{ old('status_transaksi', $transaksi->status_transaksi) == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ old('status_transaksi', $transaksi->status_transaksi) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                @error('status_transaksi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="metode_pembayaran">Metode Pembayaran <span class="text-danger">*</span></label>
                                <select class="form-control @error('metode_pembayaran') is-invalid @enderror" id="metode_pembayaran" name="metode_pembayaran" required>
                                    <option value="cash" {{ old('metode_pembayaran', $transaksi->metode_pembayaran) == 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="transfer" {{ old('metode_pembayaran', $transaksi->metode_pembayaran) == 'transfer' ? 'selected' : '' }}>Transfer</option>
                                    <option value="debit" {{ old('metode_pembayaran', $transaksi->metode_pembayaran) == 'debit' ? 'selected' : '' }}>Debit</option>
                                    <option value="kredit" {{ old('metode_pembayaran', $transaksi->metode_pembayaran) == 'kredit' ? 'selected' : '' }}>Kredit</option>
                                </select>
                                @error('metode_pembayaran')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('admin.transaksi.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

