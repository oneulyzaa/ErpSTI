<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel Utama Invoices (HANYA INI SAJA - SESUAI PERMINTAAN)
        // Semua nilai agregat disimpan langsung di tabel invoices
        // Data detail material, labor, other cost bisa diambil dari sales_order
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('sales_order_id')->nullable()->constrained('sales_orders')->nullOnDelete();
            $table->string('so_number')->nullable();
            $table->foreignId('quotation_id')->nullable()->constrained('quotations')->nullOnDelete();
            $table->string('quote_number')->nullable();
            $table->string('nomor_po')->nullable();
            $table->string('project_name')->nullable();
            $table->date('date');
            $table->date('due_date')->nullable();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->string('client_name')->nullable();
            $table->string('client_company')->nullable();
            $table->string('client_attention')->nullable();
            $table->string('client_cc')->nullable();
            $table->string('client_email')->nullable();
            $table->text('client_address')->nullable();
            $table->text('description')->nullable();
            $table->text('description_of_work')->nullable();
            
            // ======================================================
            // NILAI AGREGAT - DISIMPAN LANGSUNG DI TABEL INVOICES
            // ======================================================
            
            // Biaya Produksi + Material (Total dari material)
            $table->decimal('subtotal_material', 15, 2)->default(0);
            
            // Biaya Labor / Tenaga Kerja
            $table->decimal('subtotal_labor', 15, 2)->default(0);
            
            // Biaya Lain-Lain
            $table->decimal('subtotal_other_cost', 15, 2)->default(0);
            
            // Subtotal sebelum diskon
            $table->decimal('subtotal_before_discount', 15, 2)->default(0);
            
            // Diskon
            $table->decimal('discount', 15, 2)->default(0);
            
            // Subtotal setelah diskon
            $table->decimal('subtotal', 15, 2)->default(0);
            
            // Pajak
            $table->decimal('tax_percentage', 5, 2)->default(11);
            $table->decimal('tax_amount', 15, 2)->default(0);
            
            // Total Keseluruhan
            $table->decimal('total', 15, 2)->default(0);
            
            // Status Pembayaran
            $table->enum('status', ['draft', 'sent', 'partial', 'paid', 'overdue', 'cancelled'])->default('draft');
            
            // Jumlah yang sudah dibayar
            $table->decimal('amount_paid', 15, 2)->default(0);
            
            // Sisa yang harus dibayar
            $table->decimal('amount_due', 15, 2)->default(0);
            
            $table->text('notes')->nullable();
            $table->text('term_and_condition')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};