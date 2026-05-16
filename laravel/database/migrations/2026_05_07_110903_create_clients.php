<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            // tabel dengan data profil dari perusahaan
            $table->string('id_perusahaan')->unique();
            $table->string('nama_perusahaan');
            $table->string('email_perusahaan')->nullable();
            $table->string('nama_kontak_perusahaan')->nullable();
            $table->string('npwp_perusahaan')->nullable();

            // tabel dengan alamat: pengiriman, faktur dan  efaktur
            $table->text('alamat_pengiriman_perusahaan')->nullable();
            $table->string('nomor_telepon_pengiriman')->nullable();

            $table->text('alamat_faktur_perusahaan')->nullable();
            $table->string('nomor_telepon_faktur')->nullable();
            $table->text('alamat_efaktur_perusahaan')->nullable();

            $table->string('nomor_rekening_perusahaan')->nullable();
            $table->string('created_by')->default('System');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
