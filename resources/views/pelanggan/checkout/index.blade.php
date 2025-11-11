<!--  --><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Checkout - Toko Teknologi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">
    <style>
        .cart-item {
            transition: all 0.3s ease;
            border-radius: 10px;
        }
        .cart-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .product-img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
        }
        .quantity-control {
            max-width: 120px;
        }
        .btn-update {
            transition: all 0.2s ease;
        }
        .btn-update:hover {
            transform: scale(1.1);
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-4">
        <h3 class="mb-4">
            <i class='bx bxs-cart-alt'></i> Checkout
        </h3>

        @if(session('error'))
            <div class="alert alert-danger">
                <i class='bx bx-error-circle'></i> {{ session('error') }}
            </div>
        @endif

        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Daftar Produk</h5>
                        @foreach($keranjangs as $item)
                            <div class="cart-item mb-3 p-3 bg-white" data-item="{{ $item->id_keranjang }}">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        @if($item->produk->gambar)
                                            <img src="{{ asset('storage/' . $item->produk->gambar) }}" 
                                                 class="product-img" alt="{{ $item->produk->nama_produk }}">
                                        @else
                                            <img src="{{ $item->produk->default_image }}" 
                                                 class="product-img" alt="{{ $item->produk->nama_produk }}">
                                        @endif
                                    </div>
                                    <div class="col">
                                        <h5 class="mb-1">{{ $item->produk->nama_produk }}</h5>
                                        <p class="mb-1 text-success">Rp {{ number_format($item->produk->harga, 0, ',', '.') }}</p>
                                        <div class="d-flex align-items-center">
                                            <div class="quantity-control input-group input-group-sm">
                                                <button class="btn btn-outline-secondary btn-update" 
                                                        onclick="updateQuantity({{ $item->id_keranjang }}, -1)">
                                                    <i class='bx bx-minus'></i>
                                                </button>
                                                <input type="number" class="form-control text-center" 
                                                       value="{{ $item->jumlah }}" min="1" max="{{ $item->produk->stok }}"
                                                       onchange="updateQuantity({{ $item->id_keranjang }}, this.value, true)">
                                                <button class="btn btn-outline-secondary btn-update" 
                                                        onclick="updateQuantity({{ $item->id_keranjang }}, 1)">
                                                    <i class='bx bx-plus'></i>
                                                </button>
                                            </div>
                                            <button class="btn btn-link text-danger ms-3" 
                                                    onclick="removeItem({{ $item->id_keranjang }})">
                                                <i class='bx bx-trash'></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <h5 class="mb-0 text-success" id="subtotal-{{ $item->id_keranjang }}">
                                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Ringkasan Pembayaran</h5>

                        @php
                            $diskons = \App\Models\Diskon::where('status', 'active')
                                ->where('tanggal_mulai', '<=', now())
                                ->where('tanggal_berakhir', '>=', now())
                                ->get();
                        @endphp

                        @if($diskons->count() > 0)
                        <div class="mb-3">
                            <label for="diskon-select-checkout" class="form-label">Pilih Diskon (Opsional)</label>
                            <select class="form-select" id="diskon-select-checkout">
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
                            <span id="total-items">{{ $keranjangs->sum('jumlah') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span id="subtotal-amount">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3" id="diskon-row-checkout" style="display: none;">
                            <span>Diskon:</span>
                            <span id="diskon-amount-checkout">-Rp 0</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="h6">Total Pembayaran:</span>
                            <span class="h5 text-success" id="total-amount">
                                Rp {{ number_format($total, 0, ',', '.') }}
                            </span>
                        </div>

                        <form action="{{ route('pelanggan.checkout.process') }}" method="POST" id="checkout-form">
                            @csrf
                            <input type="hidden" name="id_diskon" id="selected-diskon-checkout" value="">
                            <div class="mb-3">
                                <label for="metode_pembayaran" class="form-label">Pilih Metode Pembayaran</label>
                                <select class="form-select" name="metode_pembayaran" id="metode_pembayaran" required>
                                    <option value="qris" selected>QRIS (scan QR)</option>
                                    <option value="cod">COD (Bayar di Tempat)</option>
                                    <option value="cash">Tunai</option>
                                    <option value="dana">DANA</option>
                                    <option value="ovo">OVO</option>
                                    <option value="gopay">GoPay</option>
                                    <option value="bank_transfer">Transfer Bank</option>
                                </select>
                                <div class="form-text">Pilih QRIS untuk melihat QR setelah transaksi dibuat; pilih lainnya untuk instruksi/petunjuk pembayaran.</div>
                            </div>

                            <button type="submit" class="btn btn-success w-100 mb-3">
                                <i class='bx bx-check-circle'></i> Lanjutkan Pembayaran
                            </button>
                            <a href="{{ route('pelanggan.dashboard') }}" class="btn btn-link text-decoration-none w-100">
                                <i class='bx bx-arrow-back'></i> Lanjut Belanja
                            </a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateQuantity(id, change, isDirectInput = false) {
            let input = document.querySelector(`[data-item="${id}"] input`);
            let currentValue = parseInt(input.value);
            let newValue;
            
            if (isDirectInput) {
                newValue = parseInt(change);
            } else {
                newValue = currentValue + parseInt(change);
            }

            // Ensure value is within bounds
            newValue = Math.max(1, Math.min(newValue, parseInt(input.max)));
            
            fetch(`/pelanggan/cart/update/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ jumlah: newValue })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    input.value = data.jumlah;
                    document.querySelector(`#subtotal-${id}`).textContent = data.subtotal_formatted;
                    document.querySelector('#total-amount').textContent = data.total_formatted;
                    document.querySelector('#total-items').textContent = data.total_items;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mengupdate jumlah');
            });
        }

        function removeItem(id) {
            if (!confirm('Apakah Anda yakin ingin menghapus item ini?')) {
                return;
            }

            fetch(`/pelanggan/cart/remove/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.querySelector(`[data-item="${id}"]`).remove();
                    document.querySelector('#total-amount').textContent = data.total_formatted;
                    document.querySelector('#total-items').textContent = data.total_items;

                    // Redirect if cart is empty
                    if (data.total_items === 0) {
                        window.location.href = '{{ route('pelanggan.dashboard') }}';
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menghapus item');
            });
        }

        // Prevent form submission on enter key in quantity input
        document.querySelectorAll('.quantity-control input').forEach(input => {
            input.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    this.blur();
                }
            });
        });

        // Diskon calculation for checkout
        document.getElementById('diskon-select-checkout').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const diskonId = selectedOption.value;
            const jenis = selectedOption.getAttribute('data-jenis');
            const nilai = parseFloat(selectedOption.getAttribute('data-nilai')) || 0;
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
                document.getElementById('diskon-row-checkout').style.display = 'flex';
            } else {
                document.getElementById('diskon-row-checkout').style.display = 'none';
            }

            document.getElementById('diskon-amount-checkout').textContent = '-Rp ' + formatNumber(diskonAmount);
            document.getElementById('total-amount').textContent = 'Rp ' + formatNumber(totalSetelahDiskon);
            document.getElementById('selected-diskon-checkout').value = diskonId;
        });

        function formatNumber(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        }
    </script>
</body>
</html>
