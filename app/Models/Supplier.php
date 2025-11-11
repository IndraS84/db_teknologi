<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_supplier';

    protected $fillable = [
        'nama_supplier',
        'alamat',
        'no_telp',
    ];

    /**
     * Get all produk for this supplier.
     */
    public function produks(): HasMany
    {
        return $this->hasMany(Produk::class, 'id_supplier', 'id_supplier');
    }

    /**
     * Get all barang masuk for this supplier.
     */
    public function barangMasuks(): HasMany
    {
        return $this->hasMany(BarangMasuk::class, 'id_supplier', 'id_supplier');
    }
}
