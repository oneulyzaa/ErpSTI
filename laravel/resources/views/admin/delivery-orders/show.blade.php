@extends('layouts.app')
@section('title', 'Detail ' . $deliveryOrder->do_number)
@section('breadcrumb', 'Detail Delivery Order')

@push('styles')
<style>
    .badge-draft     { background:#e2e8f0; color:#475569; }
    .badge-confirmed { background:#dbeafe; color:#1d4ed8; }
    .badge-shipped   { background:#fef3c7; color:#92400e; }
    .badge-delivered { background:#dcfce7; color:#15803d; }
    .badge-cancelled { background:#fee2e2; color:#b91c1c; }
</style>
@endpush

@section('content')

@php
    $statusMap = ['draft'=>['draft','Draft'],'confirmed'=>['confirmed','Confirmed'],'shipped'=>['shipped','Shipped'],'delivered'=>['delivered','Delivered'],'cancelled'=>['cancelled','Cancelled']];
    $s = $statusMap[$deliveryOrder->status] ?? ['draft','-'];
@endphp

<div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h4 class="fw-bold mb-0" style="font-family:monospace">{{ $deliveryOrder->do_number }}</h4>
            <span class="badge badge-{{ $s[0] }} rounded-pill px-2 py-1">{{ $s[1] }}</span>
        </div>
        <p class="text-muted mb-0" style="font-size:13px">
            Tanggal DO: {{ $deliveryOrder->date->format('d M Y') }}
            @if($deliveryOrder->delivery_date)
                &nbsp;·&nbsp; Pengiriman: {{ $deliveryOrder->delivery_date->format('d M Y') }}
            @endif
            @if($deliveryOrder->so_number)
                &nbsp;·&nbsp; Ref. SO: {{ $deliveryOrder->so_number }}
            @endif
        </p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('admin.delivery-orders.pdf', $deliveryOrder) }}" target="_blank"
           class="btn btn-success btn-sm d-flex align-items-center gap-2 px-3">
            <i class="bi bi-file-earmark-pdf-fill"></i> Cetak PDF
        </a>
        <a href="{{ route('admin.delivery-orders.edit', $deliveryOrder) }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <a href="{{ route('admin.delivery-orders.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <form action="{{ route('admin.delivery-orders.destroy', $deliveryOrder) }}" method="POST"
              onsubmit="return confirm('Hapus Delivery Order ini?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-trash"></i>
            </button>
        </form>
    </div>
</div>

<div class="row g-3 align-items-start">
    <div class="col-12 col-xl-8 d-flex flex-column gap-3">

        {{-- Header Info --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="row g-0">
                    <div class="col-12 col-sm-6 p-4 border-end">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;margin-bottom:10px;">Dari</div>
                        <div class="fw-bold" style="font-size:15px">PT. Sistem Teknologi Integrator</div>
                        <div class="text-muted mt-1" style="font-size:13px;line-height:1.8;">
                            Ruko Palazo Blok AB 46, Ciantra<br>Cikarang Selatan, Bekasi 17530<br>
                            Telp: +6221-22108157<br>marketing@stintegrator.com
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 p-4">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;margin-bottom:10px;">Kepada</div>
                        <div class="fw-bold" style="font-size:15px">{{ $deliveryOrder->client_company }}</div>
                        <div class="text-muted mt-1" style="font-size:13px;line-height:1.8;">
                            @if($deliveryOrder->client_attention)Attn: {{ $deliveryOrder->client_attention }}<br>@endif
                            @if($deliveryOrder->client_cc)CC: {{ $deliveryOrder->client_cc }}<br>@endif
                            Kontak: {{ $deliveryOrder->client_name }}<br>
                            @if($deliveryOrder->client_email){{ $deliveryOrder->client_email }}@endif
                        </div>
                    </div>
                </div>
                @if($deliveryOrder->destination_address)
                <div class="p-4 border-top bg-light">
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;margin-bottom:6px;">Alamat Tujuan</div>
                    <div style="font-size:14px;white-space:pre-line;">{{ $deliveryOrder->destination_address }}</div>
                </div>
                @endif
                @if($deliveryOrder->description)
                <div class="p-4 border-top bg-light">
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;margin-bottom:6px;">Deskripsi</div>
                    <div style="font-size:14px;">{{ $deliveryOrder->description }}</div>
                </div>
                @endif
                @if($deliveryOrder->so_number)
                <div class="p-4 border-top">
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;margin-bottom:6px;">Referensi Sales Order</div>
                    <div style="font-size:14px;font-family:monospace;font-weight:600;">{{ $deliveryOrder->so_number }}</div>
                </div>
                @endif
            </div>
        </div>

        {{-- Items --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                <span class="fw-semibold">Item Pengiriman</span>
                <span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $deliveryOrder->items->count() }} item</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="font-size:13px;">
                    <thead class="table-light">
                        <tr>
                            <th style="width:36px;" class="text-muted fw-semibold" style="font-size:11px">#</th>
                            <th class="text-muted fw-semibold" style="font-size:11px">ITEM</th>
                            <th class="text-muted fw-semibold" style="font-size:11px">DESKRIPSI</th>
                            <th class="text-muted fw-semibold text-center" style="font-size:11px">SATUAN</th>
                            <th class="text-muted fw-semibold text-end" style="font-size:11px">QTY</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deliveryOrder->items as $i => $item)
                        <tr>
                            <td class="text-center text-muted" style="font-family:monospace;font-size:12px;">{{ $i+1 }}</td>
                            <td class="fw-semibold">{{ $item->item_name }}</td>
                            <td class="text-muted">{{ $item->description ?: '-' }}</td>
                            <td class="text-center">{{ $item->unit }}</td>
                            <td class="text-end" style="font-family:monospace;">{{ number_format($item->qty, 2, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="4" class="text-end fw-semibold" style="font-size:12px;">Total Item</td>
                            <td class="text-end fw-bold" style="font-family:monospace;">{{ $deliveryOrder->items->count() }}</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-end fw-semibold" style="font-size:12px;">Total Qty</td>
                            <td class="text-end fw-bold" style="font-family:monospace;">{{ number_format($deliveryOrder->items->sum('qty'), 2, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>

    {{-- RIGHT --}}
    <div class="col-12 col-xl-4">
        @if($deliveryOrder->notes)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3"><span class="fw-semibold">Catatan</span></div>
            <div class="card-body" style="font-size:13px;white-space:pre-line;">{{ $deliveryOrder->notes }}</div>
        </div>
        @endif
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-footer bg-white border-top">
                <a href="{{ route('admin.delivery-orders.pdf', $deliveryOrder) }}" target="_blank"
                   class="btn btn-success w-100 d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-file-earmark-pdf-fill"></i> Cetak / Download PDF
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
