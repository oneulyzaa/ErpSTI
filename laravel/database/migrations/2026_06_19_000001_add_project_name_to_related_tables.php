<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Delivery Orders
        Schema::table('delivery_orders', function (Blueprint $table) {
            $table->string('project_name')->nullable()->after('nomor_po');
        });

        // 2. Invoices
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('project_name')->nullable()->after('nomor_po');
        });

        // 3. Receipts
        Schema::table('receipts', function (Blueprint $table) {
            $table->string('project_name')->nullable()->after('nomor_po');
        });
    }

    public function down(): void
    {
        Schema::table('delivery_orders', function (Blueprint $table) {
            $table->dropColumn('project_name');
        });
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('project_name');
        });
        Schema::table('receipts', function (Blueprint $table) {
            $table->dropColumn('project_name');
        });
    }
};