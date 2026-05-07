<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman dashboard dengan ringkasan data.
     */
    public function index(Request $request)
    {
        /*
        |----------------------------------------------------------------------
        | Kumpulkan data ringkasan untuk dashboard.
        | Ganti query di bawah dengan model yang sesuai setelah model dibuat.
        |----------------------------------------------------------------------
        */

        // Contoh data statis — ganti dengan query Eloquent sesuai model:
        // $totalPenjualan  = \App\Models\Sale::whereMonth('created_at', now()->month)->sum('grand_total');
        // $totalKlien      = \App\Models\Client::count();
        // $penawaranPending = \App\Models\Quotation::where('status', 'pending')->count();
        // $invoiceBelumLunas = \App\Models\Invoice::where('status', 'unpaid')->count();
        // $penjualanTerbaru = \App\Models\Sale::with('client')->latest()->take(5)->get();

        $stats = [
            'total_penjualan' => 'Rp 48,5 Jt',   // ganti: Sale::sum('grand_total')
            'total_klien' => 128,              // ganti: Client::count()
            'penawaran_pending' => 24,               // ganti: Quotation::where('status','pending')->count()
            'invoice_belum_lunas' => 15,               // ganti: Invoice::where('status','unpaid')->count()
        ];

        return view('admin.dashboard.index', compact('stats'));
    }
}
