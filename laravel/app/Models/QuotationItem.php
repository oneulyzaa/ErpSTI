<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationItem extends Model
{
    protected $table = 'quotation_items';
    protected $primaryKey = 'id_item';

    protected $fillable = [
        'nomor_quotation',
        'nama_item',
        'deskripsi_item',
        'jumlah_item',
        'satuan',
        'harga_item',
    ];

    protected $casts = [
        'jumlah_item' => 'integer',
        'harga_item' => 'decimal:2',
    ];

    /**
     * Relasi dengan Quotation
     */
    public function quotation()
    {
        return $this->belongsTo(Quotation::class, 'nomor_quotation', 'nomor_quotation');
    }

    /**
     * Relasi dengan QuotationItemMaterial
     */
    public function materials()
    {
        return $this->hasMany(QuotationItemMaterial::class, 'id_item', 'id_item');
    }
}