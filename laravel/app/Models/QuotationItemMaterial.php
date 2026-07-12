<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationItemMaterial extends Model
{
    protected $table = 'quotation_item_materials';
    protected $primaryKey = 'id_itemMaterial';

    protected $fillable = [
        'id_item',
        'id_material',
        'nama_material',
        'satuan_material',
        'jumlah_material',
        'harga_material',
    ];

    protected $casts = [
        'jumlah_material' => 'integer',
        'harga_material' => 'decimal:2',
    ];

    /**
     * Relasi dengan QuotationItem
     */
    public function quotationItem()
    {
        return $this->belongsTo(QuotationItem::class, 'id_item', 'id_item');
    }

    /**
     * Relasi dengan Material
     */
    public function material()
    {
        return $this->belongsTo(Material::class, 'id_material', 'id_material');
    }
}