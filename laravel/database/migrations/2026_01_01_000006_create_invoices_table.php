<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel Invoice (dokumentasi: Invoice)
        Schema::create('invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->unique(); // belum auto_increment dulu
            $table->string('nomor_invoice', 50)->primary();
            $table->string('nomor_salesorder', 50);
            $table->string('nama_project', 255);
            $table->string('referensi_po', 100)->nullable();
            $table->date('tanggal_invoice');
            $table->date('jatuh_tempo');
            $table->decimal('subtotal_produksi', 15, 2);
            $table->decimal('subtotal_material', 15, 2);
            $table->decimal('subtotal_labor', 15, 2);
            $table->decimal('subtotal_lainlain', 15, 2);
            $table->decimal('diskon', 15, 2);
            $table->decimal('pajak', 15, 2);
            $table->decimal('grandtotal', 15, 2);
            $table->string('status_pembayaran', 50);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('nomor_salesorder')->references('nomor_salesorder')->on('sales_orders')->onUpdate('cascade');
        });
        DB::statement('ALTER TABLE invoices MODIFY id INT UNSIGNED NOT NULL AUTO_INCREMENT');

    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};