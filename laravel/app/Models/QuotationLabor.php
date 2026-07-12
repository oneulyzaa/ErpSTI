<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationLabor extends Model
{
    protected $table = 'quotation_labors';
    protected $primaryKey = 'id_labor';

    protected $fillable = [
        'nomor_quotation',
        'nama_labor',
        'jumlah_sdm',
        'jumlah_hari',
        'rate_hari',
    ];

    protected $casts = [
        'jumlah_sdm' => 'integer',
        'jumlah_hari' => 'integer',
        'rate_hari' => 'decimal:2',
    ];

    /**
     * Relasi dengan Quotation
     */
    public function quotation()
    {
        return $this->belongsTo(Quotation::class, 'nomor_quotation', 'nomor_quotation');
    }
}