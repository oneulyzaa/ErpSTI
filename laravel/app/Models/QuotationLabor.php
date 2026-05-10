<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationLabor extends Model
{
    protected $fillable = [
        'quotation_id',
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

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }
}
