<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'so_number',
        'project_name',
        'client_id',
        'quotation_id',
        'quote_number',
        'nomor_po',
        'date',
        'delivery_date',
        'customer_id',
        'client_name',
        'client_company',
        'client_attention',
        'client_cc',
        'client_email',
        'client_address',
        'description_of_work',
        'subtotal_material',
        'subtotal_labor',
        'subtotal_other_cost',
        'discount',
        'subtotal',
        'tax_percentage',
        'tax_amount',
        'total',
        'status',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'delivery_date' => 'date',
        'subtotal_material' => 'decimal:2',
        'subtotal_labor' => 'decimal:2',
        'subtotal_other_cost' => 'decimal:2',
        'discount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function client()
    {
        return $this->belongsTo(ClientModel::class, 'client_id');
    }

    public function items()
    {
        return $this->hasMany(SalesOrderItem::class)->orderBy('sort_order');
    }
    public function labors()
    {
        return $this->hasMany(SalesOrderLabor::class)->orderBy('sort_order');
    }

    public function otherCosts()
    {
        return $this->hasMany(SalesOrderOtherCost::class)->orderBy('sort_order');
    }


    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function productions()
    {
        return $this->hasMany(Production::class);
    }

    public function deliveryOrders()
    {
        return $this->hasMany(DeliveryOrder::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public static function generateSONumber(): string
    {
        $prefix = 'SO-' . now()->format('Ym') . '-';
        $last = static::where('so_number', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('so_number');

        $next = $last ? (int) substr($last, -4) + 1 : 1;
        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
