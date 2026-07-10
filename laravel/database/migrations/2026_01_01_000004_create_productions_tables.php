<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel Produksi (dokumentasi: Produksi)
        Schema::create('productions', function (Blueprint $table) {
            $table->unsignedInteger('id')->unique(); // belum auto_increment dulu
            $table->string('nomor_produksi', 50)->primary();
            $table->string('nomor_salesorder', 50);
            $table->string('PIC', 255);
            $table->date('tanggal_mulai');
            $table->date('estimasi_selesai')->nullable();
            $table->string('status_produksi', 50);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('nomor_salesorder')->references('nomor_salesorder')->on('sales_orders')->onUpdate('cascade');
        });
        DB::statement('ALTER TABLE productions MODIFY id INT UNSIGNED NOT NULL AUTO_INCREMENT');

        // Tabel Produksi_Item (dokumentasi: Produksi_Item)
        Schema::create('production_items', function (Blueprint $table) {
            $table->id('id_item');
            $table->string('nomor_produksi', 50);
            $table->string('nama_item', 255);
            $table->text('deskripsi_item')->nullable();
            $table->integer('jumlah_item');
            $table->string('satuan', 50);
            $table->decimal('harga_item', 15, 2);
            $table->timestamps();

            $table->foreign('nomor_produksi')->references('nomor_produksi')->on('productions')->onUpdate('cascade')->onDelete('cascade');
        });

        // Tabel Produksi_ItemMaterial (dokumentasi: Produksi_ItemMaterial)
        Schema::create('production_item_materials', function (Blueprint $table) {
            $table->id('id_itemMaterial');
            $table->unsignedBigInteger('id_item');
            $table->unsignedBigInteger('id_material');
            $table->string('nama_material', 255);
            $table->string('satuan_material', 50);
            $table->integer('jumlah_material');
            $table->decimal('harga_material', 15, 2);
            $table->timestamps();

            $table->foreign('id_item')->references('id_item')->on('production_items')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_material')->references('id_material')->on('materials');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_item_materials');
        Schema::dropIfExists('production_items');
        Schema::dropIfExists('productions');
    }
};