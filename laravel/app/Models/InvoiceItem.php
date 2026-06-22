<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id',
        'sort_order',
        'item_name',
        'part_no',
        'description',
        'unit',
        'qty',
        'unit_price',
        'subtotal',
        'dpp',
        'discount',
        'vat',
        'total_amount',
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'dpp' => 'decimal:2',
        'discount' => 'decimal:2',
        'vat' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function materials()
    {
        return $this->hasMany(InvoiceItemMaterial::class)->orderBy('sort_order');
    }
}
