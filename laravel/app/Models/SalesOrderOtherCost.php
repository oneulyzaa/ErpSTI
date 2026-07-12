<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesOrderOtherCost extends Model
{
    protected $table = 'sales_order_other_costs';
    protected $primaryKey = 'id_biaya';

    protected $fillable = [
        'nomor_salesorder',
        'nama_biaya',
        'jumlah_biaya',
    ];

    protected $casts = [
        'jumlah_biaya' => 'decimal:2',
    ];

    /**
     * Relasi dengan SalesOrder
     */
    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class, 'nomor_salesorder', 'nomor_salesorder');
    }
}