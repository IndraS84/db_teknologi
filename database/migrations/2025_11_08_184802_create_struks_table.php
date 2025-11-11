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
        Schema::create('struks', function (Blueprint $table) {
            $table->bigIncrements('id_struk');
            $table->foreignId('id_transaksi')->unique()->constrained('transaksis', 'id_transaksi')->onDelete('cascade');
            $table->dateTime('tanggal_cetak');
            $table->decimal('total_harga', 10, 2);
            $table->string('metode_pembayaran');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('struks');
    }
};
