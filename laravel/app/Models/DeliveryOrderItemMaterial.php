<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryOrderItemMaterial extends Model
{
    protected $fillable = [
        'delivery_order_item_id',
        'asset_id',
        'material_name',
        'qty_required',
        'satuan',
        'unit_price',
        'subtotal',
        'sort_order',
    ];

    protected $casts = [
        'qty_required' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function deliveryOrderItem()
    {
        return $this->belongsTo(DeliveryOrderItem::class);
    }

    public function asset()
    {
        return $this->belongsTo(AsetModel::class, 'asset_id');
    }
}
