<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Pelanggan - Toko Teknologi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">
    <style>
        .product-card {
            transition: transform 0.2s;
            height: 100%;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .product-image {
            height: 200px;
            object-fit: cover;
        }
        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            padding: 4px 8px;
            border-radius: 50%;
            background-color: #dc3545;
            color: white;
            font-size: 12px;
        }
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
        .loader {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #28a745;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 8px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class='bx bx-store'></i> Toko Teknologi
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('pelanggan.dashboard') }}">
                            <i class='bx bxs-dashboard'></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('pelanggan.transaksi.index') }}">
                            <i class='bx bx-receipt'></i> Transaksi
                            @if($activeOrders > 0)
                                <span class="badge bg-warning">{{ $activeOrders }}</span>
                            @endif
                        </a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    <a href="{{ route('pelanggan.cart') }}" class="btn btn-outline-light position-relative me-3">
                        <i class='bx bx-cart'></i> Keranjang
                        @if($cartItems > 0)
                            <span class="cart-badge" id="cartBadge">{{ $cartItems }}</span>
                        @endif
                    </a>
                    <div class="dropdown">
                        <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class='bx bx-user'></i> {{ Auth::user()->nama_pelanggan }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="#">
                                    <i class='bx bx-user-circle'></i> Profile
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
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

    <!-- Toast Container for Notifications -->
    <div class="toast-container"></div>

    <!-- Content -->
    <div class="container py-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class='bx bx-check-circle'></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class='bx bx-error-circle'></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Search Bar -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form action="{{ route('pelanggan.dashboard') }}" method="GET" class="row g-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class='bx bx-search'></i>
                            </span>
                            <input type="text" name="search" class="form-control" placeholder="Cari produk..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="row">
                            <div class="col-6">
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="min_price" class="form-control" placeholder="Min" value="{{ request('min_price') }}">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="max_price" class="form-control" placeholder="Max" value="{{ request('max_price') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-success w-100">
                            <i class='bx bx-filter'></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="row g-4" id="products-grid">
            @forelse($products as $product)
                <div class="col-md-6 col-lg-4 col-xl-3">
                    <div class="card h-100 product-card">
                        <img src="{{ $product->gambar ? asset('storage/' . $product->gambar) : asset('images/products/default.jpg') }}" 
                             class="card-img-top product-image" 
                             alt="{{ $product->nama_produk }}">
                        <div class="card-body">
                            <h5 class="card-title">{{ $product->nama_produk }}</h5>
                            <p class="card-text">
                                <small class="text-muted">Stok: {{ $product->stok }}</small>
                            </p>
                            <p class="card-text fw-bold text-success">
                                Rp {{ number_format($product->harga, 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="card-footer bg-white border-top-0">
                            <button class="btn btn-success w-100 add-to-cart" data-id="{{ $product->id_produk }}" {{ $product->stok < 1 ? 'disabled' : '' }}>
                                <span class="loader"></span>
                                <i class='bx bx-cart-add'></i> Tambah ke Keranjang
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class='bx bx-package' style="font-size: 48px;"></i>
                        <p class="mt-2">Tidak ada produk tersedia</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $products->links() }}
        </div>

        @push('scripts')
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add to Cart functionality
            document.querySelectorAll('.add-to-cart').forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.dataset.id;
                    const loader = this.querySelector('.loader');
                    const icon = this.querySelector('i');
                    
                    // Show loader and disable button
                    loader.style.display = 'inline-block';
                    icon.style.display = 'none';
                    this.disabled = true;
                    
                    fetch(`/pelanggan/cart/add/${productId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Update cart count badge
                        const cartBadge = document.getElementById('cartBadge');
                        if (cartBadge) {
                            cartBadge.textContent = data.cart_count;
                        } else {
                            const cartLink = document.querySelector('.cart-link');
                            const newBadge = document.createElement('span');
                            newBadge.id = 'cartBadge';
                            newBadge.className = 'cart-badge';
                            newBadge.textContent = data.cart_count;
                            cartLink.appendChild(newBadge);
                        }

                        // Show success toast
                        const toast = document.createElement('div');
                        toast.className = 'toast align-items-center text-bg-success border-0';
                        toast.setAttribute('role', 'alert');
                        toast.innerHTML = `
                            <div class="d-flex">
                                <div class="toast-body">
                                    <i class='bx bx-check'></i> ${data.message}
                                </div>
                                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                            </div>
                        `;
                        document.querySelector('.toast-container').appendChild(toast);
                        const bsToast = new bootstrap.Toast(toast);
                        bsToast.show();
                        toast.addEventListener('hidden.bs.toast', () => toast.remove());
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        // Show error toast
                        const toast = document.createElement('div');
                        toast.className = 'toast align-items-center text-bg-danger border-0';
                        toast.setAttribute('role', 'alert');
                        toast.innerHTML = `
                            <div class="d-flex">
                                <div class="toast-body">
                                    <i class='bx bx-error'></i> Gagal menambahkan ke keranjang
                                </div>
                                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                            </div>
                        `;
                        document.querySelector('.toast-container').appendChild(toast);
                        const bsToast = new bootstrap.Toast(toast);
                        bsToast.show();
                        toast.addEventListener('hidden.bs.toast', () => toast.remove());
                    })
                    .finally(() => {
                        // Hide loader and enable button
                        loader.style.display = 'none';
                        icon.style.display = 'inline-block';
                        this.disabled = false;
                    });
                });
            });
        });
        </script>
        @endpush
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Setup AJAX CSRF token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Toast notification function
            function showToast(message, type = 'success') {
                const toast = `
                    <div class="toast align-items-center text-white bg-${type} border-0" role="alert">
                        <div class="d-flex">
                            <div class="toast-body">
                                ${message}
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                        </div>
                    </div>
                `;
                $('.toast-container').append(toast);
                const toastElement = new bootstrap.Toast($('.toast').last());
                toastElement.show();
                
                setTimeout(() => {
                    $('.toast').first().remove();
                }, 3000);
            }

            // Add to cart function
            $('.add-to-cart').click(function() {
                const button = $(this);
                const productId = button.data('id');
                const loader = button.find('.loader');
                const icon = button.find('i');
                
                // Show loading state
                button.prop('disabled', true);
                loader.show();
                icon.hide();

                $.ajax({
                    url: `/pelanggan/cart/add/${productId}`,
                    method: 'POST',
                    data: {
                        id_produk: productId,
                        jumlah: 1
                    },
                    success: function(response) {
                        showToast(response.message);
                        
                        // Update cart badge
                        const cartBadge = $('#cartBadge');
                        if (response.cart_count > 0) {
                            if (cartBadge.length) {
                                cartBadge.text(response.cart_count);
                            } else {
                                $('.btn-outline-light.position-relative').append(
                                    `<span class="cart-badge" id="cartBadge">${response.cart_count}</span>`
                                );
                            }
                        }
                    },
                    error: function(xhr) {
                        showToast(xhr.responseJSON.message || 'Terjadi kesalahan', 'danger');
                    },
                    complete: function() {
                        // Restore button state
                        button.prop('disabled', false);
                        loader.hide();
                        icon.show();
                    }
                });
            });
        });
    </script>
</body>
</html>