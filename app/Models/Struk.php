<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Struk extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_struk';

    protected $fillable = [
        'id_transaksi',
        'tanggal_cetak',
        'total_harga',
        'metode_pembayaran',
        'detail_pembayaran',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_cetak' => 'datetime',
            'total_harga' => 'decimal:2',
        ];
    }

    /**
     * Get the transaksi for this struk.
     */
    public function transaksi(): BelongsTo
    {
        return $this->belongsTo(Transaksi::class, 'id_transaksi', 'id_transaksi');
    }
}
