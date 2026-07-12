<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesOrderLabor extends Model
{
    protected $table = 'sales_order_labors';
    protected $primaryKey = 'id_labor';

    protected $fillable = [
        'nomor_salesorder',
        'nama_labor',
        'jumlah_sdm',
        'jumlah_hari',
        'rate_hari',
    ];

    protected $casts = [
        'jumlah_sdm' => 'integer',
        'jumlah_hari' => 'integer',
        'rate_hari' => 'decimal:2',
    ];

    /**
     * Relasi dengan SalesOrder
     */
    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class, 'nomor_salesorder', 'nomor_salesorder');
    }
}