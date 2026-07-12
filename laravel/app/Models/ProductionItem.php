<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductionItem extends Model
{
    protected $table = 'production_items';
    protected $primaryKey = 'id_item';

    protected $fillable = [
        'nomor_produksi',
        'nama_item',
        'deskripsi_item',
        'jumlah_item',
        'satuan',
        'harga_item',
    ];

    protected $casts = [
        'jumlah_item' => 'decimal:2',
        'harga_item' => 'decimal:2',
    ];

    /**
     * Relasi dengan Production
     */
    public function production(): BelongsTo
    {
        return $this->belongsTo(Production::class, 'nomor_produksi', 'nomor_produksi');
    }

    /**
     * Relasi dengan ProductionItemMaterial
     */
    public function materials(): HasMany
    {
        return $this->hasMany(ProductionItemMaterial::class, 'id_item', 'id_item');
    }
}