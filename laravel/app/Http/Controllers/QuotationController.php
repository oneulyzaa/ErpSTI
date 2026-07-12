<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\QuotationItemMaterial;
use App\Models\QuotationLabor;
use App\Models\QuotationOtherCost;
use App\Models\ClientModel;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class QuotationController extends Controller
{
    // ─── Default labor list ───────────────────────────────────────────────────
    private array $defaultLabors = [
        ['nama_labor' => 'Mechanical Design', 'jumlah_sdm' => 1, 'jumlah_hari' => 1, 'rate_hari' => 1500000],
        ['nama_labor' => 'Electrical Design', 'jumlah_sdm' => 1, 'jumlah_hari' => 1, 'rate_hari' => 1500000],
        ['nama_labor' => 'Assembling', 'jumlah_sdm' => 2, 'jumlah_hari' => 1, 'rate_hari' => 1000000],
        ['nama_labor' => 'Wiring', 'jumlah_sdm' => 2, 'jumlah_hari' => 1, 'rate_hari' => 1000000],
        ['nama_labor' => 'Commissioning', 'jumlah_sdm' => 3, 'jumlah_hari' => 1, 'rate_hari' => 1000000],
        ['nama_labor' => 'Programming', 'jumlah_sdm' => 1, 'jumlah_hari' => 1, 'rate_hari' => 1000000],
        ['nama_labor' => 'Setting & Trainhouse', 'jumlah_sdm' => 2, 'jumlah_hari' => 1, 'rate_hari' => 1000000],
        ['nama_labor' => 'Installation', 'jumlah_sdm' => 4, 'jumlah_hari' => 1, 'rate_hari' => 1500000],
        ['nama_labor' => 'Setting & Trainonsite', 'jumlah_sdm' => 2, 'jumlah_hari' => 1, 'rate_hari' => 1000000],
        ['nama_labor' => 'Accomodation', 'jumlah_sdm' => 1, 'jumlah_hari' => 1, 'rate_hari' => 0],
    ];

    // ─── List ────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Quotation::with('items', 'labors', 'client')->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nomor_quotation', 'like', "%$s%")
                    ->orWhere('nama_project', 'like', "%$s%");
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
        $materials = Material::orderBy('nama_material', 'ASC')->get();
        return view('admin.quotations.create', compact('quoteNumber', 'defaultLabors', 'clients', 'materials'));
    }

    // ─── Store ────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $this->validateQuotation($request);

        // return response()->json($request->all());
        DB::transaction(function () use ($validated, $request) {
            $diskon = (float) ($validated['diskon'] ?? 0);
            $pajak  = (float) ($validated['pajak'] ?? 0);

            [$subMat, $subLab, $subOth, $subtotal, $grandtotal] = $this->calculateTotals(
                $request->items ?? [],
                $request->labors ?? [],
                $request->other_costs ?? [],
                $diskon,
                $pajak
            );

            $quotation = Quotation::create([
                'nomor_quotation'    => $validated['nomor_quotation'],
                'id_staff'           => auth()->id() ?? 1,
                'id_client'          => $validated['id_client'] ?? null,
                'nama_project'       => $validated['nama_project'] ?? null,
                'tanggal_pembuatan'  => $validated['tanggal_pembuatan'],
                'valid_sampai'       => $validated['valid_sampai'],
                'subtotal_produksi'  => 0,
                'subtotal_material'  => $subMat,
                'subtotal_labor'     => $subLab,
                'subtotal_lainlain'  => $subOth,
                'grandtotal'         => $grandtotal,
                'diskon'             => $diskon,
                'pajak'              => $pajak,
                'termin'             => $validated['termin'] ?? null,
                'keterangan'         => $validated['keterangan'] ?? null,
                'status'             => $validated['status'],
            ]);

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
        $quotation->load('items.materials.material', 'labors', 'otherCosts', 'client');
        return view('admin.quotations.show', compact('quotation'));
    }

    // ─── Edit ─────────────────────────────────────────────────────────────────
    public function edit(Quotation $quotation)
    {
        $quotation->load('items.materials', 'labors', 'otherCosts');
        $quoteNumber = $quotation->nomor_quotation;
        $defaultLabors = $this->defaultLabors;
        $clients = ClientModel::all();
        $materials = Material::all();
        return view('admin.quotations.edit', compact('quotation', 'quoteNumber', 'defaultLabors', 'clients', 'materials'));
    }

    // ─── Update ───────────────────────────────────────────────────────────────
    public function update(Request $request, Quotation $quotation)
    {
        $validated = $this->validateQuotation($request, $quotation->nomor_quotation);

        DB::transaction(function () use ($validated, $request, $quotation) {
            $diskon = (float) ($validated['diskon'] ?? 0);
            $pajak  = (float) ($validated['pajak'] ?? 0);

            [$subMat, $subLab, $subOth, $subtotal, $grandtotal] = $this->calculateTotals(
                $request->items ?? [],
                $request->labors ?? [],
                $request->other_costs ?? [],
                $diskon,
                $pajak
            );

            $quotation->update([
                'id_client'          => $validated['id_client'] ?? null,
                'nama_project'       => $validated['nama_project'] ?? null,
                'tanggal_pembuatan'  => $validated['tanggal_pembuatan'],
                'valid_sampai'       => $validated['valid_sampai'],
                'subtotal_material'  => $subMat,
                'subtotal_labor'     => $subLab,
                'subtotal_lainlain'  => $subOth,
                'grandtotal'         => $grandtotal,
                'diskon'             => $diskon,
                'pajak'              => $pajak,
                'termin'             => $validated['termin'] ?? null,
                'keterangan'         => $validated['keterangan'] ?? null,
                'status'             => $validated['status'],
            ]);

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
        $quotation->load('items.materials.material', 'labors', 'otherCosts', 'client');

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

        $filename = 'ProjectQuote-' . $quotation->nomor_quotation . '.pdf';
        return $pdf->stream($filename);
    }

    // ─── Quick-add client via modal ──────────────────────────────────────────
    public function quickAddClient(Request $request)
    {
        $validated = $request->validate([
            'id_customer' => 'required|string|unique:customers,id_customer',
            'nama_perusahaan' => 'required|string|max:255',
            'email_perusahaan' => 'nullable|email|max:255',
            'nama_kontak' => 'nullable|string|max:255',
            'npwp_perusahaan' => 'nullable|string|max:50',
            'alamat_perusahaan' => 'nullable|string',
            'telepon_faktur' => 'nullable|string|max:50',
            'rekening_perusahaan' => 'nullable|string|max:100',
        ]);

        $client = ClientModel::create($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'client' => $client,
            ]);
        }

        return redirect()->back()->with('success', 'Client berhasil ditambahkan.');
    }

    // ─── Quick-add material via modal ────────────────────────────────────────
    public function quickAddMaterial(Request $request)
    {
        $validated = $request->validate([
            'nama_material'   => 'required|string|max:255',
            'harga_material'  => 'nullable|numeric|min:0',
            'satuan'          => 'nullable|string|max:50',
            'stok'            => 'nullable|integer|min:0',
            'supplier'        => 'nullable|string|max:255',
        ]);
        $validated['status_material'] = 'Tersedia'; // Set default status to "Tersedia"

        $material = Material::create($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'material' => $material,
            ]);
        }

        return redirect()->back()->with('success', 'Material berhasil ditambahkan.');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────
    private function validateQuotation(Request $request, ?string $ignoreId = null): array
    {
        return $request->validate([
            'nomor_quotation'    => 'required|string|max:50|unique:quotations,nomor_quotation' . ($ignoreId ? ",$ignoreId" : ''),
            'id_client'          => 'nullable|integer|exists:customers,id',
            'nama_project'       => 'nullable|string|max:255',
            'tanggal_pembuatan'  => 'required|date',
            'valid_sampai'       => 'required|date|after_or_equal:tanggal_pembuatan',
            'diskon'             => 'nullable|numeric|min:0',
            'pajak'              => 'nullable|numeric|min:0',
            'status'             => 'required|in:draft,sent,approved,rejected,expired',
            'termin'             => 'nullable|string',
            'keterangan'         => 'nullable|string',
            'items'              => 'nullable|array',
            'items.*.nama_item'       => 'required_with:items|string|max:255',
            'items.*.deskripsi_item'  => 'nullable|string',
            'items.*.satuan'          => 'required_with:items|string|max:50',
            'items.*.jumlah_item'     => 'required_with:items|numeric|min:0',
            'items.*.harga_item'      => 'required_with:items|numeric|min:0',
            'items.*.materials'       => 'nullable|array',
            'items.*.materials.*.id_material'       => 'nullable|exists:materials,id_material',
            'items.*.materials.*.nama_material'     => 'required_with:items.*.materials|string|max:255',
            'items.*.materials.*.jumlah_material'   => 'required_with:items.*.materials|numeric|min:0',
            'items.*.materials.*.satuan_material'   => 'required_with:items.*.materials|string|max:50',
            'items.*.materials.*.harga_material'    => 'nullable|numeric|min:0',
            'labors'             => 'nullable|array',
            'labors.*.nama_labor'     => 'required_with:labors|string|max:255',
            'labors.*.jumlah_sdm'     => 'required_with:labors|integer|min:0',
            'labors.*.jumlah_hari'    => 'required_with:labors|numeric|min:0',
            'labors.*.rate_hari'      => 'required_with:labors|numeric|min:0',
            'other_costs'        => 'nullable|array',
            'other_costs.*.nama_biaya'   => 'required_with:other_costs|string|max:255',
            'other_costs.*.qty'          => 'required_with:other_costs|numeric|min:0',
            'other_costs.*.rate'         => 'required_with:other_costs|numeric|min:0',
        ]);
    }

    private function calculateTotals(array $items, array $labors, array $otherCosts = [], float $diskon = 0, float $pajak = 0): array
    {
        // sum material subtotal from items (jumlah_item * harga_item)
        $subMat = collect($items)->sum(fn($i) => ($i['jumlah_item'] ?? 0) * ($i['harga_item'] ?? 0));

        // also add sub-materials subtotals
        foreach ($items as $item) {
            $materials = $item['materials'] ?? [];
            foreach ($materials as $m) {
                $subMat += ($m['jumlah_material'] ?? 0) * ($m['harga_material'] ?? 0);
            }
        }

        $subLab = collect($labors)->sum(fn($l) => ($l['jumlah_sdm'] ?? 0) * ($l['jumlah_hari'] ?? 0) * ($l['rate_hari'] ?? 0));
        // other_costs from form: qty * rate
        $subOth = collect($otherCosts)->sum(fn($c) => ($c['qty'] ?? 0) * ($c['rate'] ?? 0));
        $subtotal = $subMat + $subLab + $subOth - $diskon;
        $grandtotal = $subtotal + $pajak;
        return [$subMat, $subLab, $subOth, $subtotal, $grandtotal];
    }

    private function syncItems(Quotation $quotation, array $items): void
    {
        $quotation->items()->delete();
        foreach ($items as $i => $item) {
            if (empty($item['nama_item']))
                continue;
            $quoteItem = QuotationItem::create([
                'nomor_quotation' => $quotation->nomor_quotation,
                'nama_item'       => $item['nama_item'],
                'deskripsi_item'  => $item['deskripsi_item'] ?? null,
                'satuan'          => $item['satuan'] ?? 'Unit',
                'jumlah_item'     => $item['jumlah_item'],
                'harga_item'      => $item['harga_item'],
            ]);

            // Sync materials under this item
            $materials = $item['materials'] ?? [];
            foreach ($materials as $m => $mat) {
                if (empty($mat['nama_material']))
                    continue;
                QuotationItemMaterial::create([
                    'id_item'          => $quoteItem->id_item,
                    'id_material'      => $mat['id_material'] ?? null,
                    'nama_material'    => $mat['nama_material'],
                    'jumlah_material'  => $mat['jumlah_material'] ?? 0,
                    'satuan_material'  => $mat['satuan_material'] ?? 'pcs',
                    'harga_material'   => $mat['harga_material'] ?? 0,
                ]);
            }
        }
    }

    private function syncLabors(Quotation $quotation, array $labors): void
    {
        $quotation->labors()->delete();
        foreach ($labors as $i => $labor) {
            if (empty($labor['nama_labor']))
                continue;
            QuotationLabor::create([
                'nomor_quotation' => $quotation->nomor_quotation,
                'nama_labor'      => $labor['nama_labor'],
                'jumlah_sdm'      => $labor['jumlah_sdm'],
                'jumlah_hari'     => $labor['jumlah_hari'],
                'rate_hari'       => $labor['rate_hari'],
            ]);
        }
    }

    private function syncOtherCosts(Quotation $quotation, array $otherCosts): void
    {
        $quotation->otherCosts()->delete();
        foreach ($otherCosts as $i => $cost) {
            if (empty($cost['nama_biaya']))
                continue;
            $qty = $cost['qty'] ?? 0;
            $rate = $cost['rate'] ?? 0;
            QuotationOtherCost::create([
                'nomor_quotation' => $quotation->nomor_quotation,
                'nama_biaya'      => $cost['nama_biaya'],
                'jumlah_biaya'    => $qty * $rate,
            ]);
        }
    }
}