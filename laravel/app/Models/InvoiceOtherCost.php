<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceOtherCost extends Model
{
    protected $fillable = [
        'invoice_id',
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

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
