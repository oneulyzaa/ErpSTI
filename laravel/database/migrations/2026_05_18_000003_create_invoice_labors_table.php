<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_labors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->integer('sort_order')->default(0);
            $table->string('labor_name');
            $table->integer('mp')->default(1);
            $table->decimal('days', 8, 2)->default(1);
            $table->decimal('rate', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->timestamps();
        });

        // Add subtotal_labor column to invoices table
        if (!Schema::hasColumn('invoices', 'subtotal_labor')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->decimal('subtotal_labor', 15, 2)->default(0)->after('subtotal');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_labors');

        if (Schema::hasColumn('invoices', 'subtotal_labor')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->dropColumn('subtotal_labor');
            });
        }
    }
};
