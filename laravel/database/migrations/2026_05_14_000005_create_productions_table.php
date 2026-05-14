<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productions', function (Blueprint $table) {
            $table->id();
            $table->string('production_number')->unique();
            $table->foreignId('sales_order_id')->constrained('sales_orders')->cascadeOnDelete();
            $table->string('so_number')->nullable();
            $table->string('project_name')->nullable();
            $table->string('client_company')->nullable();
            $table->date('date');
            $table->date('target_date')->nullable();
            $table->enum('status', ['planned', 'in_progress', 'completed', 'cancelled'])->default('planned');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('production_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_id')->constrained('productions')->cascadeOnDelete();
            $table->foreignId('sales_order_item_id')->nullable()->constrained('sales_order_items')->nullOnDelete();
            $table->string('product_name');
            $table->decimal('product_qty', 10, 2)->default(1);
            $table->string('unit')->default('Unit');
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('production_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_item_id')->constrained('production_items')->cascadeOnDelete();
            $table->foreignId('asset_id')->nullable()->constrained('assets')->nullOnDelete();
            $table->string('nama_bahan_baku');
            $table->decimal('qty_required', 12, 2)->default(1);
            $table->string('satuan')->default('pcs');
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
