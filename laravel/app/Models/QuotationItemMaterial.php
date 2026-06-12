<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationItemMaterial extends Model
{
    protected $fillable = [
        'quotation_item_id',
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

    public function quotationItem()
    {
        return $this->belongsTo(QuotationItem::class);
    }

    public function asset()
    {
        return $this->belongsTo(AsetModel::class, 'asset_id');
    }
}
