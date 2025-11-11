<?php

use App\Models\Diskon;
use App\Models\Keranjang;
use App\Models\Pelanggan;
use App\Models\Produk;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('checkout page displays discount options', function () {
    // Create test data
    $pelanggan = Pelanggan::factory()->create();
    $produk = Produk::factory()->create(['harga' => 100000, 'stok' => 10]);
    $diskon = Diskon::factory()->create([
        'nama_diskon' => 'Test Discount 10%',
        'jenis_diskon' => 'persentase',
        'nilai_diskon' => 10,
        'status' => 'active',
        'tanggal_mulai' => now()->subDay(),
        'tanggal_berakhir' => now()->addDays(30),
    ]);

    // Add item to cart
    Keranjang::create([
        'id_pelanggan' => $pelanggan->id_pelanggan,
        'id_produk' => $produk->id_produk,
        'jumlah' => 2,
        'subtotal' => 200000,
    ]);

    $this->actingAs($pelanggan, 'web')
         ->get('/pelanggan/checkout')
         ->assertStatus(200)
         ->assertSee('Test Discount 10%')
         ->assertSee('10%');
});

test('checkout calculates discount correctly for percentage', function () {
    $pelanggan = Pelanggan::factory()->create();
    $produk = Produk::factory()->create(['harga' => 100000, 'stok' => 10]);
    $diskon = Diskon::factory()->create([
        'nama_diskon' => 'Test Discount 10%',
        'jenis_diskon' => 'persentase',
        'nilai_diskon' => 10,
        'status' => 'active',
        'tanggal_mulai' => now()->subDay(),
        'tanggal_berakhir' => now()->addDays(30),
    ]);

    Keranjang::create([
        'id_pelanggan' => $pelanggan->id_pelanggan,
        'id_produk' => $produk->id_produk,
        'jumlah' => 2,
        'subtotal' => 200000,
    ]);

    // Expected total: 100000 * 2 = 200000

    $this->actingAs($pelanggan, 'web')
         ->post('/pelanggan/checkout', [
             'metode_pembayaran' => 'qris',
             'id_diskon' => $diskon->id_diskon,
         ])
         ->assertRedirect();

    // Check that transaction was created with correct discount
    $this->assertDatabaseHas('transaksis', [
        'id_pelanggan' => $pelanggan->id_pelanggan,
        'id_diskon' => $diskon->id_diskon,
        'total_harga' => 200000,
    ]);

    // Verify the discount calculation: 200000 - 10% = 180000, plus kode unik (3 digits)
    $transaksi = \App\Models\Transaksi::where('id_pelanggan', $pelanggan->id_pelanggan)->latest()->first();
    $expectedDiscounted = 200000 - (200000 * 0.1); // 180000
    $kodeUnik = $transaksi->total_setelah_diskon - $expectedDiscounted;
    $this->assertGreaterThanOrEqual(100, $kodeUnik);
    $this->assertLessThanOrEqual(999, $kodeUnik);
});

test('checkout calculates discount correctly for fixed amount', function () {
    $pelanggan = Pelanggan::factory()->create();
    $produk = Produk::factory()->create(['harga' => 100000, 'stok' => 10]);
    $diskon = Diskon::factory()->create([
        'nama_diskon' => 'Test Discount Rp 50,000',
        'jenis_diskon' => 'nominal',
        'nilai_diskon' => 50000,
        'status' => 'active',
        'tanggal_mulai' => now()->subDay(),
        'tanggal_berakhir' => now()->addDays(30),
    ]);

    Keranjang::create([
        'id_pelanggan' => $pelanggan->id_pelanggan,
        'id_produk' => $produk->id_produk,
        'jumlah' => 2,
        'subtotal' => 200000,
    ]);

    // Expected total: 100000 * 2 = 200000

    $this->actingAs($pelanggan, 'web')
         ->post('/pelanggan/checkout', [
             'metode_pembayaran' => 'qris',
             'id_diskon' => $diskon->id_diskon,
         ])
         ->assertRedirect();

    // Check that transaction was created with correct discount
    $this->assertDatabaseHas('transaksis', [
        'id_pelanggan' => $pelanggan->id_pelanggan,
        'id_diskon' => $diskon->id_diskon,
        'total_harga' => 200000,
    ]);

    // Verify the discount calculation: 200000 - 50000 = 150000, plus kode unik (3 digits)
    $transaksi = \App\Models\Transaksi::where('id_pelanggan', $pelanggan->id_pelanggan)->latest()->first();
    $expectedDiscounted = 200000 - 50000; // 150000
    $kodeUnik = $transaksi->total_setelah_diskon - $expectedDiscounted;
    $this->assertGreaterThanOrEqual(100, $kodeUnik);
    $this->assertLessThanOrEqual(999, $kodeUnik);
});

test('checkout without discount works correctly', function () {
    $pelanggan = Pelanggan::factory()->create();
    $produk = Produk::factory()->create(['harga' => 100000, 'stok' => 10]);

    Keranjang::create([
        'id_pelanggan' => $pelanggan->id_pelanggan,
        'id_produk' => $produk->id_produk,
        'jumlah' => 2,
        'subtotal' => 200000,
    ]);

    $this->actingAs($pelanggan, 'web')
         ->post('/pelanggan/checkout', [
             'metode_pembayaran' => 'qris',
         ])
         ->assertRedirect();

    $this->assertDatabaseHas('transaksis', [
        'id_pelanggan' => $pelanggan->id_pelanggan,
        'id_diskon' => null,
        'total_harga' => 200000,
    ]);
});

test('expired discount is not available in checkout', function () {
    $pelanggan = Pelanggan::factory()->create();
    $produk = Produk::factory()->create(['harga' => 100000, 'stok' => 10]);
    $diskon = Diskon::factory()->create([
        'nama_diskon' => 'Expired Discount',
        'jenis_diskon' => 'persentase',
        'nilai_diskon' => 10,
        'status' => 'active',
        'tanggal_mulai' => now()->subDays(10),
        'tanggal_berakhir' => now()->subDay(),
    ]);

    Keranjang::create([
        'id_pelanggan' => $pelanggan->id_pelanggan,
        'id_produk' => $produk->id_produk,
        'jumlah' => 2,
        'subtotal' => 200000,
    ]);

    $this->actingAs($pelanggan, 'web')
         ->get('/pelanggan/checkout')
         ->assertStatus(200)
         ->assertDontSee('Expired Discount');
});
