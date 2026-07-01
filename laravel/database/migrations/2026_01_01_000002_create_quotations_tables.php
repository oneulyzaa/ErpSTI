<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel Utama Quotations
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->string('quote_number')->unique();
            $table->string('project_name')->nullable();
            $table->string('nomor_po')->nullable();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->date('date');
            $table->date('valid_until')->nullable();
            $table->string('client_name')->nullable();
            $table->string('client_company')->nullable();
            $table->string('client_attention')->nullable();
            $table->string('client_cc')->nullable();
            $table->string('client_email')->nullable();
            $table->text('client_address')->nullable();
            $table->text('description_of_work')->nullable();
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
            $table->text('term_and_condition')->nullable();
            $table->timestamps();
        });

        // Quotation Items (Item Produksi)
        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained('quotations')->cascadeOnDelete();
            $table->integer('sort_order')->default(0);
            $table->string('material_name');
            $table->text('description')->nullable();
            $table->string('unit')->default('Unit');
            $table->decimal('qty', 10, 2)->default(1);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->timestamps();
        });

        // Quotation Item Materials (Material per Item)
        Schema::create('quotation_item_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_item_id')->constrained('quotation_items')->cascadeOnDelete();
            $table->foreignId('asset_id')->nullable()->constrained('assets')->nullOnDelete();
            $table->string('material_name');
            $table->string('satuan')->default('pcs');
            $table->decimal('qty_required', 10, 2)->default(0);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->timestamps();
        });

        // Quotation Labors (Biaya Tenaga Kerja)
        Schema::create('quotation_labors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained('quotations')->cascadeOnDelete();
            $table->integer('sort_order')->default(0);
            $table->string('labor_name');
            $table->integer('mp')->default(1);
            $table->decimal('days', 8, 2)->default(1);
            $table->decimal('rate', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->timestamps();
        });

        // Quotation Other Costs (Biaya Lain-Lain)
        Schema::create('quotation_other_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained('quotations')->cascadeOnDelete();
            $table->integer('sort_order')->default(0);
            $table->string('cost_name');
            $table->decimal('qty', 10, 2)->default(1);
            $table->decimal('rate', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotation_other_costs');
        Schema::dropIfExists('quotation_labors');
        Schema::dropIfExists('quotation_item_materials');
        Schema::dropIfExists('quotation_items');
        Schema::dropIfExists('quotations');
    }
};