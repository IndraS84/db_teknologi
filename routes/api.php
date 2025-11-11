<?php

use App\Http\Controllers\Api\KeranjangController;
use App\Http\Controllers\Api\ProdukController;
use App\Http\Controllers\Api\TransaksiController;
use Illuminate\Support\Facades\Route;

// Produk Routes
Route::apiResource('produks', ProdukController::class);

// Keranjang Routes
Route::apiResource('keranjangs', KeranjangController::class);
Route::post('keranjangs/clear', [KeranjangController::class, 'clear']);

// Transaksi Routes
Route::apiResource('transaksis', TransaksiController::class);
Route::post('transaksis/from-cart', [TransaksiController::class, 'storeFromCart']);

