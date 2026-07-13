<?php

namespace App\Http\Controllers;

use App\Models\Production;
use App\Models\ProductionItem;
use App\Models\ProductionItemMaterial;
use App\Models\SalesOrder;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Auth;
class ProductionController extends Controller
{
    // ─── List ────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Production::with(['salesOrder', 'items.materials'])->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nomor_produksi', 'like', "%$s%")
                    ->orWhere('nomor_salesorder', 'like', "%$s%")
                    ->orWhereHas('salesOrder', function ($sq) use ($s) {
                        $sq->where('nama_project', 'like', "%$s%")
                            ->orWhereHas('client', function ($cq) use ($s) {
                                $cq->where('nama_perusahaan', 'like', "%$s%");
                            });
                    });
            });
        }
        if ($request->filled('status')) {
            $query->where('status_produksi', $request->status);
        }

        $productions = $query->paginate(15)->withQueryString();
        return view('admin.productions.index', compact('productions'));
    }

    // ─── Create ───────────────────────────────────────────────────────────────
    public function create(Request $request)
    {
        $productionNumber = Production::generateProductionNumber();
        $salesOrders = SalesOrder::with(['items.materials', 'labors'])
            ->whereIn('status', ['confirmed', 'in_progress', 'completed'])
            ->latest()
            ->get();

        $assets = Material::all();
        $soNomor = $request->input('nomor_salesorder');
        $selected = $soNomor ? SalesOrder::with(['items.materials'])->where('nomor_salesorder', $soNomor)->first() : null;

        return view('admin.productions.create', compact(
            'productionNumber',
            'salesOrders',
            'assets',
            'selected'
        ));
    }

    // ─── Store ────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $this->validateProduction($request);

        DB::transaction(function () use ($validated, $request) {
            // Pastikan PIC selalu ada (nullable)
            $data = $validated;
            $data['PIC'] = $validated['PIC'] ?? Auth::user()->namalengkap;

            $production = Production::create($data);
            $this->syncItems($production, $request->items ?? []);
        });

        return redirect()->route('admin.productions.index')
            ->with('success', 'Rencana Produksi berhasil dibuat.');
    }

    // ─── Show ─────────────────────────────────────────────────────────────────
    public function show(Production $production)
    {
        $production->load(['items.materials.material', 'salesOrder']);
        return view('admin.productions.show', compact('production'));
    }

    // ─── Edit (status only) ───────────────────────────────────────────────────
    public function edit(Production $production)
    {
        return view('admin.productions.edit', compact('production'));
    }

    // ─── Update Status Only ───────────────────────────────────────────────────
    public function update(Request $request, Production $production)
    {
        $validated = $request->validate([
            'status_produksi' => 'required|in:planned,in_progress,completed,cancelled',
            'keterangan' => 'nullable|string',
        ]);

        DB::transaction(function () use ($production, $validated) {
            $production->update($validated);
        });

        return redirect()->route('admin.productions.index')
            ->with('success', 'Status produksi berhasil diperbarui.');
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
        $production->load(['items.materials.material', 'salesOrder']);

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

        $filename = 'ProductionPlan-' . $production->nomor_produksi . '.pdf';
        return $pdf->stream($filename);
    }

    // ─── AJAX: get Sales Order items for auto-fill ───────────────────────────
    public function getSoItems(SalesOrder $salesOrder)
    {
        $salesOrder->load(['items.materials']);

        return response()->json([
            'nama_project' => $salesOrder->nama_project,
            'nama_perusahaan' => $salesOrder->client->nama_perusahaan ?? '',
            'nomor_po' => $salesOrder->nomor_po,
            'items' => $salesOrder->items->toArray(),
        ]);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────
    private function validateProduction(Request $request, ?string $ignoreId = null): array
    {
        return $request->validate([
            'nomor_produksi' => 'required|string|unique:productions,nomor_produksi' . ($ignoreId ? ",$ignoreId,nomor_produksi" : ''),
            'nomor_salesorder' => 'required|exists:sales_orders,nomor_salesorder',
            'tanggal_mulai' => 'required|date',
            'estimasi_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'status_produksi' => 'required|in:planned,in_progress,completed,cancelled',
            'PIC' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*.nama_item' => 'required_with:items|string|max:255',
            'items.*.jumlah_item' => 'required_with:items|numeric|min:0',
            'items.*.satuan' => 'required_with:items|string|max:50',
            'items.*.materials' => 'nullable|array',
            'items.*.materials.*.id_material' => 'nullable',
            'items.*.materials.*.nama_material' => 'required_with:items.*.materials|string|max:255',
            'items.*.materials.*.jumlah_material' => 'required_with:items.*.materials|numeric|min:0',
            'items.*.materials.*.satuan_material' => 'required_with:items.*.materials|string|max:50',
        ]);
    }

    private function syncItems(Production $production, array $items): void
    {
        // Delete existing items (cascade to materials)
        $production->items()->delete();

        foreach ($items as $i => $item) {
            if (empty($item['nama_item']))
                continue;

            $prodItem = ProductionItem::create([
                'nomor_produksi' => $production->nomor_produksi,
                'nama_item' => $item['nama_item'],
                'jumlah_item' => $item['jumlah_item'] ?? 1,
                'satuan' => $item['satuan'] ?? 'Unit',
                'harga_item' => 0
            ]);

            // Sync materials for this product
            $materials = $item['materials'] ?? [];
            foreach ($materials as $m => $mat) {
                if (empty($mat['nama_material']))
                    continue;
                ProductionItemMaterial::create([
                    'id_item' => $prodItem->id_item,
                    'id_material' => $mat['id_material'] ?? null,
                    'nama_material' => $mat['nama_material'],
                    'jumlah_material' => $mat['jumlah_material'] ?? 0,
                    'satuan_material' => $mat['satuan_material'] ?? 'pcs',
                    'harga_material' => 0,
                ]);
            }
        }
    }
}