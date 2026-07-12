<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryOrderItem extends Model
{
    protected $table = 'delivery_order_items';
    protected $primaryKey = 'id_item';

    protected $fillable = [
        'nomor_deliveryorder',
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
     * Relasi dengan DeliveryOrder
     */
    public function deliveryOrder(): BelongsTo
    {
        return $this->belongsTo(DeliveryOrder::class, 'nomor_deliveryorder', 'nomor_deliveryorder');
    }

    /**
     * Relasi dengan DeliveryOrderItemMaterial
     */
    public function materials(): HasMany
    {
        return $this->hasMany(DeliveryOrderItemMaterial::class, 'id_item', 'id_item');
    }
}