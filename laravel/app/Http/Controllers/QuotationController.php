<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\QuotationItemMaterial;
use App\Models\QuotationLabor;
use App\Models\QuotationOtherCost;
use App\Models\ClientModel;
use App\Models\AsetModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class QuotationController extends Controller
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
        $query = Quotation::with('items', 'labors', 'client')->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('quote_number', 'like', "%$s%")
                    ->orWhere('nomor_po', 'like', "%$s%")
                    ->orWhere('client_name', 'like', "%$s%")
                    ->orWhere('client_company', 'like', "%$s%")
                    ->orWhere('project_name', 'like', "%$s%");
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $quotations = $query->paginate(15)->withQueryString();
        return view('admin.quotations.index', compact('quotations'));
    }

    // ─── Create ───────────────────────────────────────────────────────────────
    public function create()
    {
        $quoteNumber = Quotation::generateQuoteNumber();
        $defaultLabors = $this->defaultLabors;
        $clients = ClientModel::all();
        $assets = AsetModel::all();
        return view('admin.quotations.create', compact('quoteNumber', 'defaultLabors', 'clients', 'assets'));
    }

    // ─── Store ────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $this->validateQuotation($request);

        // Auto-fill client fields if client_id is provided
        $validated = $this->resolveClientData($validated);

        DB::transaction(function () use ($validated, $request) {
            [$subMat, $subLab, $subOth, $total] = $this->calculateTotals(
                $request->items ?? [],
                $request->labors ?? [],
                $request->other_costs ?? []
            );

            $quotation = Quotation::create(array_merge($validated, [
                'subtotal_material' => $subMat,
                'subtotal_labor' => $subLab,
                'subtotal_other_cost' => $subOth,
                'subtotal' => $subMat + $subLab + $subOth,
                'total' => $total,
            ]));

            $this->syncItems($quotation, $request->items ?? []);
            $this->syncLabors($quotation, $request->labors ?? []);
            $this->syncOtherCosts($quotation, $request->other_costs ?? []);
        });

        return redirect()->route('admin.quotations.index')
            ->with('success', 'Quotation berhasil dibuat.');
    }

    // ─── Show ─────────────────────────────────────────────────────────────────
    public function show(Quotation $quotation)
    {
        $quotation->load('items.materials.asset', 'labors', 'otherCosts', 'client');
        return view('admin.quotations.show', compact('quotation'));
    }

    // ─── Edit ─────────────────────────────────────────────────────────────────
    public function edit(Quotation $quotation)
    {
        $quotation->load('items.materials', 'labors', 'otherCosts');
        $quoteNumber = $quotation->quote_number;
        $defaultLabors = $this->defaultLabors;
        $clients = ClientModel::all();
        $assets = AsetModel::all();
        return view('admin.quotations.edit', compact('quotation', 'quoteNumber', 'defaultLabors', 'clients', 'assets'));
    }

    // ─── Update ───────────────────────────────────────────────────────────────
    public function update(Request $request, Quotation $quotation)
    {
        $validated = $this->validateQuotation($request, $quotation->id);
        $validated = $this->resolveClientData($validated);

        DB::transaction(function () use ($validated, $request, $quotation) {
            [$subMat, $subLab, $subOth, $total] = $this->calculateTotals(
                $request->items ?? [],
                $request->labors ?? [],
                $request->other_costs ?? []
            );

            $quotation->update(array_merge($validated, [
                'subtotal_material' => $subMat,
                'subtotal_labor' => $subLab,
                'subtotal_other_cost' => $subOth,
                'subtotal' => $subMat + $subLab + $subOth,
                'total' => $total,
            ]));

            $this->syncItems($quotation, $request->items ?? []);
            $this->syncLabors($quotation, $request->labors ?? []);
            $this->syncOtherCosts($quotation, $request->other_costs ?? []);
        });

        return redirect()->route('admin.quotations.show', $quotation)
            ->with('success', 'Quotation berhasil diperbarui.');
    }

    // ─── Delete ───────────────────────────────────────────────────────────────
    public function destroy(Quotation $quotation)
    {
        $quotation->delete();
        return redirect()->route('admin.quotations.index')
            ->with('success', 'Quotation berhasil dihapus.');
    }

    // ─── PDF ──────────────────────────────────────────────────────────────────
    public function pdf(Quotation $quotation)
    {
        $quotation->load('items.materials.asset', 'labors', 'otherCosts', 'client');

        // Base64 encode logo agar bisa dipakai di DomPDF (tidak butuh remote)
        $logoPath = public_path('assets/gambar/logo-sti.png');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView('admin.quotations.pdf-design-a', compact('quotation', 'logoBase64'))
            ->setPaper('a4', 'portrait')
            ->setOption('defaultFont', 'DejaVu Sans')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', false);

        $filename = 'ProjectQuote-' . $quotation->quote_number . '.pdf';
        return $pdf->stream($filename);
    }

    // ─── Quick-add client via modal ──────────────────────────────────────────
    public function quickAddClient(Request $request)
    {
        $validated = $request->validate([
            'id_perusahaan' => 'required|string|unique:clients,id_perusahaan',
            'nama_perusahaan' => 'required|string|max:255',
            'email_perusahaan' => 'nullable|email|max:255',
            'nama_kontak_perusahaan' => 'nullable|string|max:255',
            'npwp_perusahaan' => 'nullable|string|max:50',
            'alamat_pengiriman_perusahaan' => 'nullable|string',
            'nomor_telepon_pengiriman' => 'nullable|string|max:50',
            'alamat_faktur_perusahaan' => 'nullable|string',
            'nomor_telepon_faktur' => 'nullable|string|max:50',
            'alamat_efaktur_perusahaan' => 'nullable|string',
            'nomor_rekening_perusahaan' => 'nullable|string|max:100',
        ]);

        $validated['created_by'] = 'System';
        $client = ClientModel::create($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'client' => $client,
            ]);
        }

        return redirect()->back()->with('success', 'Client berhasil ditambahkan.');
    }

    // ─── Quick-add asset/material via modal ──────────────────────────────────
    public function quickAddAsset(Request $request)
    {
        $validated = $request->validate([
            'nama_aset' => 'required|string|max:255',
            'harga' => 'nullable|numeric|min:0',
            'satuan' => 'nullable|string|max:50',
            'stok' => 'nullable|integer|min:0',
            'supplier_from' => 'nullable|string|max:255',
        ]);

        $asset = AsetModel::create($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'asset' => $asset,
            ]);
        }

        return redirect()->back()->with('success', 'Material berhasil ditambahkan.');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────
    private function resolveClientData(array $data): array
    {
        if (!empty($data['client_id'])) {
            $client = ClientModel::find($data['client_id']);
            if ($client) {
                // Only fill if manual fields are empty
                $data['client_name'] = $data['client_name'] ?: ($client->nama_kontak_perusahaan ?: $client->nama_perusahaan);
                $data['client_company'] = $data['client_company'] ?: $client->nama_perusahaan;
                $data['client_email'] = $data['client_email'] ?: $client->email_perusahaan;
                // Concatenate addresses as client_address
                $addressParts = array_filter([
                    $client->alamat_pengiriman_perusahaan,
                    $client->alamat_faktur_perusahaan,
                ]);
                $data['client_address'] = !empty($addressParts) ? implode("\n", $addressParts) : null;
            }
        }
        return $data;
    }

    private function validateQuotation(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'quote_number' => 'required|string|unique:quotations,quote_number' . ($ignoreId ? ",$ignoreId" : ''),
            'nomor_po' => 'nullable|string|max:255',
            'project_name' => 'nullable|string|max:255',
            'client_id' => 'nullable|exists:clients,id',
            'date' => 'required|date',
            'valid_until' => 'required|date|after_or_equal:date',
            'customer_id' => 'nullable|string|max:100',
            'client_name' => 'nullable|string|max:255',
            'client_company' => 'nullable|string|max:255',
            'client_attention' => 'nullable|string|max:255',
            'client_cc' => 'nullable|string|max:255',
            'client_email' => 'nullable|email|max:255',
            'client_address' => 'nullable|string',
            'description_of_work' => 'nullable|string',
            'status' => 'required|in:draft,sent,approved,rejected,expired',
            'term_and_condition' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*.material_name' => 'required_with:items|string|max:255',
            'items.*.unit' => 'required_with:items|string|max:50',
            'items.*.qty' => 'required_with:items|numeric|min:0',
            'items.*.unit_price' => 'required_with:items|numeric|min:0',
            'items.*.description' => 'nullable|string',
            'items.*.materials' => 'nullable|array',
            'items.*.materials.*.asset_id' => 'nullable|exists:assets,id',
            'items.*.materials.*.material_name' => 'required_with:items.*.materials|string|max:255',
            'items.*.materials.*.qty_required' => 'required_with:items.*.materials|numeric|min:0',
            'items.*.materials.*.satuan' => 'required_with:items.*.materials|string|max:50',
            'items.*.materials.*.unit_price' => 'nullable|numeric|min:0',
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

    private function calculateTotals(array $items, array $labors, array $otherCosts = []): array
    {
        // sum material subtotal from items (qty * unit_price)
        $subMat = collect($items)->sum(fn($i) => ($i['qty'] ?? 0) * ($i['unit_price'] ?? 0));

        // also add material sub-materials subtotals
        foreach ($items as $item) {
            $materials = $item['materials'] ?? [];
            foreach ($materials as $m) {
                $subMat += ($m['qty_required'] ?? 0) * ($m['unit_price'] ?? 0);
            }
        }

        $subLab = collect($labors)->sum(fn($l) => ($l['mp'] ?? 0) * ($l['days'] ?? 0) * ($l['rate'] ?? 0));
        $subOth = collect($otherCosts)->sum(fn($c) => ($c['qty'] ?? 0) * ($c['rate'] ?? 0));
        $total = $subMat + $subLab + $subOth;
        return [$subMat, $subLab, $subOth, $total];
    }

    private function syncItems(Quotation $quotation, array $items): void
    {
        $quotation->items()->delete();
        foreach ($items as $i => $item) {
            if (empty($item['material_name']))
                continue;
            $quoteItem = QuotationItem::create([
                'quotation_id' => $quotation->id,
                'sort_order' => $i + 1,
                'material_name' => $item['material_name'],
                'description' => $item['description'] ?? null,
                'unit' => $item['unit'] ?? 'Unit',
                'qty' => $item['qty'],
                'unit_price' => $item['unit_price'],
                'subtotal' => ($item['qty'] ?? 0) * ($item['unit_price'] ?? 0),
            ]);

            // Sync materials under this item
            $materials = $item['materials'] ?? [];
            foreach ($materials as $m => $mat) {
                if (empty($mat['material_name']))
                    continue;
                QuotationItemMaterial::create([
                    'quotation_item_id' => $quoteItem->id,
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

    private function syncLabors(Quotation $quotation, array $labors): void
    {
        $quotation->labors()->delete();
        foreach ($labors as $i => $labor) {
            if (empty($labor['labor_name']))
                continue;
            $sub = ($labor['mp'] ?? 0) * ($labor['days'] ?? 0) * ($labor['rate'] ?? 0);
            QuotationLabor::create([
                'quotation_id' => $quotation->id,
                'sort_order' => $i + 1,
                'labor_name' => $labor['labor_name'],
                'mp' => $labor['mp'],
                'days' => $labor['days'],
                'rate' => $labor['rate'],
                'subtotal' => $sub,
            ]);
        }
    }

    private function syncOtherCosts(Quotation $quotation, array $otherCosts): void
    {
        $quotation->otherCosts()->delete();
        foreach ($otherCosts as $i => $cost) {
            if (empty($cost['cost_name']))
                continue;
            QuotationOtherCost::create([
                'quotation_id' => $quotation->id,
                'sort_order' => $i + 1,
                'cost_name' => $cost['cost_name'],
                'qty' => $cost['qty'] ?? 1,
                'rate' => $cost['rate'] ?? 0,
                'subtotal' => ($cost['qty'] ?? 1) * ($cost['rate'] ?? 0),
            ]);
        }
    }
}
