<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\SalesOrder;
use App\Models\ClientModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    // ─── List ───────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Invoice::latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('invoice_number', 'like', "%$s%")
                    ->orWhere('client_name', 'like', "%$s%")
                    ->orWhere('client_company', 'like', "%$s%")
                    ->orWhere('so_number', 'like', "%$s%");
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $invoices = $query->paginate(15)->withQueryString();
        return view('admin.invoices.index', compact('invoices'));
    }

    // ─── Create ─────────────────────────────────────────────────────────────
    public function create()
    {
        $invoiceNumber = Invoice::generateInvoiceNumber();
        $salesOrders = SalesOrder::whereIn('status', ['confirmed', 'in_progress', 'completed'])->latest()->get();
        $clients = ClientModel::all();
        return view('admin.invoices.create', compact('invoiceNumber', 'salesOrders', 'clients'));
    }

    // ─── Store ──────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_number' => 'required|string|unique:invoices,invoice_number',
            'sales_order_id' => 'nullable|exists:sales_orders,id',
            'so_number' => 'nullable|string|max:255',
            'nomor_po' => 'nullable|string|max:255',
            'project_name' => 'nullable|string|max:255',
            'date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:date',
            'client_name' => 'nullable|string|max:255',
            'client_company' => 'nullable|string|max:255',
            'client_attention' => 'nullable|string|max:255',
            'client_cc' => 'nullable|string|max:255',
            'client_email' => 'nullable|email|max:255',
            'client_address' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'subtotal' => 'required|numeric|min:0',
            'subtotal_labor' => 'nullable|numeric|min:0',
            'subtotal_other_cost' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'tax_percentage' => 'required|numeric|min:0|max:100',
            'tax_amount' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'status' => 'required|in:draft,sent,paid,overdue,cancelled',
            'notes' => 'nullable|string',
            'term_and_condition' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            // Hitung ulang nilai-nilai untuk memastikan konsistensi (diskon sebelum pajak)
            $calculated = $this->calculateAmounts($validated);
            Invoice::create(array_merge($validated, $calculated));
        });

        return redirect()->route('admin.invoices.index')
            ->with('success', 'Invoice berhasil dibuat.');
    }

    // ─── PDF ────────────────────────────────────────────────────────────────
    public function pdf(Invoice $invoice)
    {
        // Tidak perlu load relasi items/labors/otherCosts karena tidak disimpan

        $logoPath = public_path('assets/gambar/logo-sti.png');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView('admin.invoices.pdf-c', compact('invoice', 'logoBase64'))
            ->setPaper('a4', 'portrait')
            ->setOption('defaultFont', 'DejaVu Sans')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', false);

        $filename = 'Invoice-' . $invoice->invoice_number . '.pdf';
        return $pdf->stream($filename);
    }

    // ─── Show ───────────────────────────────────────────────────────────────
    public function show(Invoice $invoice)
    {
        $invoice->load('salesOrder');
        return view('admin.invoices.show', compact('invoice'));
    }

    // ─── Edit ───────────────────────────────────────────────────────────────
    public function edit(Invoice $invoice)
    {
        // Tidak perlu load relasi items/labors/otherCosts karena tidak disimpan
        $invoiceNumber = $invoice->invoice_number;
        $salesOrders = SalesOrder::whereIn('status', ['confirmed', 'in_progress', 'completed'])->latest()->get();
        $clients = ClientModel::all();
        return view('admin.invoices.edit', compact('invoice', 'invoiceNumber', 'salesOrders', 'clients'));
    }

    // ─── Update ─────────────────────────────────────────────────────────────
    public function update(Request $request, Invoice $invoice)
    {
        // Log request data untuk debugging
        Log::info('=== Invoice Update Request ===');
        Log::info('Invoice ID: ' . $invoice->id);
        Log::info('Request Data:', $request->all());

        try {
            $validated = $request->validate([
            'invoice_number' => 'required|string|unique:invoices,invoice_number,' . $invoice->id,
            'sales_order_id' => 'nullable|exists:sales_orders,id',
            'so_number' => 'nullable|string|max:255',
            'nomor_po' => 'nullable|string|max:255',
            'project_name' => 'nullable|string|max:255',
            'date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:date',
            'client_name' => 'nullable|string|max:255',
            'client_company' => 'nullable|string|max:255',
            'client_attention' => 'nullable|string|max:255',
            'client_cc' => 'nullable|string|max:255',
            'client_email' => 'nullable|email|max:255',
            'client_address' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'subtotal' => 'required|numeric|min:0',
            'subtotal_labor' => 'nullable|numeric|min:0',
            'subtotal_other_cost' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'tax_percentage' => 'required|numeric|min:0|max:100',
            'tax_amount' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'status' => 'required|in:draft,sent,paid,overdue,cancelled',
            'notes' => 'nullable|string',
            'term_and_condition' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, $invoice) {
            // Hitung ulang nilai-nilai untuk memastikan konsistensi (diskon sebelum pajak)
            $calculated = $this->calculateAmounts($validated);
            $invoice->update(array_merge($validated, $calculated));
        });

        Log::info('Invoice berhasil diperbarui, ID: ' . $invoice->id);

        return redirect()->route('admin.invoices.show', $invoice)
            ->with('success', 'Invoice berhasil diperbarui.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('=== Invoice Update Validation Error ===');
            Log::error('Invoice ID: ' . $invoice->id);
            Log::error('Validation Errors:', $e->errors());
            Log::error('Request Data:', request()->all());
            throw $e;
        } catch (\Exception $e) {
            Log::error('=== Invoice Update General Error ===');
            Log::error('Invoice ID: ' . $invoice->id);
            Log::error('Error Message: ' . $e->getMessage());
            Log::error('Error File: ' . $e->getFile() . ':' . $e->getLine());
            throw $e;
        }
    }

    // ─── Delete ─────────────────────────────────────────────────────────────
    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('admin.invoices.index')
            ->with('success', 'Invoice berhasil dihapus.');
    }

    // ─── AJAX: Get SO Data ─────────────────────────────────────────────────
    public function getSoData(SalesOrder $salesOrder)
    {
        $salesOrder->load('items.materials', 'labors', 'otherCosts');

        return response()->json([
            'so_number' => $salesOrder->so_number,
            'nomor_po' => $salesOrder->nomor_po,
            'project_name' => $salesOrder->project_name,
            'client_name' => $salesOrder->client_name,
            'client_company' => $salesOrder->client_company,
            'client_attention' => $salesOrder->client_attention,
            'client_cc' => $salesOrder->client_cc,
            'client_email' => $salesOrder->client_email,
            'client_address' => $salesOrder->client_address,
            'description' => $salesOrder->description_of_work,
            'subtotal' => $salesOrder->subtotal,
            'subtotal_material' => $salesOrder->subtotal_material,
            'subtotal_labor' => $salesOrder->subtotal_labor,
            'subtotal_other_cost' => $salesOrder->subtotal_other_cost,
            'tax_percentage' => $salesOrder->tax_percentage,
            'tax_amount' => $salesOrder->tax_amount,
            'discount' => $salesOrder->discount,
            'total' => $salesOrder->total,
            'items' => $salesOrder->items->map(function ($item) {
                return [
                    'item_name' => $item->material_name,
                    'description' => $item->description,
                    'unit' => $item->unit,
                    'qty' => $item->qty,
                    'unit_price' => $item->unit_price,
                    'subtotal' => $item->subtotal,
                    'materials' => $item->materials->map(function ($mat) {
                        return [
                            'asset_id' => $mat->asset_id,
                            'material_name' => $mat->material_name,
                            'qty_required' => $mat->qty_required,
                            'satuan' => $mat->satuan,
                            'unit_price' => $mat->unit_price,
                            'subtotal' => $mat->subtotal,
                        ];
                    })->toArray(),
                ];
            }),
            'labors' => $salesOrder->labors->map(function ($labor) {
                return [
                    'labor_name' => $labor->labor_name,
                    'mp' => $labor->mp,
                    'days' => $labor->days,
                    'rate' => $labor->rate,
                    'subtotal' => $labor->subtotal,
                ];
            }),
            'other_costs' => $salesOrder->otherCosts->map(function ($cost) {
                return [
                    'cost_name' => $cost->cost_name,
                    'qty' => $cost->qty,
                    'rate' => $cost->rate,
                    'subtotal' => $cost->subtotal,
                ];
            }),
        ]);
    }

    // ─── AJAX: Get Client Data ─────────────────────────────────────────────
    public function getClientData(ClientModel $client)
    {
        return response()->json([
            'id' => $client->id,
            'nama_perusahaan' => $client->nama_perusahaan,
            'nama_kontak' => $client->nama_kontak_perusahaan,
            'email' => $client->email_perusahaan,
            'alamat_pengiriman' => $client->alamat_pengiriman_perusahaan,
            'attn' => $client->attn,
            'cc' => $client->cc,
        ]);
    }

    // ─── Helpers ────────────────────────────────────────────────────────────

    /**
     * Hitung ulang tax_amount dan total dengan rumus: DISKON SEBELUM PAJAK
     * Rumus: (subtotal + subtotal_labor + subtotal_other_cost - discount) * tax%
     */
    private function calculateAmounts(array $data): array
    {
        $subtotal      = floatval($data['subtotal'] ?? 0);
        $subtotalLabor = floatval($data['subtotal_labor'] ?? 0);
        $subtotalOther = floatval($data['subtotal_other_cost'] ?? 0);
        $discount      = floatval($data['discount'] ?? 0);
        $taxPercentage = floatval($data['tax_percentage'] ?? 0);

        $subtotalAll = $subtotal + $subtotalLabor + $subtotalOther;

        // Dasar pengenaan pajak = subtotal - diskon (tidak boleh negatif)
        $taxableBase = max($subtotalAll - $discount, 0);

        // Hitung pajak
        $taxAmount = $taxableBase * ($taxPercentage / 100);

        // Grand total = taxable_base + pajak
        $total = $taxableBase + $taxAmount;

        return [
            'tax_amount' => $taxAmount,
            'total'      => $total,
        ];
    }

}
