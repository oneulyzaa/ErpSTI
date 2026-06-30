<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Assets / Bahan Baku / Inventaris
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_code')->unique()->nullable();
            $table->string('name');
            $table->string('satuan')->default('pcs');
            $table->decimal('qty', 15, 2)->default(0);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->text('description')->nullable();
            $table->enum('status', ['available', 'low', 'out'])->default('available');
            $table->timestamps();
        });

        // Clients / Pelanggan
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('client_code')->unique()->nullable();
            $table->string('company_name');
            $table->string('contact_name')->nullable();
            $table->string('attn')->nullable();
            $table->string('cc')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
        Schema::dropIfExists('assets');
    }
};