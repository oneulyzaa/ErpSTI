<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionItem extends Model
{
    protected $fillable = [
        'production_id',
        'sales_order_item_id',
        'product_name',
        'product_qty',
        'unit',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'product_qty' => 'decimal:2',
    ];

    public function production()
    {
        return $this->belongsTo(Production::class);
    }

    public function salesOrderItem()
    {
        return $this->belongsTo(SalesOrderItem::class);
    }

    public function materials()
    {
        return $this->hasMany(ProductionMaterial::class)->orderBy('id');
    }
}
