<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            // Add client_id FK referencing clients table
            $table->foreignId('client_id')->nullable()->after('project_name')
                  ->constrained('clients')->nullOnDelete();

            // Add client_address to store concatenated address from master client
            $table->text('client_address')->nullable()->after('client_email');
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropColumn(['client_id', 'client_address']);
        });
    }
};
