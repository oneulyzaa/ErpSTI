<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Production extends Model
{
    use HasFactory;

    protected $table = 'productions';
    protected $primaryKey = 'nomor_produksi';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nomor_produksi',
        'nomor_salesorder',
        'PIC',
        'tanggal_mulai',
        'estimasi_selesai',
        'status_produksi',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'estimasi_selesai' => 'date',
    ];

    /**
     * Relasi dengan SalesOrder
     */
    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class, 'nomor_salesorder', 'nomor_salesorder');
    }

    /**
     * Relasi dengan ProductionItem
     */
    public function items(): HasMany
    {
        return $this->hasMany(ProductionItem::class, 'nomor_produksi', 'nomor_produksi');
    }

    /**
     * Generate nomor produksi otomatis
     */
    public static function generateProductionNumber(): string
    {
        $prefix = 'PRD-' . now()->format('Ym') . '-';
        $last = static::where('nomor_produksi', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('nomor_produksi');

        $next = $last ? (int) substr($last, -4) + 1 : 1;
        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}