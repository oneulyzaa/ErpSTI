<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrderLabor extends Model
{
    protected $fillable = [
        'sales_order_id',
        'sort_order',
        'labor_name',
        'mp',
        'days',
        'rate',
        'subtotal',
    ];

    protected $casts = [
        'days'     => 'decimal:2',
        'rate'     => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }
}
