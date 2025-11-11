@extends('admin.layouts.master')

@section('title', 'Tambah Transaksi')

@section('page-title', 'Tambah Transaksi')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.transaksi.index') }}">Transaksi</a></li>
    <li class="breadcrumb-item active" aria-current="page">Tambah</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Tambah Transaksi</h4>
                <form action="{{ route('admin.transaksi.store') }}" method="POST" id="transaksiForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="id_pelanggan">Pelanggan <span class="text-danger">*</span></label>
                                <select class="form-control @error('id_pelanggan') is-invalid @enderror" id="id_pelanggan" name="id_pelanggan" required>
                                    <option value="">Pilih Pelanggan</option>
                                    @foreach($pelanggans as $pelanggan)
                                        <option value="{{ $pelanggan->id_pelanggan }}" {{ old('id_pelanggan') == $pelanggan->id_pelanggan ? 'selected' : '' }}>
                                            {{ $pelanggan->nama_pelanggan }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_pelanggan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="id_diskon">Diskon</label>
                                <select class="form-control @error('id_diskon') is-invalid @enderror" id="id_diskon" name="id_diskon">
                                    <option value="">Tidak Ada Diskon</option>
                                    @foreach($diskons as $diskon)
                                        <option value="{{ $diskon->id_diskon }}" {{ old('id_diskon') == $diskon->id_diskon ? 'selected' : '' }}>
                                            {{ $diskon->nama_diskon }} ({{ $diskon->jenis_diskon == 'persentase' ? $diskon->nilai_diskon . '%' : 'Rp ' . number_format($diskon->nilai_diskon, 0, ',', '.') }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_diskon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="metode_pembayaran">Metode Pembayaran <span class="text-danger">*</span></label>
                        <select class="form-control @error('metode_pembayaran') is-invalid @enderror" id="metode_pembayaran" name="metode_pembayaran" required>
                            <option value="">Pilih Metode</option>
                            <option value="cash" {{ old('metode_pembayaran') == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="transfer" {{ old('metode_pembayaran') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                            <option value="debit" {{ old('metode_pembayaran') == 'debit' ? 'selected' : '' }}>Debit</option>
                            <option value="kredit" {{ old('metode_pembayaran') == 'kredit' ? 'selected' : '' }}>Kredit</option>
                        </select>
                        @error('metode_pembayaran')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr>
                    <h5>Items Transaksi</h5>
                    <div id="items-container">
                        <div class="item-row row mb-3">
                            <div class="col-md-5">
                                <select class="form-control produk-select" name="items[0][id_produk]" required>
                                    <option value="">Pilih Produk</option>
                                    @foreach($produks as $produk)
                                        <option value="{{ $produk->id_produk }}" data-harga="{{ $produk->harga }}" data-stok="{{ $produk->stok }}">
                                            {{ $produk->nama_produk }} - Rp {{ number_format($produk->harga, 0, ',', '.') }} (Stok: {{ $produk->stok }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="number" class="form-control jumlah-input" name="items[0][jumlah]" placeholder="Jumlah" min="1" required>
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control subtotal-input" placeholder="Subtotal" readonly>
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-danger remove-item">×</button>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary mb-3" id="add-item">+ Tambah Item</button>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Total Harga:</label>
                                <h4 id="total-harga">Rp 0</h4>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Total Setelah Diskon:</label>
                                <h4 id="total-diskon">Rp 0</h4>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
                        <a href="{{ route('admin.transaksi.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let itemIndex = 1;

    $('#add-item').click(function() {
        const itemRow = `
            <div class="item-row row mb-3">
                <div class="col-md-5">
                    <select class="form-control produk-select" name="items[${itemIndex}][id_produk]" required>
                        <option value="">Pilih Produk</option>
                        @foreach($produks as $produk)
                            <option value="{{ $produk->id_produk }}" data-harga="{{ $produk->harga }}" data-stok="{{ $produk->stok }}">
                                {{ $produk->nama_produk }} - Rp {{ number_format($produk->harga, 0, ',', '.') }} (Stok: {{ $produk->stok }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="number" class="form-control jumlah-input" name="items[${itemIndex}][jumlah]" placeholder="Jumlah" min="1" required>
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control subtotal-input" placeholder="Subtotal" readonly>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger remove-item">×</button>
                </div>
            </div>
        `;
        $('#items-container').append(itemRow);
        itemIndex++;
    });

    $(document).on('click', '.remove-item', function() {
        if ($('.item-row').length > 1) {
            $(this).closest('.item-row').remove();
            calculateTotal();
        }
    });

    $(document).on('change', '.produk-select, .jumlah-input', function() {
        const row = $(this).closest('.item-row');
        const produkSelect = row.find('.produk-select');
        const jumlahInput = row.find('.jumlah-input');
        const subtotalInput = row.find('.subtotal-input');

        const selectedOption = produkSelect.find('option:selected');
        const harga = parseFloat(selectedOption.data('harga')) || 0;
        const jumlah = parseFloat(jumlahInput.val()) || 0;
        const stok = parseFloat(selectedOption.data('stok')) || 0;

        if (jumlah > stok) {
            alert('Stok tidak mencukupi! Stok tersedia: ' + stok);
            jumlahInput.val(stok);
            return;
        }

        const subtotal = harga * jumlah;
        subtotalInput.val('Rp ' + subtotal.toLocaleString('id-ID'));

        calculateTotal();
    });

    function calculateTotal() {
        let total = 0;
        $('.item-row').each(function() {
            const produkSelect = $(this).find('.produk-select');
            const jumlahInput = $(this).find('.jumlah-input');
            const selectedOption = produkSelect.find('option:selected');
            const harga = parseFloat(selectedOption.data('harga')) || 0;
            const jumlah = parseFloat(jumlahInput.val()) || 0;
            total += harga * jumlah;
        });

        $('#total-harga').text('Rp ' + total.toLocaleString('id-ID'));
        $('#total-diskon').text('Rp ' + total.toLocaleString('id-ID'));
    }
</script>
@endpush

