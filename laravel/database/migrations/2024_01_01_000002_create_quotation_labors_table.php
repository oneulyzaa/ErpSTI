<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotation_labors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained()->cascadeOnDelete();
            $table->integer('sort_order')->default(0);
            $table->string('labor_name');           // Mechanical Design, dll
            $table->integer('mp')->default(1);       // Man Power
            $table->decimal('days', 8, 2)->default(1);
            $table->decimal('rate', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0); // mp * days * rate
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotation_labors');
    }
};
