<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('quotation_item_materials', function (Blueprint $table) {
            $table->integer('sort_order')->after('subtotal')->default(0);
        });
        Schema::table('sales_order_item_materials', function (Blueprint $table) {
            $table->integer('sort_order')->after('subtotal')->default(0);
        });
        Schema::table('delivery_order_item_materials', function (Blueprint $table) {
            $table->integer('sort_order')->after('subtotal')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotation_item_materials', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
        Schema::table('sales_order_item_materials', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
        Schema::table('delivery_order_item_materials', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};
