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
            $table->string('nama_aset');
            $table->integer('harga')->default(0);
            $table->string('satuan')->default('pcs');
            $table->integer('stok')->default(0);
            $table->string('supplier_from')->nullable();
            $table->integer('status')->default(1);
            $table->timestamps();
        });

        // Clients / Pelanggan
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

    public function down(): void
    {
        Schema::dropIfExists('clients');
        Schema::dropIfExists('assets');
    }
};