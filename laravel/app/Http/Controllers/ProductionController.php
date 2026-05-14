<?php

namespace App\Http\Controllers;

use App\Models\Production;
use App\Models\ProductionItem;
use App\Models\ProductionMaterial;
use App\Models\SalesOrder;
use App\Models\AsetModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ProductionController extends Controller
{
    // ─── List ────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Production::with(['salesOrder', 'items.materials'])->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('production_number', 'like', "%$s%")
                  ->orWhere('so_number', 'like', "%$s%")
                  ->orWhere('project_name', 'like', "%$s%")
                  ->orWhere('client_company', 'like', "%$s%");
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $productions = $query->paginate(15)->withQueryString();
        return view('admin.productions.index', compact('productions'));
    }

    // ─── Create ───────────────────────────────────────────────────────────────
    public function create(Request $request)
    {
        $productionNumber = Production::generateProductionNumber();
        $salesOrders      = SalesOrder::with(['items', 'labors'])
            ->whereIn('status', ['confirmed', 'in_progress', 'completed'])
            ->latest()
            ->get();

        $assets   = AsetModel::all();
        $soId     = $request->input('sales_order_id');
        $selected = $soId ? SalesOrder::with(['items', 'labors'])->find($soId) : null;

        return view('admin.productions.create', compact(
            'productionNumber', 'salesOrders', 'assets', 'selected'
        ));
    }

    // ─── Store ────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $this->validateProduction($request);

        DB::transaction(function () use ($validated, $request) {
            $production = Production::create($validated);
            $this->syncItems($production, $request->items ?? []);
        });

        return redirect()->route('admin.productions.index')
            ->with('success', 'Rencana Produksi berhasil dibuat.');
    }

    // ─── Show ─────────────────────────────────────────────────────────────────
    public function show(Production $production)
    {
        $production->load(['items.materials.asset', 'salesOrder']);
        return view('admin.productions.show', compact('production'));
    }

    // ─── Edit ─────────────────────────────────────────────────────────────────
    public function edit(Production $production)
    {
        $production->load(['items.materials']);
        $productionNumber = $production->production_number;
        $salesOrders      = SalesOrder::with(['items', 'labors'])
            ->whereIn('status', ['confirmed', 'in_progress', 'completed'])
            ->latest()
            ->get();
        $assets = AsetModel::all();

        return view('admin.productions.create', compact(
            'production', 'productionNumber', 'salesOrders', 'assets'
        ));
    }

    // ─── Update ───────────────────────────────────────────────────────────────
    public function update(Request $request, Production $production)
    {
        $validated = $this->validateProduction($request, $production->id);

        DB::transaction(function () use ($validated, $request, $production) {
            $production->update($validated);
            $this->syncItems($production, $request->items ?? []);
        });

        return redirect()->route('admin.productions.index')
            ->with('success', 'Rencana Produksi berhasil diperbarui.');
    }

    // ─── Delete ───────────────────────────────────────────────────────────────
    public function destroy(Production $production)
    {
        $production->delete();
        return redirect()->route('admin.productions.index')
            ->with('success', 'Rencana Produksi berhasil dihapus.');
    }

    // ─── PDF ──────────────────────────────────────────────────────────────────
    public function pdf(Production $production)
    {
        $production->load(['items.materials.asset', 'salesOrder']);

        $logoPath = public_path('assets/gambar/logo-sti.png');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView('admin.productions.pdf', compact('production', 'logoBase64'))
            ->setPaper('a4', 'portrait')
            ->setOption('defaultFont', 'DejaVu Sans')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', false);

        $filename = 'ProductionPlan-' . $production->production_number . '.pdf';
        return $pdf->stream($filename);
    }

    // ─── AJAX: get Sales Order items for auto-fill ───────────────────────────
    public function getSoItems(SalesOrder $salesOrder)
    {
        $salesOrder->load(['items', 'labors']);

        return response()->json([
            'project_name'   => $salesOrder->project_name,
            'client_company' => $salesOrder->client_company,
            'items'          => $salesOrder->items->toArray(),
        ]);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────
    private function validateProduction(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'production_number' => 'required|string|unique:productions,production_number' . ($ignoreId ? ",$ignoreId" : ''),
            'sales_order_id'    => 'required|exists:sales_orders,id',
            'so_number'         => 'nullable|string|max:255',
            'project_name'      => 'nullable|string|max:255',
            'client_company'    => 'nullable|string|max:255',
            'date'              => 'required|date',
            'target_date'       => 'nullable|date|after_or_equal:date',
            'status'            => 'required|in:planned,in_progress,completed,cancelled',
            'notes'             => 'nullable|string',
            'items'             => 'nullable|array',
            'items.*.product_name' => 'required_with:items|string|max:255',
            'items.*.product_qty'  => 'required_with:items|numeric|min:0',
            'items.*.unit'         => 'required_with:items|string|max:50',
            'items.*.materials'               => 'nullable|array',
            'items.*.materials.*.asset_id'    => 'nullable|exists:assets,id',
            'items.*.materials.*.nama_bahan_baku' => 'required_with:items.*.materials|string|max:255',
            'items.*.materials.*.qty_required'=> 'required_with:items.*.materials|numeric|min:0',
            'items.*.materials.*.satuan'      => 'required_with:items.*.materials|string|max:50',
        ]);
    }

    private function syncItems(Production $production, array $items): void
    {
        // Delete existing items (cascade to materials)
        $production->items()->delete();

        foreach ($items as $i => $item) {
            if (empty($item['product_name'])) continue;

            $prodItem = ProductionItem::create([
                'production_id'       => $production->id,
                'sales_order_item_id' => $item['sales_order_item_id'] ?? null,
                'product_name'        => $item['product_name'],
                'product_qty'         => $item['product_qty'] ?? 1,
                'unit'                => $item['unit'] ?? 'Unit',
                'status'              => $item['status'] ?? 'pending',
                'sort_order'          => $i + 1,
            ]);

            // Sync materials for this product
            $materials = $item['materials'] ?? [];
            foreach ($materials as $m => $mat) {
                if (empty($mat['nama_bahan_baku'])) continue;
                ProductionMaterial::create([
                    'production_item_id' => $prodItem->id,
                    'asset_id'           => $mat['asset_id'] ?? null,
                    'nama_bahan_baku'    => $mat['nama_bahan_baku'],
                    'qty_required'       => $mat['qty_required'] ?? 0,
                    'satuan'             => $mat['satuan'] ?? 'pcs',
                ]);
            }
        }
    }
}
