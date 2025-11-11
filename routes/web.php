<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BarangKeluarController;
use App\Http\Controllers\Admin\BarangMasukController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DiskonController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\PelangganController as AdminPelangganController;
use App\Http\Controllers\Admin\ProdukController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\TransaksiController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\AdminRegisterController;
use App\Http\Controllers\Auth\PelangganLoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PelangganDashboardController;
use App\Http\Controllers\KeranjangController;

// Home: redirect to appropriate dashboard if authenticated, otherwise to login
Route::get('/', function () {
    if (auth()->guard('admin')->check()) {
        return redirect()->route('admin.dashboard');
    }

    if (auth()->guard('web')->check()) {
        return redirect()->route('pelanggan.dashboard');
    }

    return redirect()->route('login');
});

// Backwards-compatible named route 'dashboard' (some parts of the app or packages
// may call route('dashboard') without the 'admin.' or 'pelanggan.' prefix).
// This route will redirect to the appropriate dashboard depending on the guard.
Route::get('/dashboard', function () {
    if (auth()->guard('admin')->check()) {
        return redirect()->route('admin.dashboard');
    }

    if (auth()->guard('web')->check()) {
        return redirect()->route('pelanggan.dashboard');
    }

    return redirect()->route('login');
})->name('dashboard');

// Backwards-compatible named route 'profile.edit'.
// If packages or legacy views call route('profile.edit') this will redirect users
// to the appropriate profile/dashboard area based on authentication guard.
Route::get('/profile/edit', function () {
    if (auth()->guard('admin')->check()) {
        // If you later add a dedicated admin profile edit route, replace this redirect
        return redirect()->route('admin.dashboard');
    }

    if (auth()->guard('web')->check()) {
        // If you later add a pelanggan profile edit route, replace this redirect
        return redirect()->route('pelanggan.dashboard');
    }

    return redirect()->route('login');
})->name('profile.edit');

/*
|--------------------------------------------------------------------------
| Pelanggan (User) Authentication
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [PelangganLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [PelangganLoginController::class, 'login'])->name('login.post');
    Route::get('/register', [PelangganLoginController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [PelangganLoginController::class, 'register'])->name('register.post');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [PelangganLoginController::class, 'logout'])->name('logout');

    // Pelanggan dashboard
    Route::get('/pelanggan/dashboard', [PelangganDashboardController::class, 'index'])->name('pelanggan.dashboard');

    // Keranjang routes
    Route::get('/keranjang', [KeranjangController::class, 'index'])->name('keranjang.index');
    Route::post('/keranjang', [KeranjangController::class, 'store'])->name('keranjang.store');
    Route::put('/keranjang/{id}', [KeranjangController::class, 'update'])->name('keranjang.update');
    Route::delete('/keranjang/{id}', [KeranjangController::class, 'destroy'])->name('keranjang.destroy');

    // Additional keranjang routes for AJAX calls
    Route::post('/pelanggan/keranjang/update/{id}', [KeranjangController::class, 'updateCart'])->name('pelanggan.keranjang.update');
    Route::delete('/pelanggan/keranjang/remove/{id}', [KeranjangController::class, 'removeItem'])->name('pelanggan.keranjang.remove');

    // Pelanggan cart routes (for dashboard compatibility)
    Route::get('/pelanggan/cart', [KeranjangController::class, 'index'])->name('pelanggan.cart');
    Route::post('/pelanggan/cart/add/{id}', [KeranjangController::class, 'addToCart'])->name('pelanggan.cart.add');

    // Checkout routes
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('pelanggan.checkout');
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('pelanggan.checkout.process');
    Route::get('/transaksi/{id}/payment', [CheckoutController::class, 'paymentInstructions'])->name('transaksi.payment');
    Route::get('/pelanggan/transaksi/{id}/payment', [CheckoutController::class, 'paymentInstructions'])->name('pelanggan.transaksi.payment');
    Route::post('/transaksi/{id}/payment', [CheckoutController::class, 'uploadProof'])->name('transaksi.uploadProof');
    Route::get('/transaksi/{id}', [CheckoutController::class, 'show'])->name('pelanggan.transaksi.show');
    // List transaksi for pelanggan
    Route::get('/transaksi', [CheckoutController::class, 'transactions'])->name('pelanggan.transaksi.index');

    Route::get('/transaksi/{id}/struk', [CheckoutController::class, 'showStruk'])->name('pelanggan.transaksi.struk');
    // add more pelanggan routes here (orders, cart, profile)
});

/*
|--------------------------------------------------------------------------
| Admin Authentication & Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    // Admin guest (login and register)
    Route::middleware(\App\Http\Middleware\RedirectIfAuthenticated::class . ':admin')->group(function () {
        Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminLoginController::class, 'login'])->name('login.post');
        Route::get('/register', [AdminRegisterController::class, 'showRegistrationForm'])->name('register');
        Route::post('/register', [AdminRegisterController::class, 'register'])->name('register.post');
    });

    // Admin authenticated routes
    Route::middleware(\App\Http\Middleware\AdminMiddleware::class)->group(function () {
        Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout');

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Admin actions for transaksi
    Route::post('transaksi/{id}/confirm', [TransaksiController::class, 'confirmPayment'])->name('transaksi.confirm');
    Route::get('transaksi/{id}/struk', [TransaksiController::class, 'generateStruk'])->name('transaksi.struk');

        // Resource Routes (admin area)
        Route::resource('admin', AdminController::class);
        Route::resource('pelanggan', AdminPelangganController::class);
        Route::resource('supplier', SupplierController::class);
        Route::resource('produk', ProdukController::class);
        Route::resource('diskon', DiskonController::class);
        Route::resource('barang-masuk', BarangMasukController::class);
        Route::resource('barang-keluar', BarangKeluarController::class);
        Route::resource('transaksi', TransaksiController::class);
        Route::resource('laporan', LaporanController::class);
    });
});
