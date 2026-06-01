<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClientModel;
use App\Models\Invoice;
use App\Models\Quotation;
use App\Models\SalesOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman dashboard dengan ringkasan data.
     */
    public function index(Request $request)
    {
        // Total penjualan bulan ini dari SalesOrder
        $totalPenjualan = SalesOrder::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');

        // Total klien aktif
        $totalKlien = ClientModel::count();

        // Penawaran pending
        $penawaranPending = Quotation::where('status', 'pending')->count();

        // Invoice belum lunas
        $invoiceBelumLunas = Invoice::whereNotIn('status', ['paid', 'lunas'])->count();
        $invoiceOutstanding = Invoice::whereNotIn('status', ['paid', 'lunas'])->sum('total');

        // Penjualan terbaru (Sales Orders)
        $penjualanTerbaru = SalesOrder::with('client')
            ->latest()
            ->take(5)
            ->get();

        // Data grafik penjualan 6 bulan terakhir
        $salesChartData = $this->getSalesChartData();

        // Hitung persentase perubahan penjualan dari bulan sebelumnya
        $penjualanBulanLalu = SalesOrder::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('total');

        $persentasePerubahan = 0;
        if ($penjualanBulanLalu > 0) {
            $persentasePerubahan = (($totalPenjualan - $penjualanBulanLalu) / $penjualanBulanLalu) * 100;
        }

        // Hitung klien baru bulan ini
        $klienBaru = ClientModel::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $stats = [
            'total_penjualan' => $totalPenjualan,
            'total_penjualan_formatted' => $this->formatCurrency($totalPenjualan),
            'persentase_perubahan' => round($persentasePerubahan, 1),
            'total_klien' => $totalKlien,
            'klien_baru' => $klienBaru,
            'penawaran_pending' => $penawaranPending,
            'invoice_belum_lunas' => $invoiceBelumLunas,
            'invoice_outstanding' => $invoiceOutstanding,
            'invoice_outstanding_formatted' => $this->formatCurrency($invoiceOutstanding),
        ];

        return view('admin.dashboard.index', compact('stats', 'penjualanTerbaru', 'salesChartData'));
    }

    /**
     * Get sales data for the last 6 months for chart.
     */
    private function getSalesChartData(): array
    {
        $labels = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $labels[] = $date->translatedFormat('M');

            $total = SalesOrder::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->sum('total');

            $data[] = $total;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    /**
     * Format currency for display.
     */
    private function formatCurrency($amount): string
    {
        if ($amount >= 1000000000) {
            return 'Rp ' . number_format($amount / 1000000000, 1) . ' M';
        }

        if ($amount >= 1000000) {
            return 'Rp ' . number_format($amount / 1000000, 1) . ' Jt';
        }

        if ($amount >= 1000) {
            return 'Rp ' . number_format($amount / 1000, 1) . ' Rb';
        }

        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}
