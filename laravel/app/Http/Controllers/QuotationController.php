<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\QuotationItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuotationController extends Controller
{
    // ─── List ───────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Quotation::with('items')->latest();

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

    // ─── Create Form ─────────────────────────────────────────────────────────
    public function create()
    {
        $quoteNumber = Quotation::generateQuoteNumber();
        return view('admin.quotations.create', compact('quoteNumber'));
    }

    // ─── Store ────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'quote_number'       => 'required|string|unique:quotations,quote_number',
            'date'               => 'required|date',
            'valid_until'        => 'required|date|after_or_equal:date',
            'customer_id'        => 'nullable|string|max:100',
            'client_name'        => 'required|string|max:255',
            'client_company'     => 'required|string|max:255',
            'client_attention'   => 'nullable|string|max:255',
            'client_cc'          => 'nullable|string|max:255',
            'client_email'       => 'nullable|email|max:255',
            'description_of_work'=> 'nullable|string',
            'tax_percentage'     => 'required|numeric|min:0|max:100',
            'status'             => 'required|in:draft,sent,approved,rejected,expired',
            'notes'              => 'nullable|string',
            'items'              => 'required|array|min:1',
            'items.*.material_name' => 'required|string|max:255',
            'items.*.description'   => 'nullable|string',
            'items.*.unit'          => 'required|string|max:50',
            'items.*.qty'           => 'required|numeric|min:0',
            'items.*.unit_price'    => 'required|numeric|min:0',
        ], [
            'items.required'            => 'Minimal 1 item harus diisi.',
            'valid_until.after_or_equal'=> 'Tanggal berlaku harus setelah tanggal quotation.',
            'items.*.material_name.required' => 'Nama material wajib diisi.',
            'items.*.qty.required'      => 'Jumlah wajib diisi.',
            'items.*.unit_price.required' => 'Harga satuan wajib diisi.',
        ]);

        DB::transaction(function () use ($validated, $request) {
            [$subtotal, $taxAmount, $total] = $this->calculateTotals(
                $request->items, $validated['tax_percentage']
            );

            $quotation = Quotation::create(array_merge($validated, [
                'subtotal'   => $subtotal,
                'tax_amount' => $taxAmount,
                'total'      => $total,
            ]));

            $this->syncItems($quotation, $request->items);
        });

        return redirect()->route('admin.quotations.index')
            ->with('success', 'Quotation berhasil dibuat.');
    }

    // ─── Show ─────────────────────────────────────────────────────────────────
    public function show(Quotation $quotation)
    {
        $quotation->load('items');
        return view('admin.quotations.show', compact('quotation'));
    }

    // ─── Edit Form ────────────────────────────────────────────────────────────
    public function edit(Quotation $quotation)
    {
        $quotation->load('items');
        return view('admin.quotations.edit', compact('quotation'));
    }

    // ─── Update ───────────────────────────────────────────────────────────────
    public function update(Request $request, Quotation $quotation)
    {
        $validated = $request->validate([
            'quote_number'       => 'required|string|unique:quotations,quote_number,' . $quotation->id,
            'date'               => 'required|date',
            'valid_until'        => 'required|date|after_or_equal:date',
            'customer_id'        => 'nullable|string|max:100',
            'client_name'        => 'required|string|max:255',
            'client_company'     => 'required|string|max:255',
            'client_attention'   => 'nullable|string|max:255',
            'client_cc'          => 'nullable|string|max:255',
            'client_email'       => 'nullable|email|max:255',
            'description_of_work'=> 'nullable|string',
            'tax_percentage'     => 'required|numeric|min:0|max:100',
            'status'             => 'required|in:draft,sent,approved,rejected,expired',
            'notes'              => 'nullable|string',
            'items'              => 'required|array|min:1',
            'items.*.material_name' => 'required|string|max:255',
            'items.*.description'   => 'nullable|string',
            'items.*.unit'          => 'required|string|max:50',
            'items.*.qty'           => 'required|numeric|min:0',
            'items.*.unit_price'    => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated, $request, $quotation) {
            [$subtotal, $taxAmount, $total] = $this->calculateTotals(
                $request->items, $validated['tax_percentage']
            );

            $quotation->update(array_merge($validated, [
                'subtotal'   => $subtotal,
                'tax_amount' => $taxAmount,
                'total'      => $total,
            ]));

            $this->syncItems($quotation, $request->items);
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

    // ─── Helpers ──────────────────────────────────────────────────────────────
    private function calculateTotals(array $items, float $taxPct): array
    {
        $subtotal  = collect($items)->sum(fn($i) => ($i['qty'] ?? 0) * ($i['unit_price'] ?? 0));
        $taxAmount = $subtotal * ($taxPct / 100);
        $total     = $subtotal + $taxAmount;

        return [$subtotal, $taxAmount, $total];
    }

    private function syncItems(Quotation $quotation, array $items): void
    {
        $quotation->items()->delete();

        foreach ($items as $i => $item) {
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
}
