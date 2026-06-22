<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'discount')) {
                $table->decimal('discount', 15, 2)->default(0)->after('tax_amount');
            }

            if (!Schema::hasColumn('invoices', 'dpp')) {
                $table->decimal('dpp', 15, 2)->default(0)->after('subtotal');
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'dpp')) {
                $table->dropColumn('dpp');
            }
        });
    }
};