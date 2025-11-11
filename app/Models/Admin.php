<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'admins';
    protected $primaryKey = 'id_admin';

    protected $fillable = [
        'nama_admin',
        'username',
        'password',
        'email',
    ];

    protected $hidden = [
        'password',
    ];

    public function getAuthIdentifierName()
    {
        return 'id_admin';
    }

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * Get all produk managed by this admin.
     */
    public function produks(): HasMany
    {
        return $this->hasMany(Produk::class, 'id_admin', 'id_admin');
    }

    /**
     * Get all barang masuk recorded by this admin.
     */
    public function barangMasuks(): HasMany
    {
        return $this->hasMany(BarangMasuk::class, 'id_admin', 'id_admin');
    }

    /**
     * Get all barang keluar recorded by this admin.
     */
    public function barangKeluars(): HasMany
    {
        return $this->hasMany(BarangKeluar::class, 'id_admin', 'id_admin');
    }

    /**
     * Get all laporan created by this admin.
     */
    public function laporans(): HasMany
    {
        return $this->hasMany(Laporan::class, 'id_admin', 'id_admin');
    }
}
