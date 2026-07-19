<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\SalesOrder;
use App\Models\ClientModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    // ─── List ───────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Invoice::with('salesOrder.client')->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nomor_invoice', 'like', "%$s%")
                    ->orWhere('nama_project', 'like', "%$s%")
                    ->orWhere('nomor_salesorder', 'like', "%$s%");
            });
        }
        if ($request->filled('status')) {
            $query->where('status_pembayaran', $request->status);
        }

        $invoices = $query->paginate(15)->withQueryString();
        return view('admin.invoices.index', compact('invoices'));
    }

    // ─── Create ─────────────────────────────────────────────────────────────
    public function create()
    {
        $invoiceNumber = Invoice::generateInvoiceNumber();
        $salesOrders = SalesOrder::whereIn('status', ['confirmed', 'in_progress', 'completed'])->latest()->get();
        $clients = ClientModel::all();
        return view('admin.invoices.create', compact('invoiceNumber', 'salesOrders', 'clients'));
    }

    // ─── Store ──────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nomor_invoice' => 'required|string|unique:invoices,nomor_invoice',
                'nomor_salesorder' => 'required|exists:sales_orders,nomor_salesorder',
                'nama_project' => 'nullable|string|max:255',
                'referensi_po' => 'nullable|string|max:255',
                'tanggal_invoice' => 'required|date',
                'jatuh_tempo' => 'nullable|date|after_or_equal:tanggal_invoice',
                'subtotal_labor' => 'nullable|numeric|min:0',
                'subtotal_lainlain' => 'nullable|numeric|min:0',
                'discount' => 'nullable|numeric|min:0',
                'tax_percentage' => 'required|numeric|min:0|max:100',
                'status_pembayaran' => 'required|in:draft,sent,paid,overdue,cancelled',
                'keterangan' => 'nullable|string',
                'items' => 'required|array|min:1',
            ]);

            // --- Hitung ulang subtotal_produksi & subtotal_material dari items (server-side) ---
            $totalMaterial = 0;
            $totalProduksi = 0;

            foreach ($request->items as $item) {
                $subtotalMaterialItem = 0;

                if (!empty($item['materials'])) {
                    foreach ($item['materials'] as $material) {
                        $subtotalMaterialItem += (float) $material['qty_required'] * (float) $material['unit_price'];
                    }
                }

                $subtotalProduksiItem = (float) $item['qty'] * (float) $item['unit_price'];

                $totalProduksi += $subtotalProduksiItem;
                $totalMaterial += $subtotalMaterialItem;
            }

            $validated['subtotal_produksi'] = $totalProduksi;
            $validated['subtotal_material'] = $totalMaterial;
            $validated['diskon'] = $validated['discount'] ?? 0;

            DB::transaction(function () use ($validated, $totalProduksi, $totalMaterial) {
                $calculated = $this->calculateAmounts($validated);

                Invoice::create([
                    'nomor_invoice' => $validated['nomor_invoice'],
                    'nomor_salesorder' => $validated['nomor_salesorder'],
                    'nama_project' => $validated['nama_project'],
                    'referensi_po' => $validated['referensi_po'],
                    'tanggal_invoice' => $validated['tanggal_invoice'],
                    'jatuh_tempo' => $validated['jatuh_tempo'],
                    'subtotal_produksi' => $totalProduksi,
                    'subtotal_material' => $totalMaterial,
                    'subtotal_labor' => $validated['subtotal_labor'],
                    'subtotal_lainlain' => $validated['subtotal_lainlain'],
                    'diskon' => $validated['diskon'],
                    'pajak' => $calculated['pajak'],
                    'grandtotal' => $calculated['grandtotal'],
                    'status_pembayaran' => $validated['status_pembayaran'],
                    'keterangan' => $validated['keterangan'] ?? null,
                ]);
            });

            return redirect()->route('admin.invoices.index')
                ->with('success', 'Invoice berhasil dibuat.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            Log::error('Error creating invoice: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan invoice: ' . $e->getMessage());
        }
    }

    // ─── PDF ────────────────────────────────────────────────────────────────
    public function pdf(Invoice $invoice)
    {
        // Tidak perlu load relasi items/labors/otherCosts karena tidak disimpan

        $logoPath = public_path('assets/gambar/logo-sti.png');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

        // return view('admin.invoices.pdf-c', compact('invoice', 'logoBase64'));
        $pdf = Pdf::loadView('admin.invoices.pdf-c', compact('invoice', 'logoBase64'))
            ->setPaper('a5', 'landscape')
            ->setOption('defaultFont', 'DejaVu Sans')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', false);

        $filename = 'Invoice-' . $invoice->nomor_invoice . '.pdf';
        return $pdf->stream($filename);
    }

    // ─── Show ───────────────────────────────────────────────────────────────
    public function show(Invoice $invoice)
    {
        $invoice->load('salesOrder');
        return view('admin.invoices.show', compact('invoice'));
    }

    // ─── Edit ───────────────────────────────────────────────────────────────
    public function edit(Invoice $invoice)
    {
        // Load relasi dari SalesOrder untuk populate form edit
        $invoice->load('salesOrder.client', 'salesOrder.items.materials', 'salesOrder.labors', 'salesOrder.otherCosts');
        $invoiceNumber = $invoice->nomor_invoice;
        $salesOrders = SalesOrder::whereIn('status', ['confirmed', 'in_progress', 'completed'])->latest()->get();

        // Map data items dari SalesOrder untuk form edit
        $oldItems = $invoice->salesOrder->items->map(function ($item) {
            return [
                'item_name' => $item->nama_item,
                'description' => $item->deskripsi_item,
                'unit' => $item->satuan,
                'qty' => $item->jumlah_item,
                'unit_price' => $item->harga_item,
                'materials' => $item->materials->map(function ($mat) {
                    return [
                        'asset_id' => $mat->id_material ?? '',
                        'material_name' => $mat->nama_material,
                        'qty_required' => $mat->jumlah_material,
                        'satuan' => $mat->satuan_material,
                        'unit_price' => $mat->harga_material,
                    ];
                })->toArray(),
            ];
        })->toArray();

        // Map data labors dari SalesOrder
        $oldLabors = $invoice->salesOrder->labors->map(function ($labor) {
            return [
                'labor_name' => $labor->nama_labor,
                'mp' => $labor->jumlah_sdm ?? 1,
                'days' => $labor->jumlah_hari ?? 0,
                'rate' => $labor->rate_hari ?? 0,
                'subtotal' => ($labor->jumlah_sdm ?? 1) * ($labor->jumlah_hari ?? 0) * ($labor->rate_hari ?? 0),
            ];
        })->toArray();

        // Map data other costs dari SalesOrder
        $oldOtherCosts = $invoice->salesOrder->otherCosts->map(function ($cost) {
            return [
                'cost_name' => $cost->nama_biaya,
                'qty' => 1,
                'rate' => $cost->jumlah_biaya,
            ];
        })->toArray();

        return view('admin.invoices.edit', compact('invoice', 'invoiceNumber', 'salesOrders', 'oldItems', 'oldLabors', 'oldOtherCosts'));
    }

    // ─── Update ─────────────────────────────────────────────────────────────
    public function update(Request $request, Invoice $invoice)
    {
        try {
            $validated = $request->validate([
                'nomor_invoice' => 'required|string|unique:invoices,nomor_invoice,' . $invoice->nomor_invoice . ',nomor_invoice',
                'nomor_salesorder' => 'required|exists:sales_orders,nomor_salesorder',
                'nama_project' => 'nullable|string|max:255',
                'referensi_po' => 'nullable|string|max:255',
                'tanggal_invoice' => 'required|date',
                'jatuh_tempo' => 'nullable|date|after_or_equal:tanggal_invoice',
                'subtotal_labor' => 'nullable|numeric|min:0',
                'subtotal_lainlain' => 'nullable|numeric|min:0',
                'discount' => 'nullable|numeric|min:0',
                'tax_percentage' => 'required|numeric|min:0|max:100',
                'status_pembayaran' => 'required|in:draft,sent,paid,overdue,cancelled',
                'keterangan' => 'nullable|string',
                'items' => 'required|array|min:1',
            ]);

            // --- Hitung ulang subtotal_produksi & subtotal_material dari items (server-side, sama seperti store()) ---
            $totalMaterial = 0;
            $totalProduksi = 0;

            foreach ($request->items as $item) {
                $subtotalMaterialItem = 0;

                if (!empty($item['materials'])) {
                    foreach ($item['materials'] as $material) {
                        $subtotalMaterialItem += (float) $material['qty_required'] * (float) $material['unit_price'];
                    }
                }

                $subtotalProduksiItem = (float) $item['qty'] * (float) $item['unit_price'];

                $totalProduksi += $subtotalProduksiItem;
                $totalMaterial += $subtotalMaterialItem;
            }

            $validated['subtotal_produksi'] = $totalProduksi;
            $validated['subtotal_material'] = $totalMaterial;
            $validated['diskon'] = $validated['discount'] ?? 0;

            DB::transaction(function () use ($validated, $invoice, $totalProduksi, $totalMaterial) {
                $calculated = $this->calculateAmounts($validated);

                $invoice->update([
                    'nomor_invoice' => $validated['nomor_invoice'],
                    'nomor_salesorder' => $validated['nomor_salesorder'],
                    'nama_project' => $validated['nama_project'] ?? null,
                    'referensi_po' => $validated['referensi_po'] ?? null,
                    'tanggal_invoice' => $validated['tanggal_invoice'],
                    'jatuh_tempo' => $validated['jatuh_tempo'] ?? null,
                    'subtotal_produksi' => $totalProduksi,
                    'subtotal_material' => $totalMaterial,
                    'subtotal_labor' => $validated['subtotal_labor'] ?? 0,
                    'subtotal_lainlain' => $validated['subtotal_lainlain'] ?? 0,
                    'diskon' => $validated['diskon'],
                    'pajak' => $calculated['pajak'],
                    'grandtotal' => $calculated['grandtotal'],
                    'status_pembayaran' => $validated['status_pembayaran'],
                    'keterangan' => $validated['keterangan'] ?? null,
                ]);
            });

            return redirect()->route('admin.invoices.show', $invoice)
                ->with('success', 'Invoice berhasil diperbarui.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui Invoice: ' . $e->getMessage())
                ->withInput();
        }
    }

    // ─── Delete ─────────────────────────────────────────────────────────────
    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('admin.invoices.index')
            ->with('success', 'Invoice berhasil dihapus.');
    }

    // ─── AJAX: Get SO Data ─────────────────────────────────────────────────
    public function getSoData(SalesOrder $salesOrder)
    {
        $salesOrder->load('items.materials', 'labors', 'otherCosts', 'client');

        // Get client data from relation
        $client = $salesOrder->client;

        return response()->json([
            'so_number' => $salesOrder->nomor_salesorder,
            'nomor_po' => $salesOrder->nomor_po,
            'project_name' => $salesOrder->nama_project,
            'client_name' => $client->nama_kontak ?? '',
            'client_company' => $client->nama_perusahaan ?? '',
            'client_attention' => '',
            'client_cc' => '',
            'client_email' => $client->email_perusahaan ?? '',
            'client_address' => $client->alamat_perusahaan ?? '',
            'description' => $salesOrder->keterangan ?? '',
            'subtotal' => $salesOrder->subtotal_produksi,
            'subtotal_material' => $salesOrder->subtotal_material,
            'subtotal_labor' => $salesOrder->subtotal_labor,
            'subtotal_other_cost' => $salesOrder->subtotal_lainlain,
            'tax_percentage' => $salesOrder->pajak ?? 11,
            'tax_amount' => 0,
            'discount' => $salesOrder->diskon ?? 0,
            'total' => $salesOrder->grandtotal,
            'items' => $salesOrder->items->map(function ($item) {
                return [
                    'item_name' => $item->nama_item,
                    'description' => $item->deskripsi_item,
                    'unit' => $item->satuan,
                    'qty' => $item->jumlah_item,
                    'unit_price' => $item->harga_item,
                    'subtotal' => $item->jumlah_item * $item->harga_item,
                    'materials' => $item->materials->map(function ($mat) {
                        return [
                            'asset_id' => $mat->id_material ?? '',
                            'material_name' => $mat->nama_material,
                            'qty_required' => $mat->jumlah_material,
                            'satuan' => $mat->satuan_material,
                            'unit_price' => $mat->harga_material,
                            'subtotal' => $mat->jumlah_material * $mat->harga_material,
                        ];
                    })->toArray(),
                ];
            }),
            'labors' => $salesOrder->labors->map(function ($labor) {
                $mp = $labor->jumlah_sdm ?? 1;
                $days = $labor->jumlah_hari ?? 0;
                $rate = $labor->rate_hari ?? 0;
                return [
                    'labor_name' => $labor->nama_labor,
                    'mp' => $mp,
                    'days' => $days,
                    'rate' => $rate,
                    'subtotal' => $mp * $days * $rate,
                ];
            }),
            'other_costs' => $salesOrder->otherCosts->map(function ($cost) {
                return [
                    'cost_name' => $cost->nama_biaya,
                    'qty' => 1,
                    'rate' => $cost->jumlah_biaya,
                    'subtotal' => $cost->jumlah_biaya,
                ];
            }),
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
            'alamat_pengiriman' => $client->alamat_pengiriman_perusahaan,
            'attn' => $client->attn,
            'cc' => $client->cc,
        ]);
    }

    // ─── Helpers ────────────────────────────────────────────────────────────

    /**
     * Hitung ulang grandtotal dengan rumus: DISKON SEBELUM PAJAK
     * Rumus: (subtotal_produksi + subtotal_material + subtotal_labor + subtotal_lainlain - diskon) * pajak%
     */
    private function calculateAmounts(array $data): array
    {
        $subtotalProduksi = floatval($data['subtotal_produksi'] ?? 0);
        $subtotalMaterial = floatval($data['subtotal_material'] ?? 0);
        $subtotalLabor = floatval($data['subtotal_labor'] ?? 0);
        $subtotalLainlain = floatval($data['subtotal_lainlain'] ?? 0);
        $diskon = floatval($data['diskon'] ?? 0);
        // tax_percentage adalah field dari form, pajak adalah field di database
        $pajakPersen = floatval($data['tax_percentage'] ?? $data['pajak'] ?? 0);

        $subtotalAll = $subtotalProduksi + $subtotalMaterial + $subtotalLabor + $subtotalLainlain;

        // Dasar pengenaan pajak = subtotal - diskon (tidak boleh negatif)
        $taxableBase = max($subtotalAll - $diskon, 0);

        // Hitung pajak dan grand total
        $pajakAmount = $taxableBase * ($pajakPersen / 100);
        $grandtotal = $taxableBase + $pajakAmount;

        return [
            'pajak' => $pajakPersen,
            'grandtotal' => $grandtotal,
        ];
    }

}
