@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">Keranjang Belanja</h1>

            @if($keranjang->isEmpty())
            <div class="alert alert-info">
                Keranjang belanja Anda kosong.
            </div>
            @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th>Jumlah</th>
                            <th>Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($keranjang as $item)
                        <tr id="cart-row-{{ $item->id_keranjang }}">
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($item->produk->gambar)
                                        <img src="{{ asset('storage/' . $item->produk->gambar) }}" alt="{{ $item->produk->nama }}" class="me-3" style="width: 64px; height: 64px; object-fit: cover;">
                                    @else
                                        <img src="{{ asset('images/products/default.jpg') }}" alt="{{ $item->produk->nama }}" class="me-3" style="width: 64px; height: 64px; object-fit: cover;">
                                    @endif
                                    <div>
                                        <h6 class="mb-0">{{ $item->produk->nama }}</h6>
                                        <small class="text-muted">Stok: {{ $item->produk->stok }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>Rp {{ number_format($item->produk->harga, 0, ',', '.') }}</td>
                            <td style="width: 200px">
                                <div class="input-group">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="updateQuantity({{ $item->id_keranjang }}, -1)">-</button>
                                    <input type="number" class="form-control form-control-sm text-center quantity-input" value="{{ $item->jumlah }}" min="1" max="{{ $item->produk->stok }}" onchange="updateCart({{ $item->id_keranjang }}, this.value)">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="updateQuantity({{ $item->id_keranjang }}, 1)">+</button>
                                </div>
                            </td>
                            <td class="subtotal" id="subtotal-{{ $item->id_keranjang }}">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm" onclick="removeItem({{ $item->id_keranjang }})">
                                    <i class='bx bx-trash'></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Total:</strong></td>
                            <td colspan="2" id="cart-total">Rp {{ number_format($keranjang->sum('subtotal'), 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="{{ url('/') }}" class="btn btn-outline-primary">
                    <i class='bx bx-left-arrow-alt'></i> Lanjut Belanja
                </a>
                <a href="{{ url('/checkout') }}" class="btn btn-primary">
                    Checkout <i class='bx bx-right-arrow-alt'></i>
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function updateQuantity(id, change) {
    const input = document.querySelector(`#cart-row-${id} .quantity-input`);
    const newValue = parseInt(input.value) + change;
    if (newValue >= 1 && newValue <= parseInt(input.max)) {
        input.value = newValue;
        updateCart(id, newValue);
    }
}

function updateCart(id, quantity) {
    fetch(`/keranjang/update/${id}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            jumlah: quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.querySelector(`#subtotal-${id}`).textContent = data.subtotal_formatted;
            document.querySelector('#cart-total').textContent = data.total_formatted;
            updateCartCount();
        } else {
            alert(data.error || 'Terjadi kesalahan');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan');
    });
}

function removeItem(id) {
    if (confirm('Apakah Anda yakin ingin menghapus item ini?')) {
        fetch(`/keranjang/remove/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.querySelector(`#cart-row-${id}`).remove();
                document.querySelector('#cart-total').textContent = data.total_formatted;
                updateCartCount();

                // Check if cart is empty
                if (document.querySelectorAll('tbody tr').length === 0) {
                    location.reload();
                }
            } else {
                alert(data.error || 'Terjadi kesalahan');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan');
        });
    }
}

function updateCartCount() {
    fetch('/keranjang/count')
        .then(response => response.json())
        .then(data => {
            const cartCount = document.querySelector('#cart-count');
            if (cartCount) {
                cartCount.textContent = data.cart_count;
            }
        })
        .catch(error => console.error('Error:', error));
}
</script>
@endpush