<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel Tanda_Terima_Pembayaran / Receipts (dokumentasi: Tanda_Terima_Pembayaran)
        Schema::create('receipts', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->unique(); // belum auto_increment dulu
            $table->string('nomor_receipt', 50)->primary();
            $table->string('nomor_invoice', 50);
            $table->string('nama_project', 255);
            $table->string('nomor_po', 100)->nullable();
            $table->date('tanggal_bayar');
            $table->string('metode_bayar', 50);
            $table->decimal('jumlah_bayar', 15, 2);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('nomor_invoice')->references('nomor_invoice')->on('invoices')->onUpdate('cascade');
        });
        DB::statement('ALTER TABLE receipts MODIFY id INT UNSIGNED NOT NULL AUTO_INCREMENT');

    }

    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};