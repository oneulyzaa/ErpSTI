<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrderItem extends Model
{
    protected $fillable = [
        'sales_order_id',
        'sort_order',
        'material_name',
        'description',
        'unit',
        'qty',
        'unit_price',
        'subtotal',
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function materials()
    {
        return $this->hasMany(SalesOrderItemMaterial::class)->orderBy('sort_order');
    }

    /**
     * Total subtotal dari semua materials item ini
     */
    public function getMaterialsSubtotalAttribute()
    {
        return $this->materials->sum('subtotal');
    }

    /**
     * Harga per unit dihitung dari total materials / qty item
     * (jika punya materials)
     */
    public function getCalculatedUnitPriceAttribute()
    {
        if ($this->materials->count() > 0) {
            $matSubtotal = $this->materials_subtotal;
            if ($this->qty > 0) {
                return $matSubtotal / $this->qty;
            }
            return $matSubtotal;
        }
        return $this->unit_price;
    }

    /**
     * Subtotal yang dihitung: jika punya materials, gunakan total materials
     */
    public function getCalculatedSubtotalAttribute()
    {
        if ($this->materials->count() > 0) {
            return $this->materials_subtotal;
        }
        return $this->subtotal;
    }
}
