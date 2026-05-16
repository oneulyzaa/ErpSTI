<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientModel extends Model
{
    protected $table = 'clients';
    protected $fillable = [
        'id_perusahaan',
        'nama_perusahaan',
        'email_perusahaan',
        'nama_kontak_perusahaan',
        'npwp_perusahaan',
        'alamat_pengiriman_perusahaan',
        'nomor_telepon_pengiriman',
        'alamat_faktur_perusahaan',
        'nomor_telepon_faktur',
        'alamat_efaktur_perusahaan',
        'nomor_rekening_perusahaan',
        'created_by',
    ];
}
