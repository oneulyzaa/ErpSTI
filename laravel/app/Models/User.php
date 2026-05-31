<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',         // ← tambahan
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // =========================================================================
    //  ROLE HELPERS
    // =========================================================================

    /** Cek apakah user adalah Admin / Staf Penjualan */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /** Cek apakah user adalah Finance / Accounting */
    public function isFinance(): bool
    {
        return $this->role === 'finance';
    }

    /** Cek apakah user adalah Gudang / Produksi */
    public function isGudang(): bool
    {
        return $this->role === 'gudang';
    }

    /** Cek apakah user adalah Direktur */
    public function isDirektur(): bool
    {
        return $this->role === 'direktur';
    }

    /**
     * Cek apakah user punya akses ke modul tertentu.
     *
     * Contoh pemakaian di Blade:
     *   @if(auth()->user()->hasAccess('invoice'))
     *
     * Contoh pemakaian di Controller:
     *   if (! auth()->user()->hasAccess('laporan-penjualan')) abort(403);
     */
    public function hasAccess(string $module): bool
    {
        $permissions = [
            'admin' => [
                'dashboard',
                'quotation',
                'sales-order',
                'delivery-order',
                'invoice',          // draft saja
                'master-clients',
                'master-assets',
                'laporan-penjualan',
            ],
            'finance' => [
                'dashboard',
                'invoice',          // full akses
                'tanda-terima',
                'master-clients',   // view only
                'laporan-penjualan',
                'laporan-hpp',
            ],
            'gudang' => [
                'dashboard',
                'produksi',
                'delivery-order',   // konfirmasi saja
                'sales-order',      // view only
                'master-assets',    // update stok
            ],
            'direktur' => [
                'dashboard',
                'quotation',
                'sales-order',
                'delivery-order',
                'produksi',
                'invoice',
                'tanda-terima',
                'master-clients',
                'master-assets',
                'laporan-penjualan',
                'laporan-hpp',
            ],
        ];

        return in_array($module, $permissions[$this->role] ?? []);
    }
}