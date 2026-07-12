<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $table = 'materials';
    protected $primaryKey = 'id_material';

    protected $fillable = [
        'nama_material',
        'harga_material',
        'status_material',
        'stok',
        'supplier',
        'satuan',
    ];

    protected $casts = [
        'harga_material' => 'decimal:2',
        'stok' => 'integer',
    ];
}