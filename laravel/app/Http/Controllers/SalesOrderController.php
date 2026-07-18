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
use Auth;

class SalesOrderController extends Controller
{
    // ─── Default labor list ───────────────────────────────────────────────────
    private array $defaultLabors = [
        ['labor_name' => 'Mechanical Design', 'mp' => 1, 'days' => 1, 'rate' => 1500000],
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
        $query = SalesOrder::with('items', 'labors', 'client')->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nomor_salesorder', 'like', "%$s%")
                    ->orWhere('nama_project', 'like', "%$s%")
                    ->orWhereHas('client', function ($cq) use ($s) {
                        $cq->where('nama_perusahaan', 'like', "%$s%")
                            ->orWhere('nama_kontak', 'like', "%$s%");
                    });
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
        // return response()->json($request->all());
        try {
            $validated = $this->validateSalesOrder($request);
            $validated = $this->resolveClientData($validated);

            DB::transaction(function () use ($validated, $request) {
                $diskon = (float) ($validated['diskon'] ?? 0);
                [$subProd, $subMat, $subLab, $subOth, $subtotal, $pajakAmount, $grandtotal] = $this->calculateTotals(
                    $request->items ?? [],
                    $request->labors ?? [],
                    $request->other_costs ?? [],
                    $validated['pajak'],
                    $diskon
                );

                $salesOrder = SalesOrder::create(array_merge($validated, [
                    'nomor_quotation' => $validated['nomor_quotation'],
                    'diskon' => $diskon,
                    'subtotal_produksi' => $subProd,
                    'subtotal_material' => $subMat,
                    'subtotal_labor' => $subLab,
                    'subtotal_lainlain' => $subOth,
                    'pajak' => $validated['pajak'],
                    'grandtotal' => $grandtotal,
                    'id_staff' => Auth::user()->id
                ]));

                $this->syncItems($salesOrder, $request->items ?? []);
                $this->syncLabors($salesOrder, $request->labors ?? []);
                $this->syncOtherCosts($salesOrder, $request->other_costs ?? []);
            });

            return redirect()->route('admin.sales-orders.index')
                ->with('success', 'Sales Order berhasil dibuat.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal membuat Sales Order: ' . $e->getMessage())
                ->withInput();
        }
    }

    // ─── Show ─────────────────────────────────────────────────────────────────
    public function show(SalesOrder $salesOrder)
    {
        $salesOrder->load('items', 'items.materials', 'labors', 'otherCosts', 'quotation');
        return view('admin.sales-orders.show', compact('salesOrder'));
    }

    // ─── Edit ─────────────────────────────────────────────────────────────────
    public function edit(SalesOrder $salesOrder)
    {
        $salesOrder->load('items.materials', 'labors', 'otherCosts');
        $soNumber = $salesOrder->nomor_salesorder;
        $defaultLabors = $this->defaultLabors;
        $quotations = Quotation::whereIn('status', ['approved', 'sent'])->latest()->get();
        $clients = ClientModel::all();

        // Prepare seed data for edit mode
        $oldItems = $salesOrder->items->map(function ($item) {
            return [
                'material_name' => $item->nama_item,
                'description' => $item->deskripsi_item,
                'unit' => $item->satuan,
                'qty' => $item->jumlah_item,
                'unit_price' => $item->harga_item,
                'materials' => $item->materials->map(function ($mat) {
                    return [
                        'asset_id' => $mat->id_material,
                        'material_name' => $mat->nama_material,
                        'satuan' => $mat->satuan_material,
                        'qty_required' => $mat->jumlah_material,
                        'unit_price' => $mat->harga_material,
                    ];
                })->toArray(),
            ];
        })->toArray();

        $oldLabors = $salesOrder->labors->map(function ($labor) {
            return [
                'labor_name' => $labor->nama_labor,
                'mp' => $labor->jumlah_sdm ?? 1,
                'days' => $labor->jumlah_hari ?? 1,
                'rate' => $labor->rate_hari ?? 0,
            ];
        })->toArray();

        $oldOtherCosts = $salesOrder->otherCosts->map(function ($cost) {
            return [
                'cost_name' => $cost->nama_biaya,
                'qty' => 1,
                'rate' => $cost->jumlah_biaya ?? 0,
            ];
        })->toArray();

        return view('admin.sales-orders.edit', compact('salesOrder', 'soNumber', 'defaultLabors', 'quotations', 'clients', 'oldItems', 'oldLabors', 'oldOtherCosts'));
    }

