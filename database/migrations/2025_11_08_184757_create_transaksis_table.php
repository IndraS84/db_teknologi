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
        Schema::create('transaksis', function (Blueprint $table) {
            $table->bigIncrements('id_transaksi');
            $table->foreignId('id_pelanggan')->constrained('pelanggans', 'id_pelanggan')->onDelete('cascade');
            $table->foreignId('id_diskon')->nullable()->constrained('diskons', 'id_diskon')->onDelete('set null');
            $table->dateTime('tanggal_transaksi');
            $table->decimal('total_harga', 10, 2);
            $table->decimal('total_setelah_diskon', 10, 2);
            $table->string('metode_pembayaran');
            $table->string('status_transaksi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
