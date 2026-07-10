<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel SalesOrder (dokumentasi: SalesOrder)
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->unsignedInteger('id')->unique(); // belum auto_increment dulu
            $table->string('nomor_salesorder', 50)->primary();
            $table->unsignedBigInteger('id_staff');
            $table->unsignedBigInteger('id_client');
            $table->string('nama_project', 255);
            $table->string('referensi_po', 100)->nullable();
            $table->date('tanggal_pembuatan');
            $table->decimal('subtotal_produksi', 15, 2);
            $table->decimal('subtotal_material', 15, 2);
            $table->decimal('subtotal_labor', 15, 2);
            $table->decimal('subtotal_lainlain', 15, 2);
            $table->decimal('diskon', 15, 2);
            $table->decimal('pajak', 15, 2);
            $table->decimal('grandtotal', 15, 2);
            $table->text('termin')->nullable();
            $table->string('status', 50);
            $table->string('lampiran', 255)->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('id_staff')->references('id')->on('users');
            $table->foreign('id_client')->references('id')->on('customers');
        });
        DB::statement('ALTER TABLE sales_orders MODIFY id INT UNSIGNED NOT NULL AUTO_INCREMENT');

        // Tabel SalesOrder_Item (dokumentasi: SalesOrder_Item)
        Schema::create('sales_order_items', function (Blueprint $table) {
            $table->id('id_item');
            $table->string('nomor_salesorder', 50);
            $table->string('nama_item', 255);
            $table->text('deskripsi_item')->nullable();
            $table->integer('jumlah_item');
            $table->string('satuan', 50);
            $table->decimal('harga_item', 15, 2);
            $table->timestamps();

            $table->foreign('nomor_salesorder')->references('nomor_salesorder')->on('sales_orders')->onUpdate('cascade')->onDelete('cascade');
        });

        // Tabel SalesOrder_ItemMaterial (dokumentasi: SalesOrder_ItemMaterial)
        Schema::create('sales_order_item_materials', function (Blueprint $table) {
            $table->id('id_itemMaterial');
            $table->unsignedBigInteger('id_item');
            $table->unsignedBigInteger('id_material');
            $table->string('nama_material', 255);
            $table->string('satuan_material', 50);
            $table->integer('jumlah_material');
            $table->decimal('harga_material', 15, 2);
            $table->timestamps();

            $table->foreign('id_item')->references('id_item')->on('sales_order_items')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_material')->references('id_material')->on('materials');
        });

        // Tabel SalesOrder_Labor (dokumentasi: SalesOrder_Labor)
        Schema::create('sales_order_labors', function (Blueprint $table) {
            $table->id('id_labor');
            $table->string('nomor_salesorder', 50);
            $table->string('nama_labor', 255);
            $table->integer('jumlah_sdm');
            $table->integer('jumlah_hari');
            $table->decimal('rate_hari', 15, 2);
            $table->timestamps();

            $table->foreign('nomor_salesorder')->references('nomor_salesorder')->on('sales_orders')->onUpdate('cascade')->onDelete('cascade');
        });

        // Tabel SalesOrder_OtherCost (dokumentasi: SalesOrder_OtherCost)
        Schema::create('sales_order_other_costs', function (Blueprint $table) {
            $table->id('id_biaya');
            $table->string('nomor_salesorder', 50);
            $table->string('nama_biaya', 255);
            $table->decimal('jumlah_biaya', 15, 2);
            $table->timestamps();

            $table->foreign('nomor_salesorder')->references('nomor_salesorder')->on('sales_orders')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_order_other_costs');
        Schema::dropIfExists('sales_order_labors');
        Schema::dropIfExists('sales_order_item_materials');
        Schema::dropIfExists('sales_order_items');
        Schema::dropIfExists('sales_orders');
    }
};