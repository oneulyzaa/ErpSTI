<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            if (!Schema::hasColumn('invoice_items', 'part_no')) {
                $table->string('part_no')->nullable()->after('item_name');
            }

            if (!Schema::hasColumn('invoice_items', 'dpp')) {
                $table->decimal('dpp', 15, 2)->default(0)->after('subtotal');
            }

            if (!Schema::hasColumn('invoice_items', 'discount')) {
                $table->decimal('discount', 15, 2)->default(0)->after('dpp');
            }

            if (!Schema::hasColumn('invoice_items', 'vat')) {
                $table->decimal('vat', 15, 2)->default(0)->after('discount');
            }

            if (!Schema::hasColumn('invoice_items', 'total_amount')) {
                $table->decimal('total_amount', 15, 2)->default(0)->after('vat');
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            if (Schema::hasColumn('invoice_items', 'total_amount')) {
                $table->dropColumn('total_amount');
            }
            if (Schema::hasColumn('invoice_items', 'vat')) {
                $table->dropColumn('vat');
            }
            if (Schema::hasColumn('invoice_items', 'discount')) {
                $table->dropColumn('discount');
            }
            if (Schema::hasColumn('invoice_items', 'dpp')) {
                $table->dropColumn('dpp');
            }
            if (Schema::hasColumn('invoice_items', 'part_no')) {
                $table->dropColumn('part_no');
            }
        });
    }
};