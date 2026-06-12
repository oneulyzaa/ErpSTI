<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationOtherCost extends Model
{
    protected $fillable = [
        'quotation_id',
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

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }
}
