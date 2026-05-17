<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'do_number',
        'client_id',
        'sales_order_id',
        'so_number',
        'date',
        'delivery_date',
        'client_name',
        'client_company',
        'client_attention',
        'client_cc',
        'client_email',
        'destination_address',
        'description',
        'status',
        'notes',
    ];

    protected $casts = [
        'date'          => 'date',
        'delivery_date' => 'date',
    ];

    public function client()
    {
        return $this->belongsTo(ClientModel::class, 'client_id');
    }

    public function items()
    {
        return $this->hasMany(DeliveryOrderItem::class)->orderBy('sort_order');
    }

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public static function generateDONumber(): string
    {
        $prefix = 'DO-' . now()->format('Ym') . '-';
        $last   = static::where('do_number', 'like', $prefix . '%')
            ->orderByDesc('do_number')
            ->value('do_number');

        $next = $last ? (int) substr($last, -4) + 1 : 1;
        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
