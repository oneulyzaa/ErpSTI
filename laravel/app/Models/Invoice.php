<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

    protected $table = 'invoices';
    protected $primaryKey = 'nomor_invoice';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nomor_invoice',
        'nomor_salesorder',
        'nama_project',
        'referensi_po',
        'tanggal_invoice',
        'jatuh_tempo',
        'subtotal_produksi',
        'subtotal_material',
        'subtotal_labor',
        'subtotal_lainlain',
        'diskon',
        'pajak',
        'grandtotal',
        'status_pembayaran',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_invoice' => 'date',
        'jatuh_tempo' => 'date',
        'subtotal_produksi' => 'decimal:2',
        'subtotal_material' => 'decimal:2',
        'subtotal_labor' => 'decimal:2',
        'subtotal_lainlain' => 'decimal:2',
        'diskon' => 'decimal:2',
        'pajak' => 'decimal:2',
        'grandtotal' => 'decimal:2',
    ];

    /**
     * Relasi dengan SalesOrder
     */
    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class, 'nomor_salesorder', 'nomor_salesorder');
    }

    /**
     * Relasi dengan Receipt
     */
    public function receipts(): HasMany
    {
        return $this->hasMany(Receipt::class, 'nomor_invoice', 'nomor_invoice');
    }

    /**
     * Hitung dari SalesOrder
     */
    public function calculateFromSalesOrder(SalesOrder $salesOrder): self
    {
        $this->subtotal_produksi = $salesOrder->subtotal_produksi;
        $this->subtotal_material = $salesOrder->subtotal_material;
        $this->subtotal_labor = $salesOrder->subtotal_labor;
        $this->subtotal_lainlain = $salesOrder->subtotal_lainlain;
        $this->diskon = $salesOrder->diskon;
        $this->pajak = $salesOrder->pajak;
        $this->grandtotal = $salesOrder->grandtotal;

        return $this;
    }

    /**
     * Generate nomor invoice otomatis
     */
    public static function generateInvoiceNumber(): string
    {
        $prefix = 'INV-' . now()->format('Ym') . '-';
        $last = static::where('nomor_invoice', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('nomor_invoice');

        $next = $last ? (int) substr($last, -4) + 1 : 1;
        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}