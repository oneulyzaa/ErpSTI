<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sales_order_other_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_order_id')->constrained('sales_orders')->cascadeOnDelete();
            $table->integer('sort_order')->default(0);
            $table->string('cost_name');
            $table->decimal('qty', 10, 2)->default(1);
            $table->decimal('rate', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->timestamps();
        });

        if (!Schema::hasColumn('sales_orders', 'subtotal_other_cost')) {
            Schema::table('sales_orders', function (Blueprint $table) {
                $table->decimal('subtotal_other_cost', 15, 2)->default(0)->after('subtotal_labor');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_order_other_costs');

        if (Schema::hasColumn('sales_orders', 'subtotal_other_cost')) {
            Schema::table('sales_orders', function (Blueprint $table) {
                $table->dropColumn('subtotal_other_cost');
            });
        }
    }
};
