<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel Utama Productions
        Schema::create('productions', function (Blueprint $table) {
            $table->id();
            $table->string('production_number')->unique();
            $table->foreignId('sales_order_id')->nullable()->constrained('sales_orders')->nullOnDelete();
            $table->string('so_number')->nullable();
            $table->string('nomor_po')->nullable();
            $table->string('project_name')->nullable();
            $table->date('date');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('client_name')->nullable();
            $table->string('client_company')->nullable();
            $table->text('description')->nullable();
            // Nilai agregat
            $table->decimal('subtotal_material', 15, 2)->default(0);
            $table->decimal('subtotal_labor', 15, 2)->default(0);
            $table->decimal('subtotal_other_cost', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_percentage', 5, 2)->default(11);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->string('status')->default('draft');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Production Items
        Schema::create('production_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_id')->constrained('productions')->cascadeOnDelete();
            $table->integer('sort_order')->default(0);
            $table->string('item_name');
            $table->text('description')->nullable();
            $table->string('unit')->default('Unit');
            $table->decimal('qty', 10, 2)->default(1);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->timestamps();
        });

        // Production Materials
        Schema::create('production_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_item_id')->constrained('production_items')->cascadeOnDelete();
            $table->foreignId('asset_id')->nullable()->constrained('assets')->nullOnDelete();
            $table->string('material_name');
            $table->string('satuan')->default('pcs');
            $table->decimal('qty_required', 10, 2)->default(0);
            $table->decimal('qty_used', 10, 2)->default(0);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_materials');
        Schema::dropIfExists('production_items');
        Schema::dropIfExists('productions');
    }
};