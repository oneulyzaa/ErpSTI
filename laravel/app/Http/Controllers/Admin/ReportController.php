<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalesOrder;
use App\Models\Quotation;
use App\Models\Production;
use App\Models\DeliveryOrder;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Laporan Penjualan — aggregated view across Quotation → SO → Production → DO → Invoice → Receipt.
     */
    public function sales(Request $request)
    {
        [$query, $dateFrom, $dateTo] = $this->buildSalesQuery($request);

        $salesOrders = $query->paginate(20)->withQueryString();
        $soIds       = $salesOrders->pluck('id');

        // Data pendukung per SO
        $productions    = Production::whereIn('sales_order_id', $soIds)->get()->groupBy('sales_order_id');
        $deliveryOrders = DeliveryOrder::whereIn('sales_order_id', $soIds)->get()->groupBy('sales_order_id');
        $invoices       = Invoice::with('receipts')->whereIn('sales_order_id', $soIds)->get()->groupBy('sales_order_id');

        $lists = $this->statusLists();

        return view('admin.reports.sales', array_merge(compact(
            'salesOrders', 'productions', 'deliveryOrders', 'invoices', 'dateFrom', 'dateTo'
        ), $lists));
    }

    // ─── PDF Export ─────────────────────────────────────────────────────────
    public function salesPdf(Request $request)
    {
        [$query, $dateFrom, $dateTo] = $this->buildSalesQuery($request);

        $salesOrders    = $query->get();
        $soIds          = $salesOrders->pluck('id');
        $productions    = Production::whereIn('sales_order_id', $soIds)->get()->groupBy('sales_order_id');
        $deliveryOrders = DeliveryOrder::whereIn('sales_order_id', $soIds)->get()->groupBy('sales_order_id');
        $invoices       = Invoice::with('receipts')->whereIn('sales_order_id', $soIds)->get()->groupBy('sales_order_id');

        $grandTotal = $salesOrders->sum('total');

        $logoPath = public_path('assets/gambar/logo-sti.png');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView('admin.reports.sales_pdf', compact(
            'salesOrders', 'productions', 'deliveryOrders', 'invoices',
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

        $salesOrders    = $query->get();
        $soIds          = $salesOrders->pluck('id');
        $productions    = Production::whereIn('sales_order_id', $soIds)->get()->groupBy('sales_order_id');
        $deliveryOrders = DeliveryOrder::whereIn('sales_order_id', $soIds)->get()->groupBy('sales_order_id');
        $invoices       = Invoice::with('receipts')->whereIn('sales_order_id', $soIds)->get()->groupBy('sales_order_id');

        $grandTotal = $salesOrders->sum('total');

        $html = $this->renderExcelHtml(
            $salesOrders, $productions, $deliveryOrders, $invoices,
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
        $query = SalesOrder::with(['quotation', 'items', 'labors', 'client'])->latest('date');

        $dateFrom = $request->input('date_from');
        $dateTo   = $request->input('date_to');

        if ($dateFrom) $query->whereDate('date', '>=', $dateFrom);
        if ($dateTo)   $query->whereDate('date', '<=', $dateTo);

        if ($request->filled('quotation_status')) {
            $query->whereHas('quotation', fn($q) => $q->where('status', $request->quotation_status));
        }
        if ($request->filled('so_status')) {
            $query->where('status', $request->so_status);
        }
        if ($request->filled('production_status')) {
            $query->whereHas('productions', fn($q) => $q->where('status', $request->production_status));
        }
        if ($request->filled('do_status')) {
            $query->whereHas('deliveryOrders', fn($q) => $q->where('status', $request->do_status));
        }
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
                  ->orWhere('client_name', 'like', "%$s%");
            });
        }

        return [$query, $dateFrom, $dateTo];
    }

    // ─── Private: Status list definitions ─────────────────────────────────
    private function statusLists(): array
    {
        return [
            'quotationStatuses' => [
                'draft'    => 'Draft', 'sent' => 'Sent', 'approved' => 'Approved',
                'rejected' => 'Rejected', 'expired' => 'Expired',
            ],
            'soStatuses' => [
                'draft' => 'Draft', 'confirmed' => 'Confirmed', 'in_progress' => 'In Progress',
                'completed' => 'Completed', 'cancelled' => 'Cancelled',
            ],
            'productionStatuses' => [
                'planned' => 'Planned', 'in_progress' => 'In Progress',
                'completed' => 'Completed', 'cancelled' => 'Cancelled',
            ],
            'doStatuses' => [
                'draft' => 'Draft', 'confirmed' => 'Confirmed', 'shipped' => 'Shipped',
                'delivered' => 'Delivered', 'cancelled' => 'Cancelled',
            ],
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
    private function renderExcelHtml($salesOrders, $productions, $deliveryOrders, $invoices, $dateFrom, $dateTo, $grandTotal): string
    {
        $rows = '';
        $no = 0;
        foreach ($salesOrders as $so) {
            $no++;

            $quo = $so->quotation;
            $quoStatus = $quo ? $quo->status : '-';

            $prodColl = $productions->get($so->id, collect());
            $prod     = $prodColl->first();
            $prodStatus = $prod ? $prod->status : '-';

            $doColl = $deliveryOrders->get($so->id, collect());
            $do     = $doColl->first();
            $doStatus = $do ? $do->status : '-';

            $invColl = $invoices->get($so->id, collect());
            $inv     = $invColl->first();
            $invStatus = $inv ? $inv->status : '-';

            $totalPaid = 0;
            $totalInv  = 0;
            if ($inv) {
                $totalInv  = $inv->total;
                $totalPaid = $inv->receipts->where('status', 'confirmed')->sum('amount');
            }
            $payStatus = $totalInv > 0 && $totalPaid >= $totalInv ? 'Lunas'
                       : ($totalPaid > 0 ? 'Sebagian' : 'Belum Dibayar');

            $rows .= '<tr>';
            $rows .= '<td>' . $no . '</td>';
            $rows .= '<td>' . $so->so_number . '</td>';
            $rows .= '<td>' . ($so->project_name ?: '-') . '</td>';
            $rows .= '<td>' . $so->client_company . '</td>';
            $rows .= '<td>' . ($so->client_name ?: '-') . '</td>';
            $rows .= '<td>' . $so->date->format('d/m/Y') . '</td>';
            $rows .= '<td>' . $quoStatus . '</td>';
            $rows .= '<td>' . str_replace('_', ' ', $so->status) . '</td>';
            $rows .= '<td>' . ($prodStatus !== '-' ? str_replace('_', ' ', $prodStatus) : '-') . '</td>';
            $rows .= '<td>' . ($doStatus !== '-' ? str_replace('_', ' ', $doStatus) : '-') . '</td>';
            $rows .= '<td>' . ($invStatus !== '-' ? $invStatus : '-') . '</td>';
            $rows .= '<td>' . $payStatus . '</td>';
            $rows .= '<td style="text-align:right;">' . number_format($totalPaid, 0, ',', '.') . '</td>';
            $rows .= '<td style="text-align:right;">' . number_format($totalInv, 0, ',', '.') . '</td>';
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
            <tr><td colspan="15" class="title">LAPORAN PENJUALAN</td></tr>
            <tr><td colspan="15" class="subtitle">PT. Sistem Teknologi Integrator</td></tr>
            <tr><td colspan="15" class="subtitle">' . $rangeLabel . '</td></tr>
            <tr></tr>
            <tr>
                <th>No</th>
                <th>No. SO</th>
                <th>Proyek</th>
                <th>Perusahaan</th>
                <th>Kontak</th>
                <th>Tanggal</th>
                <th>Quotation</th>
                <th>SO</th>
                <th>Produksi</th>
                <th>DO</th>
                <th>Invoice</th>
                <th>Pembayaran</th>
                <th>Terbayar</th>
                <th>Tagihan</th>
                <th>Nilai SO</th>
            </tr>
            ' . $rows . '
            <tr class="grand-total">
                <td colspan="14" style="text-align:right;">GRAND TOTAL</td>
                <td style="text-align:right;">' . number_format($grandTotal, 0, ',', '.') . '</td>
            </tr>
        </table>
        </body></html>';
    }
}
