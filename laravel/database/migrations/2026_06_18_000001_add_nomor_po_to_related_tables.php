<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Sales Orders
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->string('nomor_po')->nullable()->after('quote_number');
        });

        // 2. Productions
        Schema::table('productions', function (Blueprint $table) {
            $table->string('nomor_po')->nullable()->after('so_number');
        });

        // 3. Delivery Orders
        Schema::table('delivery_orders', function (Blueprint $table) {
            $table->string('nomor_po')->nullable()->after('so_number');
        });

        // 4. Invoices
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('nomor_po')->nullable()->after('so_number');
        });

        // 5. Receipts
        Schema::table('receipts', function (Blueprint $table) {
            $table->string('nomor_po')->nullable()->after('invoice_number');
        });
    }

    public function down(): void
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropColumn('nomor_po');
        });
        Schema::table('productions', function (Blueprint $table) {
            $table->dropColumn('nomor_po');
        });
        Schema::table('delivery_orders', function (Blueprint $table) {
            $table->dropColumn('nomor_po');
        });
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('nomor_po');
        });
        Schema::table('receipts', function (Blueprint $table) {
            $table->dropColumn('nomor_po');
        });
    }
};