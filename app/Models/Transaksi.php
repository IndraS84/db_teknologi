<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Transaksi extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_transaksi';

    protected $fillable = [
        'id_pelanggan',
        'id_diskon',
        'tanggal_transaksi',
        'total_harga',
        'total_setelah_diskon',
        'metode_pembayaran',
        'status_transaksi',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_transaksi' => 'datetime',
            'total_harga' => 'decimal:2',
            'total_setelah_diskon' => 'decimal:2',
        ];
    }

    /**
     * Get the pelanggan for this transaksi.
     */
    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }

    /**
     * Get the discount used in this transaksi.
     */
    public function diskon(): BelongsTo
    {
        return $this->belongsTo(Diskon::class, 'id_diskon', 'id_diskon');
    }

    /**
     * Get all detail transaksi for this transaksi.
     */
    public function detailTransaksis(): HasMany
    {
        return $this->hasMany(DetailTransaksi::class, 'id_transaksi', 'id_transaksi');
    }

    /**
     * Get the struk for this transaksi.
     */
    public function struk(): HasOne
    {
        return $this->hasOne(Struk::class, 'id_transaksi', 'id_transaksi');
    }
}
