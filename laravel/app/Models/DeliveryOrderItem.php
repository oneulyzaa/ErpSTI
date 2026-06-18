<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryOrderItem extends Model
{
    protected $fillable = [
        'delivery_order_id',
        'sort_order',
        'item_name',
        'description',
        'unit',
        'qty',
    ];

    protected $casts = [
        'qty' => 'decimal:2',
    ];

    public function deliveryOrder()
    {
        return $this->belongsTo(DeliveryOrder::class);
    }

    public function materials()
    {
        return $this->hasMany(DeliveryOrderItemMaterial::class)->orderBy('sort_order');
    }
}
