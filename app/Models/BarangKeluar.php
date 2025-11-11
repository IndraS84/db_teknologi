<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BarangKeluar extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_barang_keluar';

    protected $fillable = [
        'id_produk',
        'id_pelanggan',
        'id_admin',
        'jumlah',
        'tanggal_keluar',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
            'tanggal_keluar' => 'date',
        ];
    }

    /**
     * Get the produk for this barang keluar.
     */
    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }

    /**
     * Get the pelanggan for this barang keluar.
     */
    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }

    /**
     * Get the admin who recorded this barang keluar.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'id_admin', 'id_admin');
    }
}
