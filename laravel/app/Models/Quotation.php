<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;

    protected $table = 'quotations';
    protected $primaryKey = 'nomor_quotation';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nomor_quotation',
        'id_staff',
        'id_client',
        'nama_project',
        'tanggal_pembuatan',
        'valid_sampai',
        'subtotal_produksi',
        'subtotal_material',
        'subtotal_labor',
        'subtotal_lainlain',
        'grandtotal',
        'diskon',
        'pajak',
        'termin',
        'keterangan',
        'lampiran',
        'status',
    ];

    protected $casts = [
        'tanggal_pembuatan' => 'date',
        'valid_sampai' => 'date',
        'subtotal_produksi' => 'decimal:2',
        'subtotal_material' => 'decimal:2',
        'subtotal_labor' => 'decimal:2',
        'subtotal_lainlain' => 'decimal:2',
        'grandtotal' => 'decimal:2',
        'diskon' => 'decimal:2',
        'pajak' => 'decimal:2',
    ];

    /**
     * Relasi dengan User (staff yang membuat quotation)
     */
    public function staff()
    {
        return $this->belongsTo(User::class, 'id_staff');
    }

    /**
     * Relasi dengan Customer/Client
     */
    public function client()
    {
        return $this->belongsTo(ClientModel::class, 'id_client', 'id_customer');
    }

    /**
     * Relasi dengan QuotationItem
     */
    public function items()
    {
        return $this->hasMany(QuotationItem::class, 'nomor_quotation', 'nomor_quotation');
    }

    /**
     * Relasi dengan QuotationLabor
     */
    public function labors()
    {
        return $this->hasMany(QuotationLabor::class, 'nomor_quotation', 'nomor_quotation');
    }

    /**
     * Relasi dengan QuotationOtherCost
     */
    public function otherCosts()
    {
        return $this->hasMany(QuotationOtherCost::class, 'nomor_quotation', 'nomor_quotation');
    }

    /**
     * Generate nomor quotation otomatis
     */
    public static function generateQuoteNumber(): string
    {
        $prefix = 'QUO-' . now()->format('Ym') . '-';
        $last = static::where('nomor_quotation', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('nomor_quotation');

        $next = $last ? (int) substr($last, -4) + 1 : 1;
        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}