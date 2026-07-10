<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel Customer (dokumentasi: Customer)
        Schema::create('customers', function (Blueprint $table) {
            $table->unsignedInteger('id')->unique(); // belum auto_increment dulu
            $table->char('id_customer', 5)->primary();
            // $table->id('id'); // auto_increment
            // $table->string('kode_customer', 50)->unique();
            $table->string('nama_perusahaan');
            $table->string('nama_kontak', 100);
            $table->string('email_perusahaan', 100)->nullable();
            $table->text('alamat_perusahaan');
            $table->text('alamat_faktur')->nullable();
            $table->text('alamat_efaktur')->nullable();
            $table->string('telepon_faktur', 20)->nullable();
            $table->string('telepon_efaktur', 20)->nullable();
            $table->string('rekening_perusahaan', 50)->nullable();
            $table->string('npwp_perusahaan', 50)->nullable();
            $table->timestamps();
        });
        DB::statement('ALTER TABLE customers MODIFY id INT UNSIGNED NOT NULL AUTO_INCREMENT');

        // Tabel Material (dokumentasi: Material)
        Schema::create('materials', function (Blueprint $table) {
            $table->id('id_material');
            $table->string('nama_material', 255);
            $table->decimal('harga_material', 15, 2);
            $table->string('status_material', 50); // Tersedia, Habis
            $table->integer('stok');
            $table->string('supplier', 255)->nullable();
            $table->string('satuan', 50); // Kg, Meter, Pcs
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materials');
        Schema::dropIfExists('customers');
    }
};