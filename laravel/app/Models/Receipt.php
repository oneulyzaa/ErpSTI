<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Receipt extends Model
{
    use HasFactory;

    protected $table = 'receipts';
    protected $primaryKey = 'nomor_receipt';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nomor_receipt',
        'nomor_invoice',
        'nama_project',
        'nomor_po',
        'tanggal_bayar',
        'metode_bayar',
        'jumlah_bayar',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_bayar' => 'date',
        'jumlah_bayar' => 'decimal:2',
    ];

    /**
     * Relasi dengan Invoice
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'nomor_invoice', 'nomor_invoice');
    }

    /**
     * Generate nomor receipt otomatis
     */
    public static function generateReceiptNumber(): string
    {
        $prefix = 'TT-' . now()->format('Ym') . '-';
        $last = static::where('nomor_receipt', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('nomor_receipt');

        $next = $last ? (int) substr($last, -4) + 1 : 1;
        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}