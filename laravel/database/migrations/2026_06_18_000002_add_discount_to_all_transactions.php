<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Quotations
        Schema::table('quotations', function (Blueprint $table) {
            $table->decimal('discount', 15, 2)->nullable()->default(0)->after('subtotal_other_cost');
        });

        // 2. Sales Orders
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->decimal('discount', 15, 2)->nullable()->default(0)->after('subtotal_other_cost');
        });

        // 3. Invoices
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('discount', 15, 2)->nullable()->default(0)->after('subtotal_other_cost');
        });

        // 4. Receipts
        Schema::table('receipts', function (Blueprint $table) {
            $table->decimal('discount', 15, 2)->nullable()->default(0)->after('subtotal_other_cost');
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn('discount');
        });
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropColumn('discount');
        });
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('discount');
        });
        Schema::table('receipts', function (Blueprint $table) {
            $table->dropColumn('discount');
        });
    }
};