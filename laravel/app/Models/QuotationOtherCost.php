<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationOtherCost extends Model
{
    protected $table = 'quotation_other_costs';
    protected $primaryKey = 'id_biaya';

    protected $fillable = [
        'nomor_quotation',
        'nama_biaya',
        'jumlah_biaya',
    ];

    protected $casts = [
        'jumlah_biaya' => 'decimal:2',
    ];

    /**
     * Relasi dengan Quotation
     */
    public function quotation()
    {
        return $this->belongsTo(Quotation::class, 'nomor_quotation', 'nomor_quotation');
    }
}