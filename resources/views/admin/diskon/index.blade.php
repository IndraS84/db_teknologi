@extends('admin.layouts.master')

@section('title', 'Daftar Diskon')

@section('page-title', 'Daftar Diskon')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Diskon</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h4 class="card-title">Daftar Diskon</h4>
                    </div>
                    <div class="col-md-6 text-right">
                        <a href="{{ route('admin.diskon.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus"></i> Tambah Diskon
                        </a>
                    </div>
                </div>

                <form method="GET" action="{{ route('admin.diskon.index') }}" class="mb-3">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Cari diskon..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-control">
                                <option value="">Semua Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
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
                                <th>Nama Diskon</th>
                                <th>Jenis</th>
                                <th>Nilai</th>
                                <th>Tanggal Mulai</th>
                                <th>Tanggal Berakhir</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($diskons as $diskon)
                                <tr>
                                    <td>{{ $diskon->id_diskon }}</td>
                                    <td>{{ $diskon->nama_diskon }}</td>
                                    <td>{{ ucfirst($diskon->jenis_diskon) }}</td>
                                    <td>
                                        @if($diskon->jenis_diskon == 'persentase')
                                            {{ $diskon->nilai_diskon }}%
                                        @else
                                            Rp {{ number_format($diskon->nilai_diskon, 0, ',', '.') }}
                                        @endif
                                    </td>
                                    <td>{{ $diskon->tanggal_mulai->format('d M Y') }}</td>
                                    <td>{{ $diskon->tanggal_berakhir->format('d M Y') }}</td>
                                    <td>
                                        <span class="badge {{ $diskon->status == 'active' ? 'badge-success' : 'badge-danger' }}">
                                            {{ ucfirst($diskon->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.diskon.show', $diskon->id_diskon) }}" class="btn btn-info btn-sm">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.diskon.edit', $diskon->id_diskon) }}" class="btn btn-warning btn-sm">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.diskon.destroy', $diskon->id_diskon) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus diskon ini?');">
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
                                    <td colspan="8" class="text-center">Tidak ada data diskon</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $diskons->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

