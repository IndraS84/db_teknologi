@extends('admin.layouts.master')

@section('title', 'Detail Diskon')

@section('page-title', 'Detail Diskon')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.diskon.index') }}">Diskon</a></li>
    <li class="breadcrumb-item active" aria-current="page">Detail</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title">Detail Diskon</h4>
                    <div>
                        <a href="{{ route('admin.diskon.edit', $diskon->id_diskon) }}" class="btn btn-warning">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('admin.diskon.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>

                <table class="table table-bordered">
                    <tr>
                        <th width="200">ID Diskon</th>
                        <td>{{ $diskon->id_diskon }}</td>
                    </tr>
                    <tr>
                        <th>Nama Diskon</th>
                        <td>{{ $diskon->nama_diskon }}</td>
                    </tr>
                    <tr>
                        <th>Jenis Diskon</th>
                        <td>{{ ucfirst($diskon->jenis_diskon) }}</td>
                    </tr>
                    <tr>
                        <th>Nilai Diskon</th>
                        <td>
                            @if($diskon->jenis_diskon == 'persentase')
                                {{ $diskon->nilai_diskon }}%
                            @else
                                Rp {{ number_format($diskon->nilai_diskon, 0, ',', '.') }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Tanggal Mulai</th>
                        <td>{{ $diskon->tanggal_mulai->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Berakhir</th>
                        <td>{{ $diskon->tanggal_berakhir->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <span class="badge {{ $diskon->status == 'active' ? 'badge-success' : 'badge-danger' }}">
                                {{ ucfirst($diskon->status) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Keterangan</th>
                        <td>{{ $diskon->keterangan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Total Produk</th>
                        <td>{{ $diskon->produks->count() }}</td>
                    </tr>
                    <tr>
                        <th>Total Transaksi</th>
                        <td>{{ $diskon->transaksis->count() }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

