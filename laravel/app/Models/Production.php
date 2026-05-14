<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Production extends Model
{
    protected $fillable = [
        'production_number',
        'sales_order_id',
        'so_number',
        'project_name',
        'client_company',
        'date',
        'target_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'date'        => 'date',
        'target_date' => 'date',
    ];

    public function items()
    {
        return $this->hasMany(ProductionItem::class)->orderBy('sort_order');
    }

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public static function generateProductionNumber(): string
    {
        $prefix = 'PRD-' . now()->format('Ym') . '-';
        $last   = static::where('production_number', 'like', $prefix . '%')
            ->orderByDesc('production_number')
            ->value('production_number');

        $next = $last ? (int) substr($last, -4) + 1 : 1;
        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
