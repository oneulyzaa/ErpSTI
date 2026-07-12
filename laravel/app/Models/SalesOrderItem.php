<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesOrderItem extends Model
{
    protected $table = 'sales_order_items';
    protected $primaryKey = 'id_item';

    protected $fillable = [
        'nomor_salesorder',
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
     * Relasi dengan SalesOrder
     */
    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class, 'nomor_salesorder', 'nomor_salesorder');
    }

    /**
     * Relasi dengan SalesOrderItemMaterial
     */
    public function materials(): HasMany
    {
        return $this->hasMany(SalesOrderItemMaterial::class, 'id_item', 'id_item');
    }
}