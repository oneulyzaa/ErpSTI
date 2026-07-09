<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientModel extends Model
{
    protected $table = 'clients';
    
    protected $fillable = [
        'id_perusahaan',
        'nama_perusahaan',
        'email_perusahaan',
        'nama_kontak_perusahaan',
        'npwp_perusahaan',
        'alamat_pengiriman_perusahaan',
        'nomor_telepon_pengiriman',
        'alamat_faktur_perusahaan',
        'nomor_telepon_faktur',
        'alamat_efaktur_perusahaan',
        'nomor_rekening_perusahaan',
        'created_by',
    ];

    /**
     * Relasi dengan Quotation
     */
    public function quotations()
    {
        return $this->hasMany(Quotation::class, 'client_id');
    }

    /**
     * Relasi dengan SalesOrder
     */
    public function salesOrders()
    {
        return $this->hasMany(SalesOrder::class, 'client_id');
    }

    /**
     * Relasi dengan DeliveryOrder
     */
    public function deliveryOrders()
    {
        return $this->hasMany(DeliveryOrder::class, 'client_id');
    }

    /**
     * Relasi dengan Invoice
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'client_id');
    }
}
