<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrderOtherCost extends Model
{
    protected $fillable = [
        'sales_order_id',
        'sort_order',
        'cost_name',
        'qty',
        'rate',
        'subtotal',
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'rate' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }
}
