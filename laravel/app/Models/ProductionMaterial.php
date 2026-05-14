<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionMaterial extends Model
{
    protected $fillable = [
        'production_item_id',
        'asset_id',
        'nama_bahan_baku',
        'qty_required',
        'satuan',
    ];

    protected $casts = [
        'qty_required' => 'decimal:2',
    ];

    public function productionItem()
    {
        return $this->belongsTo(ProductionItem::class);
    }

    public function asset()
    {
        return $this->belongsTo(AsetModel::class, 'asset_id');
    }
}
