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
        'quotation_id',
        'quote_number',
        'nomor_po',
        'project_name',
        'date',
        'due_date',
        'client_id',
        'client_name',
        'client_company',
        'client_attention',
        'client_cc',
        'client_email',
        'client_address',
        'description',
        'description_of_work',
        'subtotal_material',
        'subtotal_labor',
        'subtotal_other_cost',
        'subtotal_before_discount',
        'discount',
        'subtotal',
        'tax_percentage',
        'tax_amount',
        'total',
        'status',
        'amount_paid',
        'amount_due',
        'notes',
        'term_and_condition',
    ];

    protected $casts = [
        'date' => 'date',
        'due_date' => 'date',
        'subtotal_material' => 'decimal:2',
        'subtotal_labor' => 'decimal:2',
        'subtotal_other_cost' => 'decimal:2',
        'subtotal_before_discount' => 'decimal:2',
        'discount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'amount_due' => 'decimal:2',
    ];

    public function calculateFromSalesOrder(SalesOrder $salesOrder): self
    {
        $this->subtotal_material = $salesOrder->subtotal_material;
        $this->subtotal_labor = $salesOrder->subtotal_labor;
        $this->subtotal_other_cost = $salesOrder->subtotal_other_cost;
        $this->discount = $salesOrder->discount;
        $this->tax_percentage = $salesOrder->tax_percentage;
        
        $this->subtotal_before_discount = $this->subtotal_material + $this->subtotal_labor + $this->subtotal_other_cost;
        $this->subtotal = $this->subtotal_before_discount - $this->discount;
        $this->tax_amount = $this->subtotal * ($this->tax_percentage / 100);
        $this->total = $this->subtotal + $this->tax_amount;
        $this->amount_due = $this->total;
        $this->amount_paid = 0;
        
        return $this;
    }

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function client()
    {
        return $this->belongsTo(ClientModel::class, 'client_id');
    }

    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }

    public function getItemsAttribute()
    {
        return $this->salesOrder ? $this->salesOrder->items : collect();
    }

    public function getLaborsAttribute()
    {
        return $this->salesOrder ? $this->salesOrder->labors : collect();
    }

    public function getOtherCostsAttribute()
    {
        return $this->salesOrder ? $this->salesOrder->otherCosts : collect();
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