    // ─── Update ───────────────────────────────────────────────────────────────
    public function update(Request $request, SalesOrder $salesOrder)
    {
        try {
            $validated = $this->validateSalesOrder($request, $salesOrder->nomor_salesorder);
            $validated = $this->resolveClientData($validated);

            DB::transaction(function () use ($validated, $request, $salesOrder) {
                $diskon = (float) ($validated['diskon'] ?? 0);
                [$subProd, $subMat, $subLab, $subOth, $subtotal, $pajakAmount, $grandtotal] = $this->calculateTotals(
                    $request->items ?? [],
                    $request->labors ?? [],
                    $request->other_costs ?? [],
                    $validated['pajak'],
                    $diskon
                );

                $salesOrder->update(array_merge($validated, [
                    'diskon' => $diskon,
                    'subtotal_produksi' => $subProd,
                    'subtotal_material' => $subMat,
                    'subtotal_labor' => $subLab,
                    'subtotal_lainlain' => $subOth,
                    'pajak' => $validated['pajak'],
                    'grandtotal' => $grandtotal,
                ]));

                $this->syncItems($salesOrder, $request->items ?? []);
                $this->syncLabors($salesOrder, $request->labors ?? []);
                $this->syncOtherCosts($salesOrder, $request->other_costs ?? []);
            });

            return redirect()->route('admin.sales-orders.show', $salesOrder)
                ->with('success', 'Sales Order berhasil diperbarui.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui Sales Order: ' . $e->getMessage())
                ->withInput();
        }
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
        $salesOrder->load('items', 'items.materials', 'labors', 'otherCosts', 'quotation');

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

        $filename = 'SalesOrder-' . $salesOrder->nomor_salesorder . '.pdf';
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

        // Map items to match SalesOrder item structure
        $items = $quotation->items->map(function ($item) {
            return [
                'material_name' => $item->nama_item,
                'description' => $item->deskripsi_item,
                'qty' => $item->jumlah_item,
                'unit' => $item->satuan,
                'unit_price' => $item->harga_item,
                'materials' => $item->materials->map(function ($mat) {
                    return [
                        'material_name' => $mat->nama_material,
                        'satuan' => $mat->satuan_material,
                        'qty_required' => $mat->jumlah_material,
                        'unit_price' => $mat->harga_material,
                        'asset_id' => $mat->id_material,
                    ];
                })->toArray(),
            ];
        })->toArray();

        // Map labors
        $labors = $quotation->labors->map(function ($labor) {
            return [
                'labor_name' => $labor->nama_labor ?? '',
                'mp' => $labor->jumlah_sdm ?? 1,
                'days' => $labor->jumlah_hari ?? 1,
                'rate' => $labor->rate_hari ?? 0,
            ];
        })->toArray();

        // Map other costs
        // QuotationOtherCost only has nama_biaya and jumlah_biaya (no qty/rate separation)
        $otherCosts = $quotation->otherCosts->map(function ($cost) {
            return [
                'cost_name' => $cost->nama_biaya ?? '',
                'qty' => 1,
                'rate' => $cost->jumlah_biaya ?? 0,
            ];
        })->toArray();

        return response()->json([
            'id_client' => $quotation->id_client,
            'id_customer' => $client?->id_customer ?? null,
            'nama_project' => $quotation->nama_project,
            'nomor_quotation' => $quotation->nomor_quotation,
            'nama_kontak' => $client?->nama_kontak ?? null,
            'nama_perusahaan' => $client?->nama_perusahaan ?? null,
            'email_perusahaan' => $client?->email_perusahaan ?? null,
            'alamat_perusahaan' => $client?->alamat_perusahaan ?? null,
            'diskon' => $quotation->diskon,
            'keterangan' => $quotation->keterangan,
            'items' => $items,
            'labors' => $labors,
            'other_costs' => $otherCosts,
        ]);
    }

