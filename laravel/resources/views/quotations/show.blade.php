@extends('layouts.app')
@section('title', 'Detail ' . $quotation->quote_number)
@section('breadcrumb', 'Detail Quotation')

@push('styles')
<style>
    .info-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #94a3b8; margin-bottom: 4px; }
    .info-value { font-size: 14px; color: #1e293b; }
    .summary-row { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; font-size: 14px; color: #475569; border-bottom: 1px solid #f1f5f9; }
    .summary-row:last-child { border-bottom: none; }
    .summary-row.total-row { font-size: 18px; font-weight: 700; color: #1e293b; border-top: 2px solid #e2e8f0; border-bottom: none; margin-top: 4px; padding-top: 12px; }
    .summary-val { font-family: monospace; font-size: 13px; color: #1e293b; }
    .summary-row.total-row .summary-val { font-size: 18px; color: #1B5DBC; font-weight: 700; }
    .badge-draft    { background:#e2e8f0; color:#475569; }
    .badge-sent     { background:#dbeafe; color:#1d4ed8; }
    .badge-approved { background:#dcfce7; color:#15803d; }
    .badge-rejected { background:#fee2e2; color:#b91c1c; }
    .badge-expired  { background:#fef9c3; color:#92400e; }
</style>
@endpush

@section('content')

@php
    $statusMap = [
        'draft'    => ['draft',    'Draft'],
        'sent'     => ['sent',     'Terkirim'],
        'approved' => ['approved', 'Disetujui'],
        'rejected' => ['rejected', 'Ditolak'],
        'expired'  => ['expired',  'Kadaluarsa'],
    ];
    $s = $statusMap[$quotation->status] ?? ['draft','-'];
@endphp

{{-- Header --}}
<div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h4 class="fw-bold mb-0" style="font-family:monospace">{{ $quotation->quote_number }}</h4>
            <span class="badge badge-{{ $s[0] }} rounded-pill px-2 py-1">{{ $s[1] }}</span>
        </div>
        <p class="text-muted mb-0" style="font-size:13px">
            Tanggal: {{ $quotation->date->format('d M Y') }}
            &nbsp;·&nbsp;
            Berlaku s/d: <span style="{{ $quotation->valid_until->isPast() && $quotation->status !== 'approved' ? 'color:#b91c1c' : '' }}">
                {{ $quotation->valid_until->format('d M Y') }}
            </span>
        </p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('quotations.edit', $quotation) }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <a href="{{ route('quotations.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <form action="{{ route('quotations.destroy', $quotation) }}" method="POST"
              onsubmit="return confirm('Hapus quotation ini?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-outline-danger btn-sm d-flex align-items-center gap-1">
                <i class="bi bi-trash"></i> Hapus
            </button>
        </form>
    </div>
</div>

<div class="row g-3 align-items-start">

    {{-- LEFT --}}
    <div class="col-12 col-xl-8 d-flex flex-column gap-3">

        {{-- Header Info --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="row g-0">
                    <div class="col-12 col-sm-6 p-4 border-end">
                        <div class="info-label mb-3">Dari</div>
                        <div class="fw-bold" style="font-size:15px;color:#1e293b;">PT. Sistem Teknologi Integrator</div>
                        <div class="text-muted mt-1" style="font-size:13px;line-height:1.8;">
                            Ruko Palazo Blok AB 46, Ciantra<br>
                            Cikarang Selatan, Bekasi 17530<br>
                            Telp: +6221-22108157<br>
                            marketing@stintegrator.com
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 p-4">
                        <div class="info-label mb-3">Kepada</div>
                        <div class="fw-bold" style="font-size:15px;color:#1e293b;">{{ $quotation->client_company }}</div>
                        <div class="text-muted mt-1" style="font-size:13px;line-height:1.8;">
                            @if($quotation->client_attention)Attn: {{ $quotation->client_attention }}<br>@endif
                            @if($quotation->client_cc)CC: {{ $quotation->client_cc }}<br>@endif
                            Kontak: {{ $quotation->client_name }}<br>
                            @if($quotation->client_email){{ $quotation->client_email }}@endif
                        </div>
                    </div>
                </div>

                @if($quotation->description_of_work)
                <div class="p-4 border-top bg-light">
                    <div class="info-label mb-1">Deskripsi Pekerjaan</div>
                    <div style="font-size:14px;color:#374151;">{{ $quotation->description_of_work }}</div>
                </div>
                @endif
            </div>
        </div>

        {{-- Items Table --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <span class="fw-semibold">Item / Material</span>
                <span class="badge bg-secondary bg-opacity-10 text-secondary ms-2">{{ $quotation->items->count() }} item</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="font-size:13px;">
                    <thead class="table-light">
                        <tr>
                            <th style="width:44px;" class="text-muted fw-semibold" style="font-size:12px">#</th>
                            <th class="text-muted fw-semibold" style="font-size:12px">MATERIAL / JASA</th>
                            <th class="text-muted fw-semibold" style="font-size:12px">DESKRIPSI</th>
                            <th class="text-muted fw-semibold text-center" style="font-size:12px">SATUAN</th>
                            <th class="text-muted fw-semibold text-end" style="font-size:12px">QTY</th>
                            <th class="text-muted fw-semibold text-end" style="font-size:12px">HARGA SATUAN</th>
                            <th class="text-muted fw-semibold text-end" style="font-size:12px">SUBTOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($quotation->items as $i => $item)
                        <tr>
                            <td class="text-center text-muted" style="font-family:monospace;font-size:12px;">{{ $i + 1 }}</td>
                            <td class="fw-semibold">{{ $item->material_name }}</td>
                            <td class="text-muted">{{ $item->description ?: '-' }}</td>
                            <td class="text-center">{{ $item->unit }}</td>
                            <td class="text-end" style="font-family:monospace;">{{ number_format($item->qty, 2, ',', '.') }}</td>
                            <td class="text-end" style="font-family:monospace;">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                            <td class="text-end fw-semibold" style="font-family:monospace;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if($quotation->notes)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <span class="fw-semibold">Catatan / Syarat & Ketentuan</span>
            </div>
            <div class="card-body">
                <p class="text-muted mb-0" style="font-size:14px;line-height:1.7;">{{ $quotation->notes }}</p>
            </div>
        </div>
        @endif

    </div>

    {{-- RIGHT --}}
    <div class="col-12 col-xl-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <span class="fw-semibold">Ringkasan Pembayaran</span>
            </div>
            <div class="card-body">
                @if($quotation->customer_id)
                <div class="summary-row" style="font-size:13px;">
                    <span class="text-muted">Customer ID</span>
                    <span style="font-family:monospace;font-size:13px;">{{ $quotation->customer_id }}</span>
                </div>
                @endif
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span class="summary-val">Rp {{ number_format($quotation->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="summary-row">
                    <span>PPN ({{ number_format($quotation->tax_percentage, 0) }}%)</span>
                    <span class="summary-val">Rp {{ number_format($quotation->tax_amount, 0, ',', '.') }}</span>
                </div>
                <div class="summary-row total-row">
                    <span>Total</span>
                    <span class="summary-val">Rp {{ number_format($quotation->total, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection
