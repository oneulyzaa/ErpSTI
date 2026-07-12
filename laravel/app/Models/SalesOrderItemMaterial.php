<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesOrderItemMaterial extends Model
{
    protected $table = 'sales_order_item_materials';
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
        'jumlah_material' => 'decimal:2',
        'harga_material' => 'decimal:2',
    ];

    /**
     * Relasi dengan SalesOrderItem
     */
    public function salesOrderItem(): BelongsTo
    {
        return $this->belongsTo(SalesOrderItem::class, 'id_item', 'id_item');
    }

    /**
     * Relasi dengan Material
     */
    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class, 'id_material', 'id_material');
    }
}