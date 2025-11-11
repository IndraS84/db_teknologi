<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produk extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_produk';

    protected $fillable = [
        'nama_produk',
        'deskripsi',
        'kategori',
        'harga',
        'stok',
        'id_admin',
        'id_supplier',
        'id_diskon',
    ];

    protected function casts(): array
    {
        return [
            'harga' => 'decimal:2',
            'stok' => 'integer',
        ];
    }

    /**
     * Get the admin who manages this produk.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'id_admin', 'id_admin');
    }

    /**
     * Get the supplier for this produk.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'id_supplier', 'id_supplier');
    }

    /**
     * Get the discount for this produk.
     */
    public function diskon(): BelongsTo
    {
        return $this->belongsTo(Diskon::class, 'id_diskon', 'id_diskon');
    }

    /**
     * Get all barang masuk for this produk.
     */
    public function barangMasuks(): HasMany
    {
        return $this->hasMany(BarangMasuk::class, 'id_produk', 'id_produk');
    }

    /**
     * Get all barang keluar for this produk.
     */
    public function barangKeluars(): HasMany
    {
        return $this->hasMany(BarangKeluar::class, 'id_produk', 'id_produk');
    }

    /**
     * Get all keranjang items for this produk.
     */
    public function keranjangs(): HasMany
    {
        return $this->hasMany(Keranjang::class, 'id_produk', 'id_produk');
    }

    /**
     * Get all detail transaksi for this produk.
     */
    public function detailTransaksis(): HasMany
    {
        return $this->hasMany(DetailTransaksi::class, 'id_produk', 'id_produk');
    }
}
