<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsetModel extends Model
{
    protected $table = 'assets';
    protected $fillable = [
        'nama_aset',
        'harga',
        'satuan',
        'stok',
        'supplier_from',
    ];
    
}
