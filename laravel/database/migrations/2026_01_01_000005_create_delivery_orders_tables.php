<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel DeliveryOrder (dokumentasi: DeliveryOrder)
        Schema::create('delivery_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->unique(); // belum auto_increment dulu
            $table->string('nomor_deliveryorder', 50)->primary();
            $table->unsignedBigInteger('id_staff');
            $table->unsignedInteger('id_client');
            $table->string('nomor_salesorder', 50);
            $table->string('nama_project', 255);
            $table->string('referensi_po', 100)->nullable();
            $table->date('tanggal_pembuatan');
            $table->date('tanggal_pengiriman');
            $table->string('status', 50);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('id_staff')->references('id')->on('users');
            $table->foreign('id_client')->references('id')->on('customers');
            $table->foreign('nomor_salesorder')->references('nomor_salesorder')->on('sales_orders')->onUpdate('cascade');
        });
        DB::statement('ALTER TABLE delivery_orders MODIFY id INT UNSIGNED NOT NULL AUTO_INCREMENT');

        // Tabel DeliveryOrder_Item (dokumentasi: DeliveryOrder_Item)
        Schema::create('delivery_order_items', function (Blueprint $table) {
            $table->id('id_item');
            $table->string('nomor_deliveryorder', 50);
            $table->string('nama_item', 255);
            $table->text('deskripsi_item')->nullable();
            $table->integer('jumlah_item');
            $table->string('satuan', 50);
            $table->decimal('harga_item', 15, 2);
            $table->timestamps();

            $table->foreign('nomor_deliveryorder')->references('nomor_deliveryorder')->on('delivery_orders')->onUpdate('cascade')->onDelete('cascade');
        });

        // Tabel DeliveryOrder_ItemMaterial (dokumentasi: DeliveryOrder_ItemMaterial)
        Schema::create('delivery_order_item_materials', function (Blueprint $table) {
            $table->id('id_itemMaterial');
            $table->unsignedBigInteger('id_item');
            $table->unsignedBigInteger('id_material');
            $table->string('nama_material', 255);
            $table->string('satuan_material', 50);
            $table->integer('jumlah_material');
            $table->decimal('harga_material', 15, 2);
            $table->timestamps();

            $table->foreign('id_item')->references('id_item')->on('delivery_order_items')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_material')->references('id_material')->on('materials');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_order_item_materials');
        Schema::dropIfExists('delivery_order_items');
        Schema::dropIfExists('delivery_orders');
    }
};