<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\QuotationLabor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class QuotationController extends Controller
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
        $query = Quotation::with('items', 'labors')->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('quote_number', 'like', "%$s%")
                  ->orWhere('client_name', 'like', "%$s%")
                  ->orWhere('client_company', 'like', "%$s%");
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
        $quoteNumber   = Quotation::generateQuoteNumber();
        $defaultLabors = $this->defaultLabors;
        return view('admin.quotations.create', compact('quoteNumber', 'defaultLabors'));
    }

    // ─── Store ────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $this->validateQuotation($request);

        DB::transaction(function () use ($validated, $request) {
            [$subMat, $subLab, $subtotal, $taxAmount, $total] = $this->calculateTotals(
                $request->items ?? [], $request->labors ?? [], $validated['tax_percentage']
            );

            $quotation = Quotation::create(array_merge($validated, [
                'subtotal_material' => $subMat,
                'subtotal_labor'    => $subLab,
                'subtotal'          => $subtotal,
                'tax_amount'        => $taxAmount,
                'total'             => $total,
            ]));

            $this->syncItems($quotation, $request->items ?? []);
            $this->syncLabors($quotation, $request->labors ?? []);
        });

        return redirect()->route('admin.quotations.index')
            ->with('success', 'Quotation berhasil dibuat.');
    }

    // ─── Show ─────────────────────────────────────────────────────────────────
    public function show(Quotation $quotation)
    {
        $quotation->load('items', 'labors');
        return view('admin.quotations.show', compact('quotation'));
    }

    // ─── Edit ─────────────────────────────────────────────────────────────────
    public function edit(Quotation $quotation)
    {
        $quotation->load('items', 'labors');
        $quoteNumber   = $quotation->quote_number;
        $defaultLabors = $this->defaultLabors;
        return view('admin.quotations.edit', compact('quotation', 'quoteNumber', 'defaultLabors'));
    }

    // ─── Update ───────────────────────────────────────────────────────────────
    public function update(Request $request, Quotation $quotation)
    {
        $validated = $this->validateQuotation($request, $quotation->id);

        DB::transaction(function () use ($validated, $request, $quotation) {
            [$subMat, $subLab, $subtotal, $taxAmount, $total] = $this->calculateTotals(
                $request->items ?? [], $request->labors ?? [], $validated['tax_percentage']
            );

            $quotation->update(array_merge($validated, [
                'subtotal_material' => $subMat,
                'subtotal_labor'    => $subLab,
                'subtotal'          => $subtotal,
                'tax_amount'        => $taxAmount,
                'total'             => $total,
            ]));

            $this->syncItems($quotation, $request->items ?? []);
            $this->syncLabors($quotation, $request->labors ?? []);
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
        $quotation->load('items', 'labors');

        // Base64 encode logo agar bisa dipakai di DomPDF (tidak butuh remote)
        $logoPath = public_path('assets/gambar/logo-sti.png');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView('admin.quotations.pdf', compact('quotation', 'logoBase64'))
            ->setPaper('a4', 'portrait')
            ->setOption('defaultFont', 'DejaVu Sans')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', false);

        $filename = 'ProjectQuote-' . $quotation->quote_number . '.pdf';
        return $pdf->stream($filename);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────
    private function validateQuotation(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'quote_number'        => 'required|string|unique:quotations,quote_number' . ($ignoreId ? ",$ignoreId" : ''),
            'date'                => 'required|date',
            'valid_until'         => 'required|date|after_or_equal:date',
            'customer_id'         => 'nullable|string|max:100',
            'client_name'         => 'required|string|max:255',
            'client_company'      => 'required|string|max:255',
            'client_attention'    => 'nullable|string|max:255',
            'client_cc'           => 'nullable|string|max:255',
            'client_email'        => 'nullable|email|max:255',
            'description_of_work' => 'nullable|string',
            'tax_percentage'      => 'required|numeric|min:0|max:100',
            'status'              => 'required|in:draft,sent,approved,rejected,expired',
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

    private function syncItems(Quotation $quotation, array $items): void
    {
        $quotation->items()->delete();
        foreach ($items as $i => $item) {
            if (empty($item['material_name'])) continue;
            QuotationItem::create([
                'quotation_id'  => $quotation->id,
                'sort_order'    => $i + 1,
                'material_name' => $item['material_name'],
                'description'   => $item['description'] ?? null,
                'unit'          => $item['unit'] ?? 'Unit',
                'qty'           => $item['qty'],
                'unit_price'    => $item['unit_price'],
                'subtotal'      => ($item['qty'] ?? 0) * ($item['unit_price'] ?? 0),
            ]);
        }
    }

    private function syncLabors(Quotation $quotation, array $labors): void
    {
        $quotation->labors()->delete();
        foreach ($labors as $i => $labor) {
            if (empty($labor['labor_name'])) continue;
            $sub = ($labor['mp'] ?? 0) * ($labor['days'] ?? 0) * ($labor['rate'] ?? 0);
            QuotationLabor::create([
                'quotation_id' => $quotation->id,
                'sort_order'   => $i + 1,
                'labor_name'   => $labor['labor_name'],
                'mp'           => $labor['mp'] ?? 1,
                'days'         => $labor['days'] ?? 1,
                'rate'         => $labor['rate'] ?? 0,
                'subtotal'     => $sub,
            ]);
        }
    }
}
