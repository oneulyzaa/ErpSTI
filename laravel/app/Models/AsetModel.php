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
        'status',
    ];

    protected $casts = [
        'harga' => 'integer',
        'stok' => 'integer',
        'status' => 'integer',
    ];

    /**
     * Relasi dengan QuotationItemMaterial
     */
    public function quotationItemMaterials()
    {
        return $this->hasMany(QuotationItemMaterial::class, 'asset_id');
    }

    /**
     * Relasi dengan SalesOrderItemMaterial
     */
    public function salesOrderItemMaterials()
    {
        return $this->hasMany(SalesOrderItemMaterial::class, 'asset_id');
    }

    /**
     * Relasi dengan ProductionMaterial
     */
    public function productionMaterials()
    {
        return $this->hasMany(ProductionMaterial::class, 'asset_id');
    }

    /**
     * Relasi dengan DeliveryOrderItemMaterial
     */
    public function deliveryOrderItemMaterials()
    {
        return $this->hasMany(DeliveryOrderItemMaterial::class, 'asset_id');
    }
}
