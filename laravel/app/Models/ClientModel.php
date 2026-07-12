<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientModel extends Model
{
    protected $table = 'customers';
    protected $primaryKey = 'id_customer';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_customer',
        'nama_perusahaan',
        'nama_kontak',
        'email_perusahaan',
        'alamat_perusahaan',
        'alamat_faktur',
        'alamat_efaktur',
        'telepon_faktur',
        'telepon_efaktur',
        'rekening_perusahaan',
        'npwp_perusahaan',
    ];

    /**
     * Relasi dengan Quotation
     */
    public function quotations()
    {
        return $this->hasMany(Quotation::class, 'id_client', 'id_customer');
    }

    /**
     * Relasi dengan SalesOrder
     */
    public function salesOrders()
    {
        return $this->hasMany(SalesOrder::class, 'id_client', 'id_customer');
    }

    /**
     * Relasi dengan DeliveryOrder
     */
    public function deliveryOrders()
    {
        return $this->hasMany(DeliveryOrder::class, 'id_client', 'id_customer');
    }

    /**
     * Relasi dengan Invoice
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'id_client', 'id_customer');
    }
}