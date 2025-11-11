<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Laporan extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_laporan';

    protected $fillable = [
        'id_admin',
        'periode',
        'total_penjualan',
        'tanggal_cetak',
    ];

    protected function casts(): array
    {
        return [
            'total_penjualan' => 'decimal:2',
            'tanggal_cetak' => 'date',
        ];
    }

    /**
     * Get the admin who created this laporan.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'id_admin', 'id_admin');
    }
}
