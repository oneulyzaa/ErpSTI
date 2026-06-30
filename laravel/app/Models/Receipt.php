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
        'payment_date',
        'client_name',
        'client_company',
        'client_attention',
        'client_email',
        'payment_method',
        'reference_number',
        'description',
        'other_costs_json',
        'subtotal_other_cost',
        'discount',
        'amount',
        'status',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'payment_date' => 'date',
        'other_costs_json' => 'array', // Cast JSON ke array
        'subtotal_other_cost' => 'decimal:2',
        'discount' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Accessor untuk mendapatkan other costs (kompatibel dengan kode lama)
     * Mengembalikan collection agar bisa di-loop seperti relasi hasMany
     */
    public function getOtherCostsAttribute()
    {
        $costs = $this->other_costs_json ?? [];
        return collect($costs)->map(function ($cost, $index) {
            return (object) [
                'sort_order' => $cost['sort_order'] ?? ($index + 1),
                'cost_name' => $cost['cost_name'] ?? '',
                'qty' => $cost['qty'] ?? 1,
                'rate' => $cost['rate'] ?? 0,
                'subtotal' => $cost['subtotal'] ?? 0,
            ];
        });
    }

    /**
     * Helper: Hitung subtotal_other_cost dari other_costs_json
     */
    public function calculateSubtotalOtherCost(): self
    {
        $costs = $this->other_costs_json ?? [];
        $total = 0;
        
        foreach ($costs as $cost) {
            $qty = $cost['qty'] ?? 1;
            $rate = $cost['rate'] ?? 0;
            $subtotal = $qty * $rate;
            $total += $subtotal;
        }
        
        $this->subtotal_other_cost = $total;
        return $this;
    }

    /**
     * Helper: Set other costs dari array (seperti syncOtherCosts lama)
     */
    public function setOtherCosts(array $otherCosts): self
    {
        $costs = [];
        
        foreach ($otherCosts as $i => $cost) {
            if (empty($cost['cost_name'])) {
                continue;
            }
            
            $qty = $cost['qty'] ?? 1;
            $rate = $cost['rate'] ?? 0;
            $subtotal = $qty * $rate;
            
            $costs[] = [
                'sort_order' => $i + 1,
                'cost_name' => $cost['cost_name'],
                'qty' => $qty,
                'rate' => $rate,
                'subtotal' => $subtotal,
            ];
        }
        
        $this->other_costs_json = $costs;
        $this->calculateSubtotalOtherCost();
        
        return $this;
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