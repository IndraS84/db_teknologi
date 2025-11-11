<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Keranjang - Toko Teknologi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">
    <style>
        .product-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
        }
        .quantity-control {
            max-width: 120px;
        }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container">
            <a class="navbar-brand" href="#">Toko Teknologi</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('pelanggan.dashboard') }}">
                            <i class='bx bxs-dashboard'></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="#">
                            <i class='bx bxs-cart'></i> Keranjang
                        </a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    <div class="dropdown">
                        <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class='bx bx-user'></i> {{ Auth::user()->nama_pelanggan }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class='bx bx-log-out'></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class='bx bxs-cart'></i> Keranjang Belanja
                        </h5>
                    </div>
                    <div class="card-body">
                        @forelse($cartItems as $item)
                        <div class="cart-item mb-3 pb-3 border-bottom" id="cart-item-{{ $item->id_keranjang }}">
                            <div class="row align-items-center">
                                <div class="col-md-2">
                                    <img src="{{ asset('images/products/default.jpg') }}" 
                                         class="product-image rounded" 
                                         alt="{{ $item->produk->nama_produk }}">
                                </div>
                                <div class="col-md-4">
                                    <h6 class="mb-1">{{ $item->produk->nama_produk }}</h6>
                                    <p class="mb-1 text-success">
                                        Rp {{ number_format($item->produk->harga, 0, ',', '.') }}
                                    </p>
                                    <small class="text-muted">
                                        Stok: {{ $item->produk->stok }}
                                    </small>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group quantity-control">
                                        <button class="btn btn-outline-secondary update-quantity" 
                                                data-id="{{ $item->id_keranjang }}" 
                                                data-action="decrease">
                                            <i class='bx bx-minus'></i>
                                        </button>
                                        <input type="number" class="form-control text-center quantity-input" 
                                               value="{{ $item->jumlah }}" 
                                               min="1" 
                                               data-id="{{ $item->id_keranjang }}">
                                        <button class="btn btn-outline-secondary update-quantity" 
                                                data-id="{{ $item->id_keranjang }}" 
                                                data-action="increase">
                                            <i class='bx bx-plus'></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-2 text-end">
                                    <div class="subtotal" id="subtotal-{{ $item->id_keranjang }}">
                                        Rp {{ number_format($item->jumlah * $item->produk->harga, 0, ',', '.') }}
                                    </div>
                                </div>
                                <div class="col-md-1 text-end">
                                    <button class="btn btn-outline-danger btn-sm remove-item" 
                                            data-id="{{ $item->id_keranjang }}">
                                        <i class='bx bx-trash'></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4">
                            <i class='bx bx-cart' style="font-size: 48px;"></i>
                            <p class="mt-2">Keranjang belanja kosong</p>
                            <a href="{{ route('pelanggan.dashboard') }}" class="btn btn-success">
                                Mulai Belanja
                            </a>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Ringkasan Belanja</h5>
                    </div>
                    <div class="card-body">
                        @if($diskons->count() > 0)
                        <div class="mb-3">
                            <label for="diskon-select" class="form-label">Pilih Diskon (Opsional)</label>
                            <select class="form-select" id="diskon-select">
                                <option value="">Tidak ada diskon</option>
                                @foreach($diskons as $diskon)
                                <option value="{{ $diskon->id_diskon }}"
                                        data-jenis="{{ $diskon->jenis_diskon }}"
                                        data-nilai="{{ $diskon->nilai_diskon }}">
                                    {{ $diskon->nama_diskon }} -
                                    @if($diskon->jenis_diskon == 'persentase')
                                        {{ $diskon->nilai_diskon }}%
                                    @else
                                        Rp {{ number_format($diskon->nilai_diskon, 0, ',', '.') }}
                                    @endif
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <div class="d-flex justify-content-between mb-2">
                            <span>Total Item:</span>
                            <span id="totalItems">{{ $cartItems->sum('jumlah') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span id="subtotalPrice">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3" id="diskon-row" style="display: none;">
                            <span>Diskon:</span>
                            <span id="diskonAmount">-Rp 0</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="fw-bold">Total:</span>
                            <span class="fw-bold text-success" id="totalPrice">
                                Rp {{ number_format($total, 0, ',', '.') }}
                            </span>
                        </div>
                        <a href="{{ route('pelanggan.checkout') }}" class="btn btn-success w-100 {{ $cartItems->isEmpty() ? 'disabled' : '' }}">
                            <i class='bx bx-check'></i> Lanjutkan ke Pembayaran
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            function formatNumber(number) {
                return new Intl.NumberFormat('id-ID').format(number);
            }

            $('.quantity-input').change(function() {
                const id = $(this).data('id');
                const quantity = parseInt($(this).val());
                
                if (quantity < 1) {
                    $(this).val(1);
                    return;
                }

                updateCart(id, quantity);
            });

            $('.update-quantity').click(function() {
                const id = $(this).data('id');
                const action = $(this).data('action');
                const input = $(`.quantity-input[data-id="${id}"]`);
                let quantity = parseInt(input.val());

                if (action === 'increase') {
                    quantity++;
                } else if (action === 'decrease' && quantity > 1) {
                    quantity--;
                }

                input.val(quantity);
                updateCart(id, quantity);
            });

            function updateCart(id, quantity) {
                $.ajax({
                    url: `/pelanggan/keranjang/update/${id}`,
                    method: 'POST',
                    data: {
                        jumlah: quantity
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#totalPrice').text('Rp ' + formatNumber(response.total));
                            $('#totalItems').text(response.cart_count);
                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON.message || 'Terjadi kesalahan');
                    }
                });
            }

            $('.remove-item').click(function() {
                if (!confirm('Apakah Anda yakin ingin menghapus item ini?')) {
                    return;
                }

                const id = $(this).data('id');
                $.ajax({
                    url: `/pelanggan/keranjang/remove/${id}`,
                    method: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            $(`#cart-item-${id}`).fadeOut(function() {
                                $(this).remove();
                                location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON.message || 'Terjadi kesalahan');
                    }
                });
            });

            // Diskon calculation
            $('#diskon-select').change(function() {
                const selectedOption = $(this).find('option:selected');
                const diskonId = selectedOption.val();
                const jenis = selectedOption.data('jenis');
                const nilai = parseFloat(selectedOption.data('nilai')) || 0;
                const subtotal = {{ $total }};

                let diskonAmount = 0;
                let totalSetelahDiskon = subtotal;

                if (diskonId && jenis && nilai > 0) {
                    if (jenis === 'persentase') {
                        diskonAmount = subtotal * (nilai / 100);
                    } else {
                        diskonAmount = Math.min(nilai, subtotal);
                    }
                    totalSetelahDiskon = subtotal - diskonAmount;
                    $('#diskon-row').show();
                } else {
                    $('#diskon-row').hide();
                }

                $('#diskonAmount').text('-Rp ' + formatNumber(diskonAmount));
                $('#totalPrice').text('Rp ' + formatNumber(totalSetelahDiskon));
                $('#selected-diskon').val(diskonId);
            });
        });
    </script>
</body>
</html>