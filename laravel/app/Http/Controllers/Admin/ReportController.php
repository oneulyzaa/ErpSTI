<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalesOrder;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Laporan Penjualan — fokus pada nilai project dari Sales Order.
     */
    public function sales(Request $request)
    {
        [$query, $dateFrom, $dateTo] = $this->buildSalesQuery($request);

        $salesOrders = $query->paginate(20)->withQueryString();
        $soIds       = $salesOrders->pluck('id');

        // Data invoice per SO
        $invoices = Invoice::with('receipts')->whereIn('sales_order_id', $soIds)->get()->groupBy('sales_order_id');

        $lists = $this->statusLists();

        return view('admin.reports.sales', array_merge(compact(
            'salesOrders', 'invoices', 'dateFrom', 'dateTo'
        ), $lists));
    }

    // ─── PDF Export ─────────────────────────────────────────────────────────
    public function salesPdf(Request $request)
    {
        [$query, $dateFrom, $dateTo] = $this->buildSalesQuery($request);

        $salesOrders = $query->get();
        $soIds       = $salesOrders->pluck('id');
        $invoices    = Invoice::with('receipts')->whereIn('sales_order_id', $soIds)->get()->groupBy('sales_order_id');

        $grandTotal = $salesOrders->sum('total');

        $logoPath = public_path('assets/gambar/logo-sti.png');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView('admin.reports.sales_pdf', compact(
            'salesOrders', 'invoices',
            'dateFrom', 'dateTo', 'grandTotal', 'logoBase64'
        ))
            ->setPaper('a4', 'landscape')
            ->setOption('defaultFont', 'DejaVu Sans')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', false);

        return $pdf->stream('Laporan-Penjualan.pdf');
    }

    // ─── XLS Export (HTML-table based .xls) ────────────────────────────────
    public function salesExcel(Request $request)
    {
        [$query, $dateFrom, $dateTo] = $this->buildSalesQuery($request);

        $salesOrders = $query->get();
        $soIds       = $salesOrders->pluck('id');
        $invoices    = Invoice::with('receipts')->whereIn('sales_order_id', $soIds)->get()->groupBy('sales_order_id');

        $grandTotal = $salesOrders->sum('total');

        $html = $this->renderExcelHtml(
            $salesOrders, $invoices,
            $dateFrom, $dateTo, $grandTotal
        );

        return response($html)
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', 'attachment; filename="Laporan-Penjualan.xls"')
            ->header('Cache-Control', 'max-age=0');
    }

    // ─── Private: Shared query builder ────────────────────────────────────
    private function buildSalesQuery(Request $request): array
    {
        $query = SalesOrder::with(['client'])->latest('date');

        $dateFrom = $request->input('date_from');
        $dateTo   = $request->input('date_to');

        if ($dateFrom) $query->whereDate('date', '>=', $dateFrom);
        if ($dateTo)   $query->whereDate('date', '<=', $dateTo);

        if ($request->filled('invoice_status')) {
            $query->whereHas('invoices', fn($q) => $q->where('status', $request->invoice_status));
        }
        if ($request->filled('payment_status')) {
            $query->whereHas('invoices', function ($q) use ($request) {
                $ps = $request->payment_status;
                if ($ps === 'paid') {
                    $q->whereHas('receipts', fn($r) => $r->where('status', 'confirmed'))
                      ->whereRaw('(SELECT COALESCE(SUM(amount),0) FROM receipts WHERE invoice_id = invoices.id AND status = "confirmed") >= total');
                } elseif ($ps === 'unpaid') {
                    $q->where(fn($sub) => $sub
                        ->whereDoesntHave('receipts', fn($r) => $r->where('status', 'confirmed'))
                        ->orWhereRaw('(SELECT COALESCE(SUM(amount),0) FROM receipts WHERE invoice_id = invoices.id AND status = "confirmed") < total')
                    );
                } elseif ($ps === 'partial') {
                    $q->whereHas('receipts', fn($r) => $r->where('status', 'confirmed'))
                      ->whereRaw('(SELECT COALESCE(SUM(amount),0) FROM receipts WHERE invoice_id = invoices.id AND status = "confirmed") > 0')
                      ->whereRaw('(SELECT COALESCE(SUM(amount),0) FROM receipts WHERE invoice_id = invoices.id AND status = "confirmed") < total');
                }
            });
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('so_number', 'like', "%$s%")
                  ->orWhere('project_name', 'like', "%$s%")
                  ->orWhere('client_company', 'like', "%$s%")
                  ->orWhere('client_name', 'like', "%$s%")
                  ->orWhere('nomor_po', 'like', "%$s%");
            });
        }

        return [$query, $dateFrom, $dateTo];
    }

    // ─── Private: Status list definitions ─────────────────────────────────
    private function statusLists(): array
    {
        return [
            'invoiceStatuses' => [
                'draft' => 'Draft', 'sent' => 'Sent', 'paid' => 'Paid',
                'overdue' => 'Overdue', 'cancelled' => 'Cancelled',
            ],
            'paymentStatuses' => [
                'unpaid'  => 'Belum Dibayar',
                'partial' => 'Sebagian',
                'paid'    => 'Lunas',
            ],
        ];
    }

    // ─── Private: Render Excel-compatible HTML ────────────────────────────
    private function renderExcelHtml($salesOrders, $invoices, $dateFrom, $dateTo, $grandTotal): string
    {
        $rows = '';
        $no = 0;
        foreach ($salesOrders as $so) {
            $no++;

            $invColl = $invoices->get($so->id, collect());
            $inv     = $invColl->first();
            $invStatus = $inv ? $inv->status : '-';

            $rows .= '<tr>';
            $rows .= '<td>' . $no . '</td>';
            $rows .= '<td>' . $so->so_number . '</td>';
            $rows .= '<td>' . $so->client_company . '</td>';
            $rows .= '<td>' . ($so->nomor_po ?: '-') . '</td>';
            $rows .= '<td>' . ($so->project_name ?: '-') . '</td>';
            $rows .= '<td>' . $so->date->format('d/m/Y') . '</td>';
            $rows .= '<td>' . ($invStatus !== '-' ? $invStatus : '-') . '</td>';
            $rows .= '<td style="text-align:right;">' . number_format($so->total, 0, ',', '.') . '</td>';
            $rows .= '</tr>' . "\n";
        }

        $rangeLabel = '';
        if ($dateFrom && $dateTo) {
            $rangeLabel = 'Periode: ' . \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') . ' - ' . \Carbon\Carbon::parse($dateTo)->format('d/m/Y');
        } elseif ($dateFrom) {
            $rangeLabel = 'Dari: ' . \Carbon\Carbon::parse($dateFrom)->format('d/m/Y');
        } elseif ($dateTo) {
            $rangeLabel = 'Sampai: ' . \Carbon\Carbon::parse($dateTo)->format('d/m/Y');
        }

        return '<html xmlns:o="urn:schemas-microsoft-com:office:office"
                      xmlns:x="urn:schemas-microsoft-com:office:excel"
                      xmlns="http://www.w3.org/TR/REC-html40">
        <head><meta charset="UTF-8">
        <style>
            table { border-collapse: collapse; font-size: 12px; }
            th, td { border: 1px solid #999; padding: 4px 6px; }
            th { background: #1e3a5f; color: #fff; font-weight: bold; text-align: center; }
            .title { font-size: 16px; font-weight: bold; text-align: center; }
            .subtitle { font-size: 12px; text-align: center; margin-bottom: 10px; }
            .grand-total td { font-weight: bold; background: #e8f0fe; }
        </style>
        </head><body>
        <table>
            <tr><td colspan="8" class="title">LAPORAN PENJUALAN</td></tr>
            <tr><td colspan="8" class="subtitle">PT. Sistem Teknologi Integrator</td></tr>
            <tr><td colspan="8" class="subtitle">' . $rangeLabel . '</td></tr>
            <tr></tr>
            <tr>
                <th>No</th>
                <th>Nomor Sales Order</th>
                <th>Nama Klien</th>
                <th>Nomor PO</th>
                <th>Nama Project</th>
                <th>Tanggal SO</th>
                <th>Status Invoice</th>
                <th>Nilai Project</th>
            </tr>
            ' . $rows . '
            <tr class="grand-total">
                <td colspan="7" style="text-align:right;">GRAND TOTAL</td>
                <td style="text-align:right;">' . number_format($grandTotal, 0, ',', '.') . '</td>
            </tr>
        </table>
        </body></html>';
    }
}