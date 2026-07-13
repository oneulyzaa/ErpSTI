<?php

namespace App\Http\Controllers;

use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderItem;
use App\Models\DeliveryOrderItemMaterial;
use App\Models\SalesOrder;
use App\Models\ClientModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class DeliveryOrderController extends Controller
{
    // ─── List ────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = DeliveryOrder::with(['items', 'client'])->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nomor_deliveryorder', 'like', "%$s%")
                    ->orWhere('nama_project', 'like', "%$s%")
                    ->orWhere('nomor_salesorder', 'like', "%$s%")
                    ->orWhere('nomor_po', 'like', "%$s%");
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $deliveryOrders = $query->paginate(15)->withQueryString();
        return view('admin.delivery-orders.index', compact('deliveryOrders'));
    }

    // ─── Create ──────────────────────────────────────────────────────────────
    public function create()
    {
        $doNumber = DeliveryOrder::generateDONumber();
        $salesOrders = SalesOrder::whereIn('status', ['confirmed', 'in_progress', 'completed'])->latest()->get();
        $clients = ClientModel::all();
        return view('admin.delivery-orders.create', compact('doNumber', 'salesOrders', 'clients'));
    }

    // ─── Store ───────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_deliveryorder' => 'required|string|unique:delivery_orders,nomor_deliveryorder',
            'id_client' => 'nullable|exists:customers,id',
            'nomor_salesorder' => 'nullable|exists:sales_orders,nomor_salesorder',
            'nomor_po' => 'nullable|string|max:255',
            'nama_project' => 'nullable|string|max:255',
            'tanggal_pembuatan' => 'required|date',
            'tanggal_pengiriman' => 'nullable|date|after_or_equal:tanggal_pembuatan',
            'status' => 'required|in:draft,confirmed,shipped,delivered,cancelled',
            'keterangan' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*.nama_item' => 'required_with:items|string|max:255',
            'items.*.deskripsi_item' => 'nullable|string',
            'items.*.satuan' => 'required_with:items|string|max:50',
            'items.*.jumlah_item' => 'required_with:items|numeric|min:0',
            'items.*.harga_item' => 'nullable|numeric|min:0',
            'items.*.materials' => 'nullable|array',
            'items.*.materials.*.id_material' => 'nullable|exists:materials,id_material',
            'items.*.materials.*.nama_material' => 'required_with:items.*.materials|string|max:255',
            'items.*.materials.*.satuan_material' => 'nullable|string|max:50',
            'items.*.materials.*.jumlah_material' => 'required_with:items.*.materials|numeric|min:0',
            'items.*.materials.*.harga_material' => 'nullable|numeric|min:0',
        ]);

        // Set id_staff dari user yang login
        $validated['id_staff'] = auth()->id();

        if (empty($requset->tanggal_pengiriman))
            $validated['tanggal_pengiriman'] = now()->addDays(7);

        DB::transaction(function () use ($validated, $request) {
            $deliveryOrder = DeliveryOrder::create($validated);
            $this->syncItems($deliveryOrder, $request->items ?? []);
        });

        return redirect()->route('admin.delivery-orders.index')
            ->with('success', 'Delivery Order berhasil dibuat.');
    }

    // ─── Show ────────────────────────────────────────────────────────────────
    public function show(DeliveryOrder $deliveryOrder)
    {
        $deliveryOrder->load('items.materials', 'salesOrder', 'client');
        return view('admin.delivery-orders.show', compact('deliveryOrder'));
    }

    // ─── Edit ────────────────────────────────────────────────────────────────
    public function edit(DeliveryOrder $deliveryOrder)
    {
        $deliveryOrder->load('items.materials');
        $doNumber = $deliveryOrder->nomor_deliveryorder;
        $salesOrders = SalesOrder::whereIn('status', ['confirmed', 'in_progress', 'completed'])->latest()->get();
        $clients = ClientModel::all();
        return view('admin.delivery-orders.edit', compact('deliveryOrder', 'doNumber', 'salesOrders', 'clients'));
    }

    // ─── Update ──────────────────────────────────────────────────────────────
    public function update(Request $request, DeliveryOrder $deliveryOrder)
    {
        $validated = $request->validate([
            'nomor_deliveryorder' => 'required|string|unique:delivery_orders,nomor_deliveryorder,' . $deliveryOrder->nomor_deliveryorder . ',nomor_deliveryorder',
            'id_client' => 'nullable|exists:customers,id',
            'nomor_salesorder' => 'nullable|exists:sales_orders,nomor_salesorder',
            'nomor_po' => 'nullable|string|max:255',
            'nama_project' => 'nullable|string|max:255',
            'tanggal_pembuatan' => 'required|date',
            'tanggal_pengiriman' => 'nullable|date|after_or_equal:tanggal_pembuatan',
            'status' => 'required|in:draft,confirmed,shipped,delivered,cancelled',
            'keterangan' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*.nama_item' => 'required_with:items|string|max:255',
            'items.*.deskripsi_item' => 'nullable|string',
            'items.*.satuan' => 'required_with:items|string|max:50',
            'items.*.jumlah_item' => 'required_with:items|numeric|min:0',
            'items.*.harga_item' => 'nullable|numeric|min:0',
            'items.*.materials' => 'nullable|array',
            'items.*.materials.*.id_material' => 'nullable|exists:materials,id_material',
            'items.*.materials.*.nama_material' => 'required_with:items.*.materials|string|max:255',
            'items.*.materials.*.satuan_material' => 'nullable|string|max:50',
            'items.*.materials.*.jumlah_material' => 'required_with:items.*.materials|numeric|min:0',
            'items.*.materials.*.harga_material' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated, $request, $deliveryOrder) {
            $deliveryOrder->update($validated);
            $this->syncItems($deliveryOrder, $request->items ?? []);
        });

        return redirect()->route('admin.delivery-orders.show', $deliveryOrder)
            ->with('success', 'Delivery Order berhasil diperbarui.');
    }

    // ─── Delete ──────────────────────────────────────────────────────────────
    public function destroy(DeliveryOrder $deliveryOrder)
    {
        $deliveryOrder->delete();
        return redirect()->route('admin.delivery-orders.index')
            ->with('success', 'Delivery Order berhasil dihapus.');
    }

    // ─── PDF ─────────────────────────────────────────────────────────────────
    public function pdf(DeliveryOrder $deliveryOrder)
    {
        $deliveryOrder->load('items.materials', 'client');

        $logoPath = public_path('assets/gambar/logo-sti.png');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView('admin.delivery-orders.pdf', compact('deliveryOrder', 'logoBase64'))
            ->setPaper('a5', 'landscape')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', false);

        $filename = 'DeliveryOrder-' . $deliveryOrder->nomor_deliveryorder . '.pdf';
        return $pdf->stream($filename);
    }

    // ─── AJAX: Get SO Data ──────────────────────────────────────────────────
    public function getSoData($nomorSalesorder)
    {
        $salesOrder = SalesOrder::where('nomor_salesorder', $nomorSalesorder)->firstOrFail();
        $salesOrder->load('items.materials', 'client');

        return response()->json([
            'nomor_salesorder' => $salesOrder->nomor_salesorder,
            'nomor_po' => $salesOrder->nomor_po,
            'nama_project' => $salesOrder->nama_project,
            'id_client' => $salesOrder->id_client,
            'items' => $salesOrder->items->map(function ($item) {
                return [
                    'nama_item' => $item->nama_item,
                    'deskripsi_item' => $item->deskripsi_item,
                    'satuan' => $item->satuan,
                    'jumlah_item' => $item->jumlah_item,
                    'harga_item' => $item->harga_item,
                    'materials' => $item->materials->map(function ($mat) {
                        return [
                            'id_material' => $mat->id_material,
                            'nama_material' => $mat->nama_material,
                            'satuan_material' => $mat->satuan_material,
                            'jumlah_material' => $mat->jumlah_material,
                            'harga_material' => $mat->harga_material,
                        ];
                    })->toArray(),
                ];
            }),
        ]);
    }

    // ─── AJAX: Get Client Data from master client ──────────────────────────
    public function getClientData($id_client)
    {
        $client = ClientModel::where('id', $id_client)->firstOrFail();
        return response()->json([
            'id' => $client->id,
            'nama_perusahaan' => $client->nama_perusahaan,
            'nama_kontak' => $client->nama_kontak,
            'email' => $client->email_perusahaan,
            'alamat' => $client->alamat_perusahaan,
            'attn' => $client->nama_kontak,
            'cc' => '',
        ]);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────
    private function syncItems(DeliveryOrder $deliveryOrder, array $items): void
    {
        $deliveryOrder->items()->delete();
        foreach ($items as $i => $item) {
            if (empty($item['nama_item']))
                continue;
            $doItem = DeliveryOrderItem::create([
                'nomor_deliveryorder' => $deliveryOrder->nomor_deliveryorder,
                'nama_item' => $item['nama_item'],
                'deskripsi_item' => $item['deskripsi_item'] ?? null,
                'jumlah_item' => $item['jumlah_item'] ?? 0,
                'satuan' => $item['satuan'] ?? 'Unit',
                'harga_item' => $item['harga_item'] ?? 0,
            ]);

            if (!empty($item['materials'])) {
                foreach ($item['materials'] as $m => $mat) {
                    if (empty($mat['nama_material']))
                        continue;
                    DeliveryOrderItemMaterial::create([
                        'id_item' => $doItem->id_item,
                        'id_material' => $mat['id_material'] ?? null,
                        'nama_material' => $mat['nama_material'],
                        'jumlah_material' => $mat['jumlah_material'] ?? 0,
                        'satuan_material' => $mat['satuan_material'] ?? 'pcs',
                        'harga_material' => $mat['harga_material'] ?? 0,
                    ]);
                }
            }
        }
    }
}