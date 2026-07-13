<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesOrder extends Model
{
    use HasFactory;

    protected $table = 'sales_orders';
    protected $primaryKey = 'nomor_salesorder';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nomor_salesorder',
        'id_staff',
        'id_client',
        'nomor_quotation',
        'nama_project',
        'nomor_po',
        'tanggal_pembuatan',
        'subtotal_produksi',
        'subtotal_material',
        'subtotal_labor',
        'subtotal_lainlain',
        'diskon',
        'pajak',
        'grandtotal',
        'termin',
        'status',
        'lampiran',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_pembuatan' => 'date',
        'subtotal_produksi' => 'decimal:2',
        'subtotal_material' => 'decimal:2',
        'subtotal_labor' => 'decimal:2',
        'subtotal_lainlain' => 'decimal:2',
        'diskon' => 'decimal:2',
        'pajak' => 'decimal:2',
        'grandtotal' => 'decimal:2',
    ];

    /**
     * Relasi dengan User (staff yang membuat sales order)
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id');
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
     * Relasi dengan Quotation
     */
    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class, 'nomor_quotation', 'nomor_quotation');
    }

    /**
     * Relasi dengan SalesOrderItem
     */
    public function items(): HasMany
    {
        return $this->hasMany(SalesOrderItem::class, 'nomor_salesorder', 'nomor_salesorder');
    }

    /**
     * Relasi dengan SalesOrderLabor
     */
    public function labors(): HasMany
    {
        return $this->hasMany(SalesOrderLabor::class, 'nomor_salesorder', 'nomor_salesorder');
    }

    /**
     * Relasi dengan SalesOrderOtherCost
     */
    public function otherCosts(): HasMany
    {
        return $this->hasMany(SalesOrderOtherCost::class, 'nomor_salesorder', 'nomor_salesorder');
    }

    /**
     * Relasi dengan Production
     */
    public function productions(): HasMany
    {
        return $this->hasMany(Production::class, 'nomor_salesorder', 'nomor_salesorder');
    }

    /**
     * Relasi dengan DeliveryOrder
     */
    public function deliveryOrders(): HasMany
    {
        return $this->hasMany(DeliveryOrder::class, 'nomor_salesorder', 'nomor_salesorder');
    }

    /**
     * Relasi dengan Invoice
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'nomor_salesorder', 'nomor_salesorder');
    }

    /**
     * Generate nomor sales order otomatis
     */
    public static function generateSONumber(): string
    {
        $prefix = 'SO-' . now()->format('Ym') . '-';
        $last = static::where('nomor_salesorder', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('nomor_salesorder');

        $next = $last ? (int) substr($last, -4) + 1 : 1;
        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}