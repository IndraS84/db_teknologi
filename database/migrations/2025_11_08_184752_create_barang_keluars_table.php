<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('barang_keluars', function (Blueprint $table) {
            $table->bigIncrements('id_barang_keluar');
            $table->foreignId('id_produk')->constrained('produks', 'id_produk')->onDelete('cascade');
            $table->foreignId('id_pelanggan')->constrained('pelanggans', 'id_pelanggan')->onDelete('cascade');
            $table->foreignId('id_admin')->constrained('admins', 'id_admin')->onDelete('cascade');
            $table->integer('jumlah');
            $table->date('tanggal_keluar');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_keluars');
    }
};
