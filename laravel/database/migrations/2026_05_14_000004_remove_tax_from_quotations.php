<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->decimal('tax_percentage', 5, 2)->default(0)->change();
            $table->decimal('tax_amount', 15, 2)->default(0)->change();
        });
    }

    public function down(): void
    {
        // Restore previous defaults if rolling back
        Schema::table('quotations', function (Blueprint $table) {
            $table->decimal('tax_percentage', 5, 2)->default(11)->change();
            $table->decimal('tax_amount', 15, 2)->default(0)->change();
        });
    }
};
