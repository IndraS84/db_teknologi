<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pelanggan extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = "pelanggans";
    protected $primaryKey = 'id_pelanggan';

    protected $fillable = [
        'nama_pelanggan',
        'alamat',
        'no_hp',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    public function getAuthIdentifierName()
    {
        return 'id_pelanggan';
    }

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * Get all barang keluar for this pelanggan.
     */
    public function barangKeluars(): HasMany
    {
        return $this->hasMany(BarangKeluar::class, 'id_pelanggan', 'id_pelanggan');
    }

    /**
     * Get all keranjang items for this pelanggan.
     */
    public function keranjangs(): HasMany
    {
        return $this->hasMany(Keranjang::class, 'id_pelanggan', 'id_pelanggan');
    }

    /**
     * Get all transaksi for this pelanggan.
     */
    public function transaksis(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'id_pelanggan', 'id_pelanggan');
    }
}