    // ─── AJAX: Get Client Data from master client ─────────────────────
    public function getClientData($id_client)
    {
        $client = ClientModel::where('id', $id_client)->first();
        return response()->json([
            'id_customer' => $client->id_customer,
            'nama_perusahaan' => $client->nama_perusahaan,
            'nama_kontak' => $client->nama_kontak,
            'email_perusahaan' => $client->email_perusahaan,
            'alamat_perusahaan' => $client->alamat_perusahaan,
        ]);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────
    private function resolveClientData(array $data): array
    {
        // Client data is now resolved via id_client relationship
        // No additional client fields to resolve in sales_orders table
        return $data;
    }

    private function validateSalesOrder(Request $request, ?string $ignoreId = null): array
    {
        return $request->validate([
            'nomor_salesorder' => 'required|string|unique:sales_orders,nomor_salesorder' . ($ignoreId ? ",$ignoreId,nomor_salesorder" : ''),
            'nama_project' => 'nullable|string|max:255',
            'id_client' => 'nullable|exists:customers,id',
            'nomor_quotation' => 'nullable|string|max:255',
            'nomor_po' => 'nullable|string|max:255',
            'tanggal_pembuatan' => 'required|date',
            'customer_id' => 'nullable|string|max:100',
            'keterangan' => 'nullable|string',
            'diskon' => 'nullable|numeric|min:0',
            'pajak' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:draft,confirmed,in_progress,completed,cancelled',
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

    private function calculateTotals(array $items, array $labors, array $otherCosts = [], float $pajakPct = 0, float $diskon = 0): array
    {
        // Production subtotal (qty * unit_price) - this is the PRODUCTION cost
        $subProd = collect($items)->sum(function ($i) {
            return ($i['qty'] ?? 0) * ($i['unit_price'] ?? 0);
        });

        // Material subtotal - materials are ADDITIONAL costs inside products
        $subMat = collect($items)->sum(function ($i) {
            return collect($i['materials'] ?? [])->sum(fn($m) => ($m['qty_required'] ?? 0) * ($m['unit_price'] ?? 0));
        });

        $subLab = collect($labors)->sum(fn($l) => ($l['mp'] ?? 0) * ($l['days'] ?? 0) * ($l['rate'] ?? 0));
        $subOth = collect($otherCosts)->sum(fn($c) => ($c['qty'] ?? 0) * ($c['rate'] ?? 0));

        // Subtotal before discount
        $subtotal = $subProd + $subMat + $subLab + $subOth;

        // Discount is applied before tax
        $taxableBase = max($subtotal - $diskon, 0);
        $pajakAmount = $taxableBase * ($pajakPct / 100);
        $grandtotal = $taxableBase + $pajakAmount;

        return [$subProd, $subMat, $subLab, $subOth, $subtotal, $pajakAmount, $grandtotal];
    }

    private function syncItems(SalesOrder $salesOrder, array $items): void
    {
        $salesOrder->items()->delete();
        foreach ($items as $i => $item) {
            if (empty($item['material_name']))
                continue;
            $soItem = SalesOrderItem::create([
                'nomor_salesorder' => $salesOrder->nomor_salesorder,
                'nama_item' => $item['material_name'],
                'deskripsi_item' => $item['description'] ?? null,
                'satuan' => $item['unit'] ?? 'Unit',
                'jumlah_item' => $item['qty'],
                'harga_item' => $item['unit_price'],
                // 'subtotal' => ($item['qty'] ?? 0) * ($item['unit_price'] ?? 0),
            ]);

            if (!empty($item['materials'])) {
                foreach ($item['materials'] as $m => $mat) {
                    if (empty($mat['material_name']))
                        continue;
                    \App\Models\SalesOrderItemMaterial::create([
                        'id_item' => $soItem->id_item,
                        'id_material' => $mat['asset_id'] ?? null,
                        'nama_material' => $mat['material_name'],
                        'jumlah_material' => $mat['qty_required'] ?? 0,
                        'satuan_material' => $mat['satuan'] ?? 'pcs',
                        'harga_material' => $mat['unit_price'] ?? 0,
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
            // $sub = ($labor['mp'] ?? 0) * ($labor['days'] ?? 0) * ($labor['rate'] ?? 0);
            SalesOrderLabor::create([
                'nomor_salesorder' => $salesOrder->nomor_salesorder,
                'nama_labor' => $labor['labor_name'],
                'jumlah_sdm' => $labor['mp'] ?? 1,
                'jumlah_hari' => $labor['days'] ?? 1,
                'rate_hari' => $labor['rate'] ?? 0,
            ]);
        }
    }

    private function syncOtherCosts(SalesOrder $salesOrder, array $otherCosts): void
    {
        $salesOrder->otherCosts()->delete();
        foreach ($otherCosts as $i => $cost) {
            if (empty($cost['cost_name']))
                continue;
            $qty = (float) ($cost['qty'] ?? 1);
            $rate = (float) ($cost['rate'] ?? 0);
            SalesOrderOtherCost::create([
                'nomor_salesorder' => $salesOrder->nomor_salesorder,
                'nama_biaya' => $cost['cost_name'],
                'jumlah_biaya' => $qty * $rate,
            ]);
        }
    }
}
