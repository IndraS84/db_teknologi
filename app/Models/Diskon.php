<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Diskon extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_diskon';

    protected $fillable = [
        'nama_diskon',
        'jenis_diskon',
        'nilai_diskon',
        'tanggal_mulai',
        'tanggal_berakhir',
        'status',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'nilai_diskon' => 'decimal:2',
            'tanggal_mulai' => 'date',
            'tanggal_berakhir' => 'date',
        ];
    }

    /**
     * Get all produk with this discount.
     */
    public function produks(): HasMany
    {
        return $this->hasMany(Produk::class, 'id_diskon', 'id_diskon');
    }

    /**
     * Get all transaksi using this discount.
     */
    public function transaksis(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'id_diskon', 'id_diskon');
    }
}
