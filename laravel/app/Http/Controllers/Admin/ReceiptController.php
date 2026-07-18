<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Receipt;
use App\Models\Invoice;
use App\Models\ClientModel;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReceiptController extends Controller
{
    public function index(Request $request)
    {
        // Load relasi invoice -> salesOrder -> client untuk mendapatkan data perusahaan
        $query = Receipt::with('invoice.salesOrder.client')->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nomor_receipt', 'like', "%$s%")
                    ->orWhere('nama_project', 'like', "%$s%")
                    ->orWhere('nomor_invoice', 'like', "%$s%")
                    ->orWhere('nomor_po', 'like', "%$s%");
            });
        }

        $receipts = $query->paginate(15)->withQueryString();
        return view('admin.receipts.index', compact('receipts'));
    }

    public function create()
    {
        $receiptNumber = Receipt::generateReceiptNumber();
        $invoices = Invoice::whereIn('status_pembayaran', ['sent', 'paid', 'overdue'])->latest()->get();
        $clients = ClientModel::all();
        return view('admin.receipts.create', compact('receiptNumber', 'invoices', 'clients'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nomor_receipt' => 'required|string|unique:receipts,nomor_receipt',
                'nomor_invoice' => 'required|exists:invoices,nomor_invoice',
                'nomor_po' => 'nullable|string|max:255',
                'nama_project' => 'nullable|string|max:255',
                'tanggal_bayar' => 'required|date',
                'metode_bayar' => 'required|in:cash,transfer,cheque,other',
                'jumlah_bayar' => 'required|numeric|min:0',
                'keterangan' => 'nullable|string',
            ]);

            Receipt::create($validated);

            return redirect()->route('admin.receipts.index')
                ->with('success', 'Tanda Terima berhasil dibuat.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error creating receipt: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan Tanda Terima: ' . $e->getMessage());
        }
    }

    public function pdf(Receipt $receipt)
    {
        $receipt->load('invoice.salesOrder.client');

        $logoPath = public_path('assets/gambar/logo-sti.png');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView('admin.receipts.pdf', compact('receipt', 'logoBase64'))
            ->setPaper('a5', 'landscape')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', false);

        $filename = 'TandaTerima-' . $receipt->nomor_receipt . '.pdf';
        return $pdf->stream($filename);
    }

    public function show(Receipt $receipt)
    {
        $receipt->load('invoice.salesOrder.client');
        return view('admin.receipts.show', compact('receipt'));
    }

    public function edit(Receipt $receipt)
    {
        $receiptNumber = $receipt->nomor_receipt;
        $invoices = Invoice::latest()->get();
        return view('admin.receipts.edit', compact('receipt', 'receiptNumber', 'invoices'));
    }

    public function update(Request $request, Receipt $receipt)
    {
        try {
            $validated = $request->validate([
                'nomor_receipt' => 'required|string|unique:receipts,nomor_receipt,' . $receipt->nomor_receipt . ',nomor_receipt',
                'nomor_invoice' => 'required|exists:invoices,nomor_invoice',
                'nomor_po' => 'nullable|string|max:255',
                'nama_project' => 'nullable|string|max:255',
                'tanggal_bayar' => 'required|date',
                'metode_bayar' => 'required|in:cash,transfer,cheque,other',
                'jumlah_bayar' => 'required|numeric|min:0',
                'keterangan' => 'nullable|string',
            ]);

            $receipt->update($validated);

            return redirect()->route('admin.receipts.show', $receipt)
                ->with('success', 'Tanda Terima berhasil diperbarui.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error updating receipt: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui Tanda Terima: ' . $e->getMessage());
        }
    }

    public function destroy(Receipt $receipt)
    {
        $receipt->delete();
        return redirect()->route('admin.receipts.index')
            ->with('success', 'Tanda Terima berhasil dihapus.');
    }

    public function getInvoiceData(Invoice $invoice)
    {
        $invoice->load('salesOrder.client');
        $client = $invoice->salesOrder->client ?? null;

        return response()->json([
            'nomor_invoice' => $invoice->nomor_invoice,
            'nomor_po' => $invoice->nomor_po ?? $invoice->salesOrder->nomor_po ?? '',
            'nama_project' => $invoice->nama_project ?? $invoice->salesOrder->nama_project ?? '',
            'client_name' => $client->nama_kontak ?? '',
            'client_company' => $client->nama_perusahaan ?? '',
            'client_email' => $client->email_perusahaan ?? '',
            'total' => $invoice->grandtotal,
            'paid_amount' => $invoice->receipts()->sum('jumlah_bayar'),
        ]);
    }

    public function getClientData(ClientModel $client)
    {
        return response()->json([
            'id' => $client->id,
            'nama_perusahaan' => $client->nama_perusahaan,
            'nama_kontak' => $client->nama_kontak_perusahaan,
            'email' => $client->email_perusahaan,
        ]);
    }

    // Method removed - invoice paid status is calculated dynamically
}