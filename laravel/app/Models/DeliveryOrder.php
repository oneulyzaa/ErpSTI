<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryOrder extends Model
{
    use HasFactory;

    protected $table = 'delivery_orders';
    protected $primaryKey = 'nomor_deliveryorder';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nomor_deliveryorder',
        'id_staff',
        'id_client',
        'nomor_salesorder',
        'nama_project',
        'nomor_po',
        'tanggal_pembuatan',
        'tanggal_pengiriman',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_pembuatan' => 'date',
        'tanggal_pengiriman' => 'date',
    ];

    /**
     * Relasi dengan User (staff yang membuat delivery order)
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_staff');
    }

    /**
     * Relasi dengan Customer/Client
     * FK id_client mereferensi kolom id di tabel customers
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(ClientModel::class, 'id_client', 'id');
    }

    /**
     * Relasi dengan SalesOrder
     */
    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class, 'nomor_salesorder', 'nomor_salesorder');
    }

    /**
     * Relasi dengan DeliveryOrderItem
     */
    public function items(): HasMany
    {
        return $this->hasMany(DeliveryOrderItem::class, 'nomor_deliveryorder', 'nomor_deliveryorder');
    }

    /**
     * Generate nomor delivery order otomatis
     */
    public static function generateDONumber(): string
    {
        $prefix = 'DO-' . now()->format('Ym') . '-';
        $last = static::where('nomor_deliveryorder', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('nomor_deliveryorder');

        $next = $last ? (int) substr($last, -4) + 1 : 1;
        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}