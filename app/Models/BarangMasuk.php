<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BarangMasuk extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_barang_masuk';

    protected $fillable = [
        'id_produk',
        'id_supplier',
        'id_admin',
        'jumlah',
        'tanggal_masuk',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
            'tanggal_masuk' => 'date',
        ];
    }

    /**
     * Get the produk for this barang masuk.
     */
    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }

    /**
     * Get the supplier for this barang masuk.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'id_supplier', 'id_supplier');
    }

    /**
     * Get the admin who recorded this barang masuk.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'id_admin', 'id_admin');
    }
}
