<?php

namespace App\Http\Controllers;

use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\SalesOrderLabor;
use App\Models\Quotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class SalesOrderController extends Controller
{
    // ─── Default labor list ───────────────────────────────────────────────────
    private array $defaultLabors = [
        ['labor_name' => 'Mechanical Design',    'mp' => 1, 'days' => 1, 'rate' => 1500000],
        ['labor_name' => 'Electrical Design',     'mp' => 1, 'days' => 1, 'rate' => 1500000],
        ['labor_name' => 'Assembling',            'mp' => 2, 'days' => 1, 'rate' => 1000000],
        ['labor_name' => 'Wiring',                'mp' => 2, 'days' => 1, 'rate' => 1000000],
        ['labor_name' => 'Commissioning',         'mp' => 3, 'days' => 1, 'rate' => 1000000],
        ['labor_name' => 'Programming',           'mp' => 1, 'days' => 1, 'rate' => 1000000],
        ['labor_name' => 'Setting & Trainhouse',  'mp' => 2, 'days' => 1, 'rate' => 1000000],
        ['labor_name' => 'Installation',          'mp' => 4, 'days' => 1, 'rate' => 1500000],
        ['labor_name' => 'Setting & Trainonsite', 'mp' => 2, 'days' => 1, 'rate' => 1000000],
        ['labor_name' => 'Accomodation',          'mp' => 1, 'days' => 1, 'rate' => 0],
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
        $soNumber     = SalesOrder::generateSONumber();
        $defaultLabors = $this->defaultLabors;
        $quotations   = Quotation::whereIn('status', ['approved', 'sent'])->latest()->get();
        return view('admin.sales-orders.create', compact('soNumber', 'defaultLabors', 'quotations'));
    }

    // ─── Store ────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $this->validateSalesOrder($request);

        DB::transaction(function () use ($validated, $request) {
            [$subMat, $subLab, $subtotal, $taxAmount, $total] = $this->calculateTotals(
                $request->items ?? [], $request->labors ?? [], $validated['tax_percentage']
            );

            $salesOrder = SalesOrder::create(array_merge($validated, [
                'subtotal_material' => $subMat,
                'subtotal_labor'    => $subLab,
                'subtotal'          => $subtotal,
                'tax_amount'        => $taxAmount,
                'total'             => $total,
            ]));

            $this->syncItems($salesOrder, $request->items ?? []);
            $this->syncLabors($salesOrder, $request->labors ?? []);
        });

        return redirect()->route('admin.sales-orders.index')
            ->with('success', 'Sales Order berhasil dibuat.');
    }

    // ─── Show ─────────────────────────────────────────────────────────────────
    public function show(SalesOrder $salesOrder)
    {
        $salesOrder->load('items', 'labors', 'quotation');
        return view('admin.sales-orders.show', compact('salesOrder'));
    }

    // ─── Edit ─────────────────────────────────────────────────────────────────
    public function edit(SalesOrder $salesOrder)
    {
        $salesOrder->load('items', 'labors');
        $soNumber     = $salesOrder->so_number;
        $defaultLabors = $this->defaultLabors;
        $quotations   = Quotation::whereIn('status', ['approved', 'sent'])->latest()->get();
        return view('admin.sales-orders.edit', compact('salesOrder', 'soNumber', 'defaultLabors', 'quotations'));
    }

    // ─── Update ───────────────────────────────────────────────────────────────
    public function update(Request $request, SalesOrder $salesOrder)
    {
        $validated = $this->validateSalesOrder($request, $salesOrder->id);

        DB::transaction(function () use ($validated, $request, $salesOrder) {
            [$subMat, $subLab, $subtotal, $taxAmount, $total] = $this->calculateTotals(
                $request->items ?? [], $request->labors ?? [], $validated['tax_percentage']
            );

            $salesOrder->update(array_merge($validated, [
                'subtotal_material' => $subMat,
                'subtotal_labor'    => $subLab,
                'subtotal'          => $subtotal,
                'tax_amount'        => $taxAmount,
                'total'             => $total,
            ]));

            $this->syncItems($salesOrder, $request->items ?? []);
            $this->syncLabors($salesOrder, $request->labors ?? []);
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
        $salesOrder->load('items', 'labors');

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
        $quotation->load('items', 'labors');
        $soNumber     = SalesOrder::generateSONumber();
        $defaultLabors = $this->defaultLabors;
        $quotations   = Quotation::whereIn('status', ['approved', 'sent'])->latest()->get();

        return view('admin.sales-orders.create', compact(
            'soNumber', 'defaultLabors', 'quotations', 'quotation'
        ));
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────
    private function validateSalesOrder(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'so_number'           => 'required|string|unique:sales_orders,so_number' . ($ignoreId ? ",$ignoreId" : ''),
            'project_name'        => 'nullable|string|max:255',
            'quotation_id'        => 'nullable|exists:quotations,id',
            'quote_number'        => 'nullable|string|max:255',
            'date'                => 'required|date',
            'delivery_date'       => 'nullable|date|after_or_equal:date',
            'customer_id'         => 'nullable|string|max:100',
            'client_name'         => 'required|string|max:255',
            'client_company'      => 'required|string|max:255',
            'client_attention'    => 'nullable|string|max:255',
            'client_cc'           => 'nullable|string|max:255',
            'client_email'        => 'nullable|email|max:255',
            'description_of_work' => 'nullable|string',
            'tax_percentage'      => 'required|numeric|min:0|max:100',
            'status'              => 'required|in:draft,confirmed,in_progress,completed,cancelled',
            'notes'               => 'nullable|string',
            'items'               => 'nullable|array',
            'items.*.material_name' => 'required_with:items|string|max:255',
            'items.*.unit'          => 'required_with:items|string|max:50',
            'items.*.qty'           => 'required_with:items|numeric|min:0',
            'items.*.unit_price'    => 'required_with:items|numeric|min:0',
            'labors'                => 'nullable|array',
            'labors.*.labor_name'   => 'required_with:labors|string|max:255',
            'labors.*.mp'           => 'required_with:labors|integer|min:0',
            'labors.*.days'         => 'required_with:labors|numeric|min:0',
            'labors.*.rate'         => 'required_with:labors|numeric|min:0',
        ]);
    }

    private function calculateTotals(array $items, array $labors, float $taxPct): array
    {
        $subMat = collect($items)->sum(fn($i) => ($i['qty'] ?? 0) * ($i['unit_price'] ?? 0));
        $subLab = collect($labors)->sum(fn($l) => ($l['mp'] ?? 0) * ($l['days'] ?? 0) * ($l['rate'] ?? 0));
        $subtotal  = $subMat + $subLab;
        $taxAmount = $subtotal * ($taxPct / 100);
        $total     = $subtotal + $taxAmount;
        return [$subMat, $subLab, $subtotal, $taxAmount, $total];
    }

    private function syncItems(SalesOrder $salesOrder, array $items): void
    {
        $salesOrder->items()->delete();
        foreach ($items as $i => $item) {
            if (empty($item['material_name'])) continue;
            SalesOrderItem::create([
                'sales_order_id' => $salesOrder->id,
                'sort_order'     => $i + 1,
                'material_name'  => $item['material_name'],
                'description'    => $item['description'] ?? null,
                'unit'           => $item['unit'] ?? 'Unit',
                'qty'            => $item['qty'],
                'unit_price'     => $item['unit_price'],
                'subtotal'       => ($item['qty'] ?? 0) * ($item['unit_price'] ?? 0),
            ]);
        }
    }

    private function syncLabors(SalesOrder $salesOrder, array $labors): void
    {
        $salesOrder->labors()->delete();
        foreach ($labors as $i => $labor) {
            if (empty($labor['labor_name'])) continue;
            $sub = ($labor['mp'] ?? 0) * ($labor['days'] ?? 0) * ($labor['rate'] ?? 0);
            SalesOrderLabor::create([
                'sales_order_id' => $salesOrder->id,
                'sort_order'     => $i + 1,
                'labor_name'     => $labor['labor_name'],
                'mp'             => $labor['mp'] ?? 1,
                'days'           => $labor['days'] ?? 1,
                'rate'           => $labor['rate'] ?? 0,
                'subtotal'       => $sub,
            ]);
        }
    }
}
