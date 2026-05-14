<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_number',
        'project_name',
        'client_id',
        'date',
        'valid_until',
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
        'subtotal',
        'total',
        'status',
        'notes',
    ];

    protected $casts = [
        'date'             => 'date',
        'valid_until'      => 'date',
        'subtotal_material'=> 'decimal:2',
        'subtotal_labor'   => 'decimal:2',
        'subtotal'         => 'decimal:2',
        'total'            => 'decimal:2',
    ];

    public function client()
    {
        return $this->belongsTo(ClientModel::class, 'client_id');
    }

    public function items()
    {
        return $this->hasMany(QuotationItem::class)->orderBy('sort_order');
    }

    public function labors()
    {
        return $this->hasMany(QuotationLabor::class)->orderBy('sort_order');
    }

    public static function generateQuoteNumber(): string
    {
        $prefix = 'QUO-' . now()->format('Ym') . '-';
        $last   = static::where('quote_number', 'like', $prefix . '%')
            ->orderByDesc('quote_number')
            ->value('quote_number');

        $next = $last ? (int) substr($last, -4) + 1 : 1;
        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
