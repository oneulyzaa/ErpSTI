<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoiceLabor;
use App\Models\InvoiceOtherCost;
use App\Models\SalesOrder;
use App\Models\ClientModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            'items' => 'nullable|array',
            'items.*.item_name' => 'required_with:items|string|max:255',
            'items.*.description' => 'nullable|string|max:500',
            'items.*.unit' => 'required_with:items|string|max:50',
            'items.*.qty' => 'required_with:items|numeric|min:0',
            'items.*.unit_price' => 'required_with:items|numeric|min:0',
            'items.*.subtotal' => 'nullable|numeric|min:0',
            'labors' => 'nullable|array',
            'labors.*.labor_name' => 'required_with:labors|string|max:255',
            'labors.*.mp' => 'required_with:labors|integer|min:1',
            'labors.*.days' => 'required_with:labors|numeric|min:0',
            'labors.*.rate' => 'required_with:labors|numeric|min:0',
            'labors.*.subtotal' => 'nullable|numeric|min:0',
            'other_costs' => 'nullable|array',
            'other_costs.*.cost_name' => 'required_with:other_costs|string|max:255',
            'other_costs.*.qty' => 'required_with:other_costs|numeric|min:0',
            'other_costs.*.rate' => 'required_with:other_costs|numeric|min:0',
            'other_costs.*.subtotal' => 'nullable|numeric|min:0',
            'items.*.materials' => 'nullable|array',
            'items.*.materials.*.material_name' => 'required_with:items.*.materials|string|max:255',
            'items.*.materials.*.satuan' => 'nullable|string|max:50',
            'items.*.materials.*.qty_required' => 'required_with:items.*.materials|numeric|min:0',
            'items.*.materials.*.unit_price' => 'required_with:items.*.materials|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $invoice = Invoice::create($validated);
            $this->syncItems($invoice, $request->items ?? []);
            $this->syncLabors($invoice, $request->labors ?? []);
            $this->syncOtherCosts($invoice, $request->other_costs ?? []);
        });

        return redirect()->route('admin.invoices.index')
            ->with('success', 'Invoice berhasil dibuat.');
    }

    // ─── PDF ────────────────────────────────────────────────────────────────
    public function pdf(Invoice $invoice)
    {
        $invoice->load('items.materials', 'labors', 'otherCosts');

        $logoPath = public_path('assets/gambar/logo-sti.png');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView('admin.invoices.pdf-b', compact('invoice', 'logoBase64'))
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
        $invoice->load('items.materials', 'labors', 'otherCosts', 'salesOrder');
        return view('admin.invoices.show', compact('invoice'));
    }

    // ─── Edit ───────────────────────────────────────────────────────────────
    public function edit(Invoice $invoice)
    {
        $invoice->load('items.materials', 'labors', 'otherCosts');
        $invoiceNumber = $invoice->invoice_number;
        $salesOrders = SalesOrder::whereIn('status', ['confirmed', 'in_progress', 'completed'])->latest()->get();
        $clients = ClientModel::all();
        return view('admin.invoices.edit', compact('invoice', 'invoiceNumber', 'salesOrders', 'clients'));
    }

    // ─── Update ─────────────────────────────────────────────────────────────
    public function update(Request $request, Invoice $invoice)
    {
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
            'items' => 'nullable|array',
            'items.*.item_name' => 'required_with:items|string|max:255',
            'items.*.description' => 'nullable|string|max:500',
            'items.*.unit' => 'required_with:items|string|max:50',
            'items.*.qty' => 'required_with:items|numeric|min:0',
            'items.*.unit_price' => 'required_with:items|numeric|min:0',
            'items.*.subtotal' => 'nullable|numeric|min:0',
            'labors' => 'nullable|array',
            'labors.*.labor_name' => 'required_with:labors|string|max:255',
            'labors.*.mp' => 'required_with:labors|integer|min:1',
            'labors.*.days' => 'required_with:labors|numeric|min:0',
            'labors.*.rate' => 'required_with:labors|numeric|min:0',
            'labors.*.subtotal' => 'nullable|numeric|min:0',
            'other_costs' => 'nullable|array',
            'other_costs.*.cost_name' => 'required_with:other_costs|string|max:255',
            'other_costs.*.qty' => 'required_with:other_costs|numeric|min:0',
            'other_costs.*.rate' => 'required_with:other_costs|numeric|min:0',
            'other_costs.*.subtotal' => 'nullable|numeric|min:0',
            'items.*.materials' => 'nullable|array',
            'items.*.materials.*.material_name' => 'required_with:items.*.materials|string|max:255',
            'items.*.materials.*.satuan' => 'nullable|string|max:50',
            'items.*.materials.*.qty_required' => 'required_with:items.*.materials|numeric|min:0',
            'items.*.materials.*.unit_price' => 'required_with:items.*.materials|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated, $request, $invoice) {
            $invoice->update($validated);
            $this->syncItems($invoice, $request->items ?? []);
            $this->syncLabors($invoice, $request->labors ?? []);
            $this->syncOtherCosts($invoice, $request->other_costs ?? []);
        });

        return redirect()->route('admin.invoices.show', $invoice)
            ->with('success', 'Invoice berhasil diperbarui.');
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
    private function syncItems(Invoice $invoice, array $items): void
    {
        $invoice->items()->delete();
        foreach ($items as $i => $item) {
            if (empty($item['item_name']))
                continue;
            $invoiceItem = InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'sort_order' => $i + 1,
                'item_name' => $item['item_name'],
                'description' => $item['description'] ?? null,
                'unit' => $item['unit'] ?? 'Unit',
                'qty' => $item['qty'] ?? 0,
                'unit_price' => $item['unit_price'] ?? 0,
                'subtotal' => $item['subtotal'] ?? (($item['qty'] ?? 0) * ($item['unit_price'] ?? 0)),
            ]);

            if (!empty($item['materials'])) {
                foreach ($item['materials'] as $m => $mat) {
                    if (empty($mat['material_name']))
                        continue;
                    \App\Models\InvoiceItemMaterial::create([
                        'invoice_item_id' => $invoiceItem->id,
                        'asset_id' => $mat['asset_id'] ?? null,
                        'material_name' => $mat['material_name'],
                        'qty_required' => $mat['qty_required'] ?? 0,
                        'satuan' => $mat['satuan'] ?? 'pcs',
                        'unit_price' => $mat['unit_price'] ?? 0,
                        'subtotal' => ($mat['qty_required'] ?? 0) * ($mat['unit_price'] ?? 0),
                        'sort_order' => $m + 1,
                    ]);
                }
            }
        }
    }

    private function syncLabors(Invoice $invoice, array $labors): void
    {
        $invoice->labors()->delete();
        foreach ($labors as $i => $labor) {
            if (empty($labor['labor_name']))
                continue;
            InvoiceLabor::create([
                'invoice_id' => $invoice->id,
                'sort_order' => $i + 1,
                'labor_name' => $labor['labor_name'],
                'mp' => $labor['mp'] ?? 1,
                'days' => $labor['days'] ?? 0,
                'rate' => $labor['rate'] ?? 0,
                'subtotal' => $labor['subtotal'] ?? (($labor['mp'] ?? 1) * ($labor['days'] ?? 0) * ($labor['rate'] ?? 0)),
            ]);
        }
    }

    private function syncOtherCosts(Invoice $invoice, array $otherCosts): void
    {
        $invoice->otherCosts()->delete();
        foreach ($otherCosts as $i => $cost) {
            if (empty($cost['cost_name']))
                continue;
            InvoiceOtherCost::create([
                'invoice_id' => $invoice->id,
                'sort_order' => $i + 1,
                'cost_name' => $cost['cost_name'],
                'qty' => $cost['qty'] ?? 1,
                'rate' => $cost['rate'] ?? 0,
                'subtotal' => $cost['subtotal'] ?? (($cost['qty'] ?? 1) * ($cost['rate'] ?? 0)),
            ]);
        }
    }
}
