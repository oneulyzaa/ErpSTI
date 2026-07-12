<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'password',
        'namalengkap',
        'email',
        'telepon',
        'akses',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi dengan Quotation (staff yang membuat quotation)
     */
    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class, 'id_staff');
    }

    /**
     * Relasi dengan SalesOrder (staff yang membuat sales order)
     */
    public function salesOrders(): HasMany
    {
        return $this->hasMany(SalesOrder::class, 'id_staff');
    }

    /**
     * Relasi dengan DeliveryOrder (staff yang membuat delivery order)
     */
    public function deliveryOrders(): HasMany
    {
        return $this->hasMany(DeliveryOrder::class, 'id_staff');
    }
}
