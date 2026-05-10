<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            // Tambah kolom subtotal_material & subtotal_labor setelah description_of_work
            $table->decimal('subtotal_material', 15, 2)->default(0)->after('description_of_work');
            $table->decimal('subtotal_labor', 15, 2)->default(0)->after('subtotal_material');
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn(['subtotal_material', 'subtotal_labor']);
        });
    }
};
