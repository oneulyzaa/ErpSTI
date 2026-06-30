<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel Receipts (Penerimaan / Bukti Pembayaran)
        // HANYA 1 TABEL SAJA - SESUAI PERMINTAAN
        // other_costs disimpan sebagai JSON
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_number')->unique();
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
            $table->string('invoice_number')->nullable();
            $table->string('nomor_po')->nullable();
            $table->string('project_name')->nullable();
            $table->date('date');
            $table->date('payment_date')->nullable();
            $table->string('client_name')->nullable();
            $table->string('client_company')->nullable();
            $table->string('client_attention')->nullable();
            $table->string('client_email')->nullable();
            $table->string('payment_method')->nullable(); // cash, transfer, cheque, etc.
            $table->string('reference_number')->nullable(); // nomor transfer/cheque
            $table->text('description')->nullable();
            
            // ======================================================
            // NILAI AGREGAT - DISIMPAN LANGSUNG DI TABEL receipts
            // ======================================================
            
            // Other Costs (detail disimpan sebagai JSON, totalnya disimpan di sini)
            $table->json('other_costs_json')->nullable(); // Menyimpan detail other costs sebagai array JSON
            $table->decimal('subtotal_other_cost', 15, 2)->default(0);
            
            // Diskon
            $table->decimal('discount', 15, 2)->default(0);
            
            // Jumlah pembayaran
            $table->decimal('amount', 15, 2)->default(0);
            
            $table->enum('status', ['draft', 'confirmed', 'cancelled'])->default('draft');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};