@extends('admin.layouts.master')

@section('title', 'Detail Admin')

@section('page-title', 'Detail Admin')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.admin.index') }}">Admin</a></li>
    <li class="breadcrumb-item active" aria-current="page">Detail</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title">Detail Admin</h4>
                    <div>
                        <a href="{{ route('admin.admin.edit', $admin->id_admin) }}" class="btn btn-warning">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('admin.admin.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <th width="200">ID Admin</th>
                                <td>{{ $admin->id_admin }}</td>
                            </tr>
                            <tr>
                                <th>Nama Admin</th>
                                <td>{{ $admin->nama_admin }}</td>
                            </tr>
                            <tr>
                                <th>Username</th>
                                <td>{{ $admin->username }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <th>Email</th>
                                <td>{{ $admin->email }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Dibuat</th>
                                <td>{{ $admin->created_at->format('d M Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Diupdate</th>
                                <td>{{ $admin->updated_at->format('d M Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

