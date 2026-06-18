<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'sales_order_id',
        'so_number',
        'nomor_po',
        'date',
        'due_date',
        'client_name',
        'client_company',
        'client_attention',
        'client_cc',
        'client_email',
        'client_address',
        'description',
        'subtotal',
        'subtotal_labor',
        'subtotal_other_cost',
        'discount',
        'tax_percentage',
        'tax_amount',
        'total',
        'status',
        'notes',
        'term_and_condition',
    ];

    protected $casts = [
        'date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'subtotal_labor' => 'decimal:2',
        'subtotal_other_cost' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(InvoiceItem::class)->orderBy('sort_order');
    }
    public function labors()
    {
        return $this->hasMany(InvoiceLabor::class)->orderBy('sort_order');
    }

    public function otherCosts()
    {
        return $this->hasMany(InvoiceOtherCost::class)->orderBy('sort_order');
    }


    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }

    public static function generateInvoiceNumber(): string
    {
        $prefix = 'INV-' . now()->format('Ym') . '-';
        $last = static::where('invoice_number', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('invoice_number');

        $next = $last ? (int) substr($last, -4) + 1 : 1;
        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
