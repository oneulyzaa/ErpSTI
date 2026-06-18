<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invoice_other_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->integer('sort_order')->default(0);
            $table->string('cost_name');
            $table->decimal('qty', 10, 2)->default(1);
            $table->decimal('rate', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->timestamps();
        });

        if (!Schema::hasColumn('invoices', 'subtotal_other_cost')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->decimal('subtotal_other_cost', 15, 2)->default(0)->after('subtotal_labor');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_other_costs');

        if (Schema::hasColumn('invoices', 'subtotal_other_cost')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->dropColumn('subtotal_other_cost');
            });
        }
    }
};
