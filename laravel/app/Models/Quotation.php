<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_number',
        'date',
        'valid_until',
        'customer_id',
        'client_name',
        'client_company',
        'client_attention',
        'client_cc',
        'client_email',
        'description_of_work',
        'subtotal',
        'tax_percentage',
        'tax_amount',
        'total',
        'status',
        'notes',
    ];

    protected $casts = [
        'date'           => 'date',
        'valid_until'    => 'date',
        'subtotal'       => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'tax_amount'     => 'decimal:2',
        'total'          => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(QuotationItem::class)->orderBy('sort_order');
    }

    /**
     * Auto-generate quote number: QUO-YYYYMM-XXXX
     */
    public static function generateQuoteNumber(): string
    {
        $prefix = 'QUO-' . now()->format('Ym') . '-';
        $last   = static::where('quote_number', 'like', $prefix . '%')
            ->orderByDesc('quote_number')
            ->value('quote_number');

        $next = $last ? (int) substr($last, -4) + 1 : 1;

        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'draft'    => '<span class="badge badge-secondary">Draft</span>',
            'sent'     => '<span class="badge badge-info">Terkirim</span>',
            'approved' => '<span class="badge badge-success">Disetujui</span>',
            'rejected' => '<span class="badge badge-danger">Ditolak</span>',
            'expired'  => '<span class="badge badge-warning">Kadaluarsa</span>',
            default    => '<span class="badge badge-secondary">-</span>',
        };
    }
}
