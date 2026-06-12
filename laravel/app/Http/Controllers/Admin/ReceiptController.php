<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Receipt;
use App\Models\ReceiptOtherCost;
use App\Models\Invoice;
use App\Models\ClientModel;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReceiptController extends Controller
{
    // ─── List ───────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Receipt::latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('receipt_number', 'like', "%$s%")
                    ->orWhere('client_name', 'like', "%$s%")
                    ->orWhere('client_company', 'like', "%$s%")
                    ->orWhere('invoice_number', 'like', "%$s%");
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $receipts = $query->paginate(15)->withQueryString();
        return view('admin.receipts.index', compact('receipts'));
    }

    // ─── Create ─────────────────────────────────────────────────────────────
    public function create()
    {
        $receiptNumber = Receipt::generateReceiptNumber();
        $invoices = Invoice::whereIn('status', ['sent', 'paid', 'overdue'])->latest()->get();
        $clients = ClientModel::all();
        return view('admin.receipts.create', compact('receiptNumber', 'invoices', 'clients'));
    }

    // ─── Store ──────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'receipt_number' => 'required|string|unique:receipts,receipt_number',
            'invoice_id' => 'nullable|exists:invoices,id',
            'invoice_number' => 'nullable|string|max:255',
            'date' => 'required|date',
            'client_name' => 'nullable|string|max:255',
            'client_company' => 'nullable|string|max:255',
            'client_attention' => 'nullable|string|max:255',
            'client_email' => 'nullable|email|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'subtotal_other_cost' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:cash,transfer,cheque,other',
            'payment_reference' => 'nullable|string|max:255',
            'status' => 'required|in:draft,confirmed,cancelled',
            'notes' => 'nullable|string',
            'other_costs' => 'nullable|array',
            'other_costs.*.cost_name' => 'required_with:other_costs|string|max:255',
            'other_costs.*.qty' => 'required_with:other_costs|numeric|min:0',
            'other_costs.*.rate' => 'required_with:other_costs|numeric|min:0',
        ]);

        $receipt = Receipt::create($validated);
        $this->syncOtherCosts($receipt, $request->other_costs ?? []);

        // Auto-update invoice status to paid if fully paid
        if ($receipt->invoice_id && $receipt->status === 'confirmed') {
            $this->checkInvoicePaidStatus($receipt->invoice_id);
        }

        return redirect()->route('admin.receipts.index')
            ->with('success', 'Tanda Terima berhasil dibuat.');
    }

    // ─── PDF ────────────────────────────────────────────────────────────────
    public function pdf(Receipt $receipt)
    {
        $receipt->load('invoice', 'otherCosts');

        $logoPath = public_path('assets/gambar/logo-sti.png');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView('admin.receipts.pdf', compact('receipt', 'logoBase64'))
            ->setPaper('a4', 'portrait')
            ->setOption('defaultFont', 'DejaVu Sans')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', false);

        $filename = 'TandaTerima-' . $receipt->receipt_number . '.pdf';
        return $pdf->stream($filename);
    }

    // ─── Show ───────────────────────────────────────────────────────────────
    public function show(Receipt $receipt)
    {
        $receipt->load('invoice', 'otherCosts');
        return view('admin.receipts.show', compact('receipt'));
    }

    // ─── Edit ───────────────────────────────────────────────────────────────
    public function edit(Receipt $receipt)
    {
        $receiptNumber = $receipt->receipt_number;
        $invoices = Invoice::whereIn('status', ['sent', 'paid', 'overdue'])->latest()->get();
        $clients = ClientModel::all();
        return view('admin.receipts.edit', compact('receipt', 'receiptNumber', 'invoices', 'clients'));
    }

    // ─── Update ─────────────────────────────────────────────────────────────
    public function update(Request $request, Receipt $receipt)
    {
        $validated = $request->validate([
            'receipt_number' => 'required|string|unique:receipts,receipt_number,' . $receipt->id,
            'invoice_id' => 'nullable|exists:invoices,id',
            'invoice_number' => 'nullable|string|max:255',
            'date' => 'required|date',
            'client_name' => 'nullable|string|max:255',
            'client_company' => 'nullable|string|max:255',
            'client_attention' => 'nullable|string|max:255',
            'client_email' => 'nullable|email|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'subtotal_other_cost' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:cash,transfer,cheque,other',
            'payment_reference' => 'nullable|string|max:255',
            'status' => 'required|in:draft,confirmed,cancelled',
            'notes' => 'nullable|string',
            'other_costs' => 'nullable|array',
            'other_costs.*.cost_name' => 'required_with:other_costs|string|max:255',
            'other_costs.*.qty' => 'required_with:other_costs|numeric|min:0',
            'other_costs.*.rate' => 'required_with:other_costs|numeric|min:0',
        ]);

        $receipt->update($validated);
        $this->syncOtherCosts($receipt, $request->other_costs ?? []);

        // Re-check invoice paid status
        if ($receipt->invoice_id) {
            $this->checkInvoicePaidStatus($receipt->invoice_id);
        }

        return redirect()->route('admin.receipts.show', $receipt)
            ->with('success', 'Tanda Terima berhasil diperbarui.');
    }

    // ─── Delete ─────────────────────────────────────────────────────────────
    public function destroy(Receipt $receipt)
    {
        $receipt->delete();
        return redirect()->route('admin.receipts.index')
            ->with('success', 'Tanda Terima berhasil dihapus.');
    }

    // ─── AJAX: Get Invoice Data ─────────────────────────────────────────────
    public function getInvoiceData(Invoice $invoice)
    {
        return response()->json([
            'invoice_number' => $invoice->invoice_number,
            'client_name' => $invoice->client_name,
            'client_company' => $invoice->client_company,
            'client_attention' => $invoice->client_attention,
            'client_email' => $invoice->client_email,
            'total' => $invoice->total,
            'paid_amount' => $invoice->receipts()->where('status', 'confirmed')->sum('amount'),
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
        ]);
    }

    // ─── Helpers ────────────────────────────────────────────────────────────
    private function checkInvoicePaidStatus(int $invoiceId): void
    {
        $invoice = Invoice::find($invoiceId);
        if (!$invoice)
            return;

        $totalPaid = $invoice->receipts()->where('status', 'confirmed')->sum('amount');
        if ($totalPaid >= $invoice->total) {
            $invoice->update(['status' => 'paid']);
        }
    }

    private function syncOtherCosts(Receipt $receipt, array $otherCosts): void
    {
        $receipt->otherCosts()->delete();
        foreach ($otherCosts as $i => $cost) {
            if (empty($cost['cost_name']))
                continue;
            ReceiptOtherCost::create([
                'receipt_id' => $receipt->id,
                'sort_order' => $i + 1,
                'cost_name' => $cost['cost_name'],
                'qty' => $cost['qty'] ?? 1,
                'rate' => $cost['rate'] ?? 0,
                'subtotal' => ($cost['qty'] ?? 1) * ($cost['rate'] ?? 0),
            ]);
        }
    }
}
