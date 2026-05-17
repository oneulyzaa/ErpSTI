<?php

namespace App\Http\Controllers;

use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderItem;
use App\Models\SalesOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class DeliveryOrderController extends Controller
{
    // ─── List ────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = DeliveryOrder::with('items')->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('do_number', 'like', "%$s%")
                  ->orWhere('client_name', 'like', "%$s%")
                  ->orWhere('client_company', 'like', "%$s%")
                  ->orWhere('so_number', 'like', "%$s%");
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
        $doNumber    = DeliveryOrder::generateDONumber();
        $salesOrders = SalesOrder::whereIn('status', ['confirmed', 'in_progress', 'completed'])->latest()->get();
        return view('admin.delivery-orders.create', compact('doNumber', 'salesOrders'));
    }

    // ─── Store ───────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'do_number'           => 'required|string|unique:delivery_orders,do_number',
            'sales_order_id'      => 'nullable|exists:sales_orders,id',
            'so_number'           => 'nullable|string|max:255',
            'date'                => 'required|date',
            'delivery_date'       => 'nullable|date|after_or_equal:date',
            'client_name'         => 'nullable|string|max:255',
            'client_company'      => 'nullable|string|max:255',
            'client_attention'    => 'nullable|string|max:255',
            'client_cc'           => 'nullable|string|max:255',
            'client_email'        => 'nullable|email|max:255',
            'destination_address' => 'nullable|string',
            'description'         => 'nullable|string',
            'status'              => 'required|in:draft,confirmed,shipped,delivered,cancelled',
            'notes'               => 'nullable|string',
            'items'               => 'nullable|array',
            'items.*.item_name'   => 'required_with:items|string|max:255',
            'items.*.unit'        => 'required_with:items|string|max:50',
            'items.*.qty'         => 'required_with:items|numeric|min:0',
        ]);

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
        $deliveryOrder->load('items', 'salesOrder');
        return view('admin.delivery-orders.show', compact('deliveryOrder'));
    }

    // ─── Edit ────────────────────────────────────────────────────────────────
    public function edit(DeliveryOrder $deliveryOrder)
    {
        $deliveryOrder->load('items');
        $doNumber    = $deliveryOrder->do_number;
        $salesOrders = SalesOrder::whereIn('status', ['confirmed', 'in_progress', 'completed'])->latest()->get();
        return view('admin.delivery-orders.edit', compact('deliveryOrder', 'doNumber', 'salesOrders'));
    }

    // ─── Update ──────────────────────────────────────────────────────────────
    public function update(Request $request, DeliveryOrder $deliveryOrder)
    {
        $validated = $request->validate([
            'do_number'           => 'required|string|unique:delivery_orders,do_number,' . $deliveryOrder->id,
            'sales_order_id'      => 'nullable|exists:sales_orders,id',
            'so_number'           => 'nullable|string|max:255',
            'date'                => 'required|date',
            'delivery_date'       => 'nullable|date|after_or_equal:date',
            'client_name'         => 'nullable|string|max:255',
            'client_company'      => 'nullable|string|max:255',
            'client_attention'    => 'nullable|string|max:255',
            'client_cc'           => 'nullable|string|max:255',
            'client_email'        => 'nullable|email|max:255',
            'destination_address' => 'nullable|string',
            'description'         => 'nullable|string',
            'status'              => 'required|in:draft,confirmed,shipped,delivered,cancelled',
            'notes'               => 'nullable|string',
            'items'               => 'nullable|array',
            'items.*.item_name'   => 'required_with:items|string|max:255',
            'items.*.unit'        => 'required_with:items|string|max:50',
            'items.*.qty'         => 'required_with:items|numeric|min:0',
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
        $deliveryOrder->load('items');

        $logoPath = public_path('assets/gambar/logo-sti.png');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView('admin.delivery-orders.pdf', compact('deliveryOrder', 'logoBase64'))
            ->setPaper('a4', 'portrait')
            ->setOption('defaultFont', 'DejaVu Sans')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', false);

        $filename = 'DeliveryOrder-' . $deliveryOrder->do_number . '.pdf';
        return $pdf->stream($filename);
    }

    // ─── AJAX: Get SO Data ──────────────────────────────────────────────────
    public function getSoData(SalesOrder $salesOrder)
    {
        $salesOrder->load('items');

        return response()->json([
            'so_number'         => $salesOrder->so_number,
            'client_name'       => $salesOrder->client_name,
            'client_company'    => $salesOrder->client_company,
            'client_attention'  => $salesOrder->client_attention,
            'client_cc'         => $salesOrder->client_cc,
            'client_email'      => $salesOrder->client_email,
            'description'       => $salesOrder->description_of_work,
            'items'             => $salesOrder->items->map(function ($item) {
                return [
                    'item_name'  => $item->material_name,
                    'description' => $item->description,
                    'unit'       => $item->unit,
                    'qty'        => $item->qty,
                ];
            }),
        ]);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────
    private function syncItems(DeliveryOrder $deliveryOrder, array $items): void
    {
        $deliveryOrder->items()->delete();
        foreach ($items as $i => $item) {
            if (empty($item['item_name'])) continue;
            DeliveryOrderItem::create([
                'delivery_order_id' => $deliveryOrder->id,
                'sort_order'        => $i + 1,
                'item_name'         => $item['item_name'],
                'description'       => $item['description'] ?? null,
                'unit'              => $item['unit'] ?? 'Unit',
                'qty'               => $item['qty'] ?? 0,
            ]);
        }
    }
}
