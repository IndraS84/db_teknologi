<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Register - Toko Teknologi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Admin Register</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.register.post') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="nama_admin" class="form-label">Nama Admin</label>
                                <input id="nama_admin" type="text" class="form-control @error('nama_admin') is-invalid @enderror"
                                    name="nama_admin" value="{{ old('nama_admin') }}" required autofocus>
                                @error('nama_admin')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input id="username" type="text" class="form-control @error('username') is-invalid @enderror"
                                    name="username" value="{{ old('username') }}" required>
                                @error('username')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                    name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="alert alert-info">
                                <strong>Catatan:</strong> Password default adalah <code>12345</code>. Anda dapat mengubahnya setelah login.
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    Register
                                </button>
                            </div>
                        </form>

                        <div class="mt-3 text-center">
                            Sudah punya akun? <a href="{{ route('admin.login') }}">Login di sini</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
