<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'receipt_number',
        'invoice_id',
        'invoice_number',
        'nomor_po',
        'project_name',
        'date',
        'client_name',
        'client_company',
        'client_attention',
        'client_email',
        'description',
        'amount',
        'subtotal_other_cost',
        'discount',
        'payment_method',
        'payment_reference',
        'status',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
        'subtotal_other_cost' => 'decimal:2',
        'discount' => 'decimal:2',
    ];
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function otherCosts()
    {
        return $this->hasMany(ReceiptOtherCost::class)->orderBy('sort_order');
    }


    public static function generateReceiptNumber(): string
    {
        $prefix = 'TT-' . now()->format('Ym') . '-';
        $last = static::where('receipt_number', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('receipt_number');

        $next = $last ? (int) substr($last, -4) + 1 : 1;
        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
