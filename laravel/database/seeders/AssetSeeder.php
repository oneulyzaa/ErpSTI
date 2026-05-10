<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssetSeeder extends Seeder
{
    /**
     * Cara jalankan seeder ini:
     * 1. Buka terminal di direktori proyek Laravel Anda.
     * 2. Jalankan perintah berikut untuk menjalankan seeder:
     *    `php artisan db:seed --class=AssetSeeder`
     */
    public function run(): void
    {
        DB::table('assets')->insert([
            [
                'nama_aset' => 'LINEAR BUSHING LMU8',
                'harga' => 25000,
                'satuan' => 'pcs',
                'stok' => 50,
                'supplier_from' => 'PT Motion Linear Indonesia',
            ],
            [
                'nama_aset' => 'CONNECTOR SACC-M12MRD-4Q SH',
                'harga' => 85000,
                'satuan' => 'pcs',
                'stok' => 30,
                'supplier_from' => 'PT Phoenix Contact',
            ],
            [
                'nama_aset' => 'EJECTOR PIN SKD 61 DIA 4 X 100',
                'harga' => 15000,
                'satuan' => 'pcs',
                'stok' => 100,
                'supplier_from' => 'CV Teknik Presisi',
            ],
            [
                'nama_aset' => 'STRIPPER BOLT MSB10-10',
                'harga' => 20000,
                'satuan' => 'pcs',
                'stok' => 75,
                'supplier_from' => 'PT Mold Center',
            ],
            [
                'nama_aset' => 'BEARING 608ZZ',
                'harga' => 12000,
                'satuan' => 'pcs',
                'stok' => 200,
                'supplier_from' => 'SKF Indonesia',
            ],
            [
                'nama_aset' => 'LINEAR SHAFT 8MM X 500MM',
                'harga' => 95000,
                'satuan' => 'pcs',
                'stok' => 40,
                'supplier_from' => 'PT Linear Motion',
            ],
            [
                'nama_aset' => 'BALL SCREW SFU1605',
                'harga' => 450000,
                'satuan' => 'pcs',
                'stok' => 10,
                'supplier_from' => 'HIWIN Indonesia',
            ],
            [
                'nama_aset' => 'COUPLING FLEXIBLE 8X8',
                'harga' => 35000,
                'satuan' => 'pcs',
                'stok' => 60,
                'supplier_from' => 'CV Mekanik Jaya',
            ],
            [
                'nama_aset' => 'TIMING BELT GT2 6MM',
                'harga' => 30000,
                'satuan' => 'meter',
                'stok' => 100,
                'supplier_from' => 'PT Beltindo',
            ],
            [
                'nama_aset' => 'PULLEY GT2 20T',
                'harga' => 45000,
                'satuan' => 'pcs',
                'stok' => 50,
                'supplier_from' => 'CV Automation Part',
            ],
            [
                'nama_aset' => 'PROXIMITY SENSOR LJ12A3-4-Z/BX',
                'harga' => 65000,
                'satuan' => 'pcs',
                'stok' => 35,
                'supplier_from' => 'Omron Supplier',
            ],
            [
                'nama_aset' => 'LIMIT SWITCH ME-8108',
                'harga' => 40000,
                'satuan' => 'pcs',
                'stok' => 45,
                'supplier_from' => 'PT Switch Electric',
            ],
            [
                'nama_aset' => 'MOTOR DC 24V 100W',
                'harga' => 350000,
                'satuan' => 'pcs',
                'stok' => 15,
                'supplier_from' => 'PT Servo Teknik',
            ],
            [
                'nama_aset' => 'GEARBOX RATIO 1:30',
                'harga' => 500000,
                'satuan' => 'pcs',
                'stok' => 10,
                'supplier_from' => 'Gearindo',
            ],
            [
                'nama_aset' => 'ALUMINUM PROFILE 2020',
                'harga' => 75000,
                'satuan' => 'meter',
                'stok' => 80,
                'supplier_from' => 'Aluminium Teknik',
            ],
            [
                'nama_aset' => 'T-NUT M5',
                'harga' => 2000,
                'satuan' => 'pcs',
                'stok' => 500,
                'supplier_from' => 'CV Baut Murah',
            ],
            [
                'nama_aset' => 'BOLT HEX M8 X 20',
                'harga' => 1500,
                'satuan' => 'pcs',
                'stok' => 1000,
                'supplier_from' => 'CV Baut Murah',
            ],
            [
                'nama_aset' => 'NUT M8',
                'harga' => 1000,
                'satuan' => 'pcs',
                'stok' => 1000,
                'supplier_from' => 'CV Baut Murah',
            ],
            [
                'nama_aset' => 'WASHER M8',
                'harga' => 800,
                'satuan' => 'pcs',
                'stok' => 1200,
                'supplier_from' => 'CV Baut Murah',
            ],
            [
                'nama_aset' => 'LINEAR GUIDE HGH15',
                'harga' => 300000,
                'satuan' => 'pcs',
                'stok' => 20,
                'supplier_from' => 'HIWIN Indonesia',
            ],
        ]);
    }
}