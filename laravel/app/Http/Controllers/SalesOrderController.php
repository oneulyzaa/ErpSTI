<?php

namespace App\Http\Controllers;

use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\SalesOrderLabor;
use App\Models\SalesOrderOtherCost;
use App\Models\Quotation;
use App\Models\ClientModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class SalesOrderController extends Controller
{
    // ─── Default labor list ───────────────────────────────────────────────────
    private array $defaultLabors = [
        ['labor_name' => 'Mechanical Design', 'mp' =>  1, 'days' => 1, 'rate' => 1500000],
        ['labor_name' => 'Electrical Design', 'mp' => 1, 'days' => 1, 'rate' => 1500000],
        ['labor_name' => 'Assembling', 'mp' => 2, 'days' => 1, 'rate' => 1000000],
        ['labor_name' => 'Wiring', 'mp' => 2, 'days' => 1, 'rate' => 1000000],
        ['labor_name' => 'Commissioning', 'mp' => 3, 'days' => 1, 'rate' => 1000000],
        ['labor_name' => 'Programming', 'mp' => 1, 'days' => 1, 'rate' => 1000000],
        ['labor_name' => 'Setting & Trainhouse', 'mp' => 2, 'days' => 1, 'rate' => 1000000],
        ['labor_name' => 'Installation', 'mp' => 4, 'days' => 1, 'rate' => 1500000],
        ['labor_name' => 'Setting & Trainonsite', 'mp' => 2, 'days' => 1, 'rate' => 1000000],
        ['labor_name' => 'Accomodation', 'mp' => 1, 'days' => 1, 'rate' => 0],
    ];

    // ─── List ────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = SalesOrder::with('items', 'labors')->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('so_number', 'like', "%$s%")
                    ->orWhere('client_name', 'like', "%$s%")
                    ->orWhere('client_company', 'like', "%$s%")
                    ->orWhere('project_name', 'like', "%$s%");
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $salesOrders = $query->paginate(15)->withQueryString();
        return view('admin.sales-orders.index', compact('salesOrders'));
    }

    // ─── Create ───────────────────────────────────────────────────────────────
    public function create()
    {
        $soNumber = SalesOrder::generateSONumber();
        $defaultLabors = $this->defaultLabors;
        $quotations = Quotation::whereIn('status', ['approved', 'sent'])->latest()->get();
        $clients = ClientModel::all();
        return view('admin.sales-orders.create', compact('soNumber', 'defaultLabors', 'quotations', 'clients'));
    }

    // ─── Store ────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $this->validateSalesOrder($request);
        $validated = $this->resolveClientData($validated);

        DB::transaction(function () use ($validated, $request) {
            $discount = (float) ($validated['discount'] ?? 0);
            [$subMat, $subLab, $subOth, $subtotal, $taxAmount, $total] = $this->calculateTotals(
                $request->items ?? [],
                $request->labors ?? [],
                $request->other_costs ?? [],
                $validated['tax_percentage'],
                $discount
            );

            $salesOrder = SalesOrder::create(array_merge($validated, [
                'discount' => $discount,
                'subtotal_material' => $subMat,
                'subtotal_labor' => $subLab,
                'subtotal_other_cost' => $subOth,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total' => $total,
            ]));

            $this->syncItems($salesOrder, $request->items ?? []);
            $this->syncLabors($salesOrder, $request->labors ?? []);
            $this->syncOtherCosts($salesOrder, $request->other_costs ?? []);
        });

        return redirect()->route('admin.sales-orders.index')
            ->with('success', 'Sales Order berhasil dibuat.');
    }

    // ─── Show ─────────────────────────────────────────────────────────────────
    public function show(SalesOrder $salesOrder)
    {
        $salesOrder->load('items', 'labors', 'otherCosts', 'quotation');
        return view('admin.sales-orders.show', compact('salesOrder'));
    }

    // ─── Edit ─────────────────────────────────────────────────────────────────
    public function edit(SalesOrder $salesOrder)
    {
        $salesOrder->load('items', 'labors', 'otherCosts');
        $soNumber = $salesOrder->so_number;
        $defaultLabors = $this->defaultLabors;
        $quotations = Quotation::whereIn('status', ['approved', 'sent'])->latest()->get();
        $clients = ClientModel::all();
        return view('admin.sales-orders.edit', compact('salesOrder', 'soNumber', 'defaultLabors', 'quotations', 'clients'));
    }

    // ─── Update ───────────────────────────────────────────────────────────────
    public function update(Request $request, SalesOrder $salesOrder)
    {
        $validated = $this->validateSalesOrder($request, $salesOrder->id);
        $validated = $this->resolveClientData($validated);

        DB::transaction(function () use ($validated, $request, $salesOrder) {
            $discount = (float) ($validated['discount'] ?? 0);
            [$subMat, $subLab, $subOth, $subtotal, $taxAmount, $total] = $this->calculateTotals(
                $request->items ?? [],
                $request->labors ?? [],
                $request->other_costs ?? [],
                $validated['tax_percentage'],
                $discount
            );

            $salesOrder->update(array_merge($validated, [
                'discount' => $discount,
                'subtotal_material' => $subMat,
                'subtotal_labor' => $subLab,
                'subtotal_other_cost' => $subOth,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total' => $total,
            ]));

            $this->syncItems($salesOrder, $request->items ?? []);
            $this->syncLabors($salesOrder, $request->labors ?? []);
            $this->syncOtherCosts($salesOrder, $request->other_costs ?? []);
        });

        return redirect()->route('admin.sales-orders.show', $salesOrder)
            ->with('success', 'Sales Order berhasil diperbarui.');
    }

    // ─── Delete ───────────────────────────────────────────────────────────────
    public function destroy(SalesOrder $salesOrder)
    {
        $salesOrder->delete();
        return redirect()->route('admin.sales-orders.index')
            ->with('success', 'Sales Order berhasil dihapus.');
    }

    // ─── PDF ──────────────────────────────────────────────────────────────────
    public function pdf(SalesOrder $salesOrder)
    {
        $salesOrder->load('items', 'labors', 'otherCosts');

        $logoPath = public_path('assets/gambar/logo-sti.png');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView('admin.sales-orders.pdf', compact('salesOrder', 'logoBase64'))
            ->setPaper('a4', 'portrait')
            ->setOption('defaultFont', 'DejaVu Sans')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', false);

        $filename = 'SalesOrder-' . $salesOrder->so_number . '.pdf';
        return $pdf->stream($filename);
    }

    // ─── Copy from Quotation ────────────────────────────────────────────────
    public function copyFromQuotation(Quotation $quotation)
    {
        $quotation->load('items.materials', 'labors', 'otherCosts');
        $soNumber = SalesOrder::generateSONumber();
        $defaultLabors = $this->defaultLabors;
        $quotations = Quotation::whereIn('status', ['approved', 'sent'])->latest()->get();
        $clients = ClientModel::all();

        return view('admin.sales-orders.create', compact(
            'soNumber',
            'defaultLabors',
            'quotations',
            'quotation',
            'clients'
        ));
    }

    // ─── AJAX: Get Quotation Data (items + labors + client info) ────────
    public function getQuotationData(Quotation $quotation)
    {
        $quotation->load(['items.materials', 'labors', 'otherCosts', 'client']);

        $client = $quotation->client;

        return response()->json([
            'client_id' => $client?->id ?? $quotation->client_id,
            'customer_id' => $client?->id_perusahaan ?? $quotation->customer_id,
            'project_name' => $quotation->project_name,
            'quote_number' => $quotation->quote_number,
            'client_name' => $client?->nama_kontak_perusahaan ?? $quotation->client_name,
            'client_company' => $client?->nama_perusahaan ?? $quotation->client_company,
            'client_attention' => $quotation->client_attention,
            'client_cc' => $quotation->client_cc,
            'client_email' => $client?->email_perusahaan ?? $quotation->client_email,
            'client_address' => $client?->alamat_pengiriman_perusahaan ?? $quotation->client_address,
            'description_of_work' => $quotation->description_of_work,
            'discount' => $quotation->discount,
            'items' => $quotation->items->toArray(),
            'labors' => $quotation->labors->toArray(),
            'other_costs' => $quotation->otherCosts->toArray(),
        ]);
    }

    // ─── AJAX: Get Client Data from master client ─────────────────────
    public function getClientData(ClientModel $client)
    {
        return response()->json([
            'id' => $client->id,
            'nama_perusahaan' => $client->nama_perusahaan,
            'nama_kontak' => $client->nama_kontak_perusahaan,
            'email' => $client->email_perusahaan,
            'alamat_pengiriman_perusahaan' => $client->alamat_pengiriman_perusahaan,
            'attn' => $client->attn,
            'cc' => $client->cc,
        ]);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────
    private function resolveClientData(array $data): array
    {
        if (!empty($data['client_id'])) {
            $client = ClientModel::find($data['client_id']);
            if ($client) {
                $data['client_name'] = $data['client_name'] ?: ($client->nama_kontak_perusahaan ?: $client->nama_perusahaan);
                $data['client_company'] = $data['client_company'] ?: $client->nama_perusahaan;
                $data['client_email'] = $data['client_email'] ?: $client->email_perusahaan;
                $data['client_address'] = $data['client_address'] ?: $client->alamat_pengiriman_perusahaan;
                $data['client_attention'] = $data['client_attention'] ?: $client->attn;
                $data['client_cc'] = $data['client_cc'] ?: $client->cc;
            }
        }
        return $data;
    }

    private function validateSalesOrder(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'so_number' => 'required|string|unique:sales_orders,so_number' . ($ignoreId ? ",$ignoreId" : ''),
            'project_name' => 'nullable|string|max:255',
            'client_id' => 'nullable|exists:clients,id',
            'quotation_id' => 'nullable|exists:quotations,id',
            'quote_number' => 'nullable|string|max:255',
            'nomor_po' => 'nullable|string|max:255',
            'date' => 'required|date',
            'delivery_date' => 'nullable|date|after_or_equal:date',
            'customer_id' => 'nullable|string|max:100',
            'client_name' => 'nullable|string|max:255',
            'client_company' => 'nullable|string|max:255',
            'client_attention' => 'nullable|string|max:255',
            'client_cc' => 'nullable|string|max:255',
            'client_email' => 'nullable|email|max:255',
            'client_address' => 'nullable|string|max:255',
            'description_of_work' => 'nullable|string',
            'discount' => 'nullable|numeric|min:0',
            'tax_percentage' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:draft,confirmed,in_progress,completed,cancelled',
            'notes' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*.material_name' => 'required_with:items|string|max:255',
            'items.*.unit' => 'required_with:items|string|max:50',
            'items.*.qty' => 'required_with:items|numeric|min:0',
            'items.*.unit_price' => 'required_with:items|numeric|min:0',
            'items.*.materials' => 'nullable|array',
            'items.*.materials.*.material_name' => 'required_with:items.*.materials|string|max:255',
            'items.*.materials.*.satuan' => 'nullable|string|max:50',
            'items.*.materials.*.qty_required' => 'required_with:items.*.materials|numeric|min:0',
            'items.*.materials.*.unit_price' => 'required_with:items.*.materials|numeric|min:0',
            'labors' => 'nullable|array',
            'labors.*.labor_name' => 'required_with:labors|string|max:255',
            'labors.*.mp' => 'required_with:labors|integer|min:0',
            'labors.*.days' => 'required_with:labors|numeric|min:0',
            'labors.*.rate' => 'required_with:labors|numeric|min:0',
            'other_costs' => 'nullable|array',
            'other_costs.*.cost_name' => 'required_with:other_costs|string|max:255',
            'other_costs.*.qty' => 'required_with:other_costs|numeric|min:0',
            'other_costs.*.rate' => 'required_with:other_costs|numeric|min:0',
        ]);
    }

    private function calculateTotals(array $items, array $labors, array $otherCosts = [], float $taxPct = 0, float $discount = 0): array
    {
        $subMat = collect($items)->sum(function ($i) {
            $itemSub = ($i['qty'] ?? 0) * ($i['unit_price'] ?? 0);
            $matSub = collect($i['materials'] ?? [])->sum(fn($m) => ($m['qty_required'] ?? 0) * ($m['unit_price'] ?? 0));
            return $itemSub + $matSub;
        });
        $subLab = collect($labors)->sum(fn($l) => ($l['mp'] ?? 0) * ($l['days'] ?? 0) * ($l['rate'] ?? 0));
        $subOth = collect($otherCosts)->sum(fn($c) => ($c['qty'] ?? 0) * ($c['rate'] ?? 0));
        $subtotal = $subMat + $subLab + $subOth - $discount;
        $taxAmount = $subtotal * ($taxPct / 100);
        $total = $subtotal + $taxAmount;
        return [$subMat, $subLab, $subOth, $subtotal, $taxAmount, $total];
    }

    private function syncItems(SalesOrder $salesOrder, array $items): void
    {
        $salesOrder->items()->delete();
        foreach ($items as $i => $item) {
            if (empty($item['material_name']))
                continue;
            $soItem = SalesOrderItem::create([
                'sales_order_id' => $salesOrder->id,
                'sort_order' => $i + 1,
                'material_name' => $item['material_name'],
                'description' => $item['description'] ?? null,
                'unit' => $item['unit'] ?? 'Unit',
                'qty' => $item['qty'],
                'unit_price' => $item['unit_price'],
                'subtotal' => ($item['qty'] ?? 0) * ($item['unit_price'] ?? 0),
            ]);

            if (!empty($item['materials'])) {
                foreach ($item['materials'] as $m => $mat) {
                    if (empty($mat['material_name']))
                        continue;
                    \App\Models\SalesOrderItemMaterial::create([
                        'sales_order_item_id' => $soItem->id,
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

    private function syncLabors(SalesOrder $salesOrder, array $labors): void
    {
        $salesOrder->labors()->delete();
        foreach ($labors as $i => $labor) {
            if (empty($labor['labor_name']))
                continue;
            $sub = ($labor['mp'] ?? 0) * ($labor['days'] ?? 0) * ($labor['rate'] ?? 0);
            SalesOrderLabor::create([
                'sales_order_id' => $salesOrder->id,
                'sort_order' => $i + 1,
                'labor_name' => $labor['labor_name'],
                'mp' => $labor['mp'] ?? 1,
                'days' => $labor['days'] ?? 1,
                'rate' => $labor['rate'] ?? 0,
                'subtotal' => $sub,
            ]);
        }
    }

    private function syncOtherCosts(SalesOrder $salesOrder, array $otherCosts): void
    {
        $salesOrder->otherCosts()->delete();
        foreach ($otherCosts as $i => $cost) {
            if (empty($cost['cost_name']))
                continue;
            SalesOrderOtherCost::create([
                'sales_order_id' => $salesOrder->id,
                'sort_order' => $i + 1,
                'cost_name' => $cost['cost_name'],
                'qty' => $cost['qty'] ?? 1,
                'rate' => $cost['rate'] ?? 0,
                'subtotal' => ($cost['qty'] ?? 1) * ($cost['rate'] ?? 0),
            ]);
        }
    }
}
