<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel Utama Delivery Orders
        Schema::create('delivery_orders', function (Blueprint $table) {
            $table->id();
            $table->string('do_number')->unique();
            $table->foreignId('sales_order_id')->nullable()->constrained('sales_orders')->nullOnDelete();
            $table->string('so_number')->nullable();
            $table->string('nomor_po')->nullable();
            $table->string('project_name')->nullable();
            $table->date('date');
            $table->date('delivery_date')->nullable();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->string('client_name')->nullable();
            $table->string('client_company')->nullable();
            $table->string('client_attention')->nullable();
            $table->string('client_cc')->nullable();
            $table->string('client_email')->nullable();
            $table->text('destination_address')->nullable();
            $table->text('description')->nullable();
            // Nilai agregat
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->string('status')->default('draft');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Delivery Order Items
        Schema::create('delivery_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_order_id')->constrained('delivery_orders')->cascadeOnDelete();
            $table->integer('sort_order')->default(0);
            $table->string('item_name');
            $table->text('description')->nullable();
            $table->string('unit')->default('Unit');
            $table->decimal('qty', 10, 2)->default(1);
            $table->decimal('qty_delivered', 10, 2)->default(0);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->timestamps();
        });

        // Delivery Order Item Materials
        Schema::create('delivery_order_item_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_order_item_id')->constrained('delivery_order_items')->cascadeOnDelete();
            $table->foreignId('asset_id')->nullable()->constrained('assets')->nullOnDelete();
            $table->string('material_name');
            $table->string('satuan')->default('pcs');
            $table->decimal('qty_required', 10, 2)->default(0);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_order_item_materials');
        Schema::dropIfExists('delivery_order_items');
        Schema::dropIfExists('delivery_orders');
    }
};