@extends('layouts.app')
@section('title', 'Detail ' . $invoice->invoice_number)
@section('breadcrumb', 'Detail Invoice')

@push('styles')
<style>
    .badge-draft     { background:#e2e8f0; color:#475569; }
    .badge-sent      { background:#dbeafe; color:#1d4ed8; }
    .badge-paid      { background:#dcfce7; color:#15803d; }
    .badge-overdue   { background:#fee2e2; color:#b91c1c; }
    .badge-cancelled { background:#f3e8ff; color:#7c3aed; }
    .info-label { font-size: 12px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; }
    .info-value { font-size: 14px; font-weight: 500; color: #1e293b; }
    .table-items th { background: #1e3a5f !important; color: #fff !important; font-size: 11px; text-transform: uppercase; letter-spacing: .05em; }
    .total-value { font-family: monospace; color: #1B5DBC; }
</style>
@endpush

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Invoice: {{ $invoice->invoice_number }}</h4>
        <p class="text-muted mb-0" style="font-size:13px">{{ $invoice->client_company }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.invoices.pdf', $invoice) }}" class="btn btn-danger d-flex align-items-center gap-2" target="_blank">
            <i class="bi bi-file-pdf"></i> Cetak PDF
        </a>
        <a href="{{ route('admin.invoices.edit', $invoice) }}" class="btn btn-primary d-flex align-items-center gap-2">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <a href="{{ route('admin.invoices.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="row g-3 align-items-start">

    {{-- ── LEFT COLUMN ── --}}
    <div class="col-12 col-xl-8 d-flex flex-column gap-3">

        {{-- Info Invoice --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <span class="fw-semibold">Informasi Invoice</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-sm-4">
                        <span class="info-label">No. Invoice</span>
                        <div class="info-value">{{ $invoice->invoice_number }}</div>
                    </div>
                    <div class="col-12 col-sm-4">
                        <span class="info-label">No. SO</span>
                        <div class="info-value">{{ $invoice->so_number ?: '-' }}</div>
                    </div>
                    <div class="col-12 col-sm-4">
                        <span class="info-label">Status</span>
                        <div class="info-value mt-1">
                            <span class="badge badge-{{ $invoice->status }} px-2 py-1" style="font-size:12px;">{{ ucfirst($invoice->status) }}</span>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <span class="info-label">Tanggal</span>
                        <div class="info-value">{{ $invoice->date->format('d M Y') }}</div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <span class="info-label">Jatuh Tempo</span>
                        <div class="info-value">{{ $invoice->due_date?->format('d M Y') ?: '-' }}</div>
                    </div>
                    @if($invoice->description)
                    <div class="col-12">
                        <span class="info-label">Deskripsi</span>
                        <div class="info-value">{{ $invoice->description }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Klien --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <span class="fw-semibold">Klien</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-sm-6">
                        <span class="info-label">Perusahaan</span>
                        <div class="info-value">{{ $invoice->client_company }}</div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <span class="info-label">Kontak</span>
                        <div class="info-value">{{ $invoice->client_name ?: '-' }}</div>
                    </div>
                    <div class="col-12 col-sm-4">
                        <span class="info-label">Attn</span>
                        <div class="info-value">{{ $invoice->client_attention ?: '-' }}</div>
                    </div>
                    <div class="col-12 col-sm-4">
                        <span class="info-label">CC</span>
                        <div class="info-value">{{ $invoice->client_cc ?: '-' }}</div>
                    </div>
                    <div class="col-12 col-sm-4">
                        <span class="info-label">Email</span>
                        <div class="info-value">{{ $invoice->client_email ?: '-' }}</div>
                    </div>
                    @if($invoice->client_address)
                    <div class="col-12">
                        <span class="info-label">Alamat</span>
                        <div class="info-value" style="white-space:pre-wrap;">{{ $invoice->client_address }}</div>      
                    </div>
                    @endif  
                </div>
            </div>
        </div>

        {{-- Item Produksi --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <span class="fw-semibold">Item Produksi</span>
            </div>
            <div class="table-responsive">
                <table class="table mb-0" style="font-size:13px">
                    <thead class="table-items">
                        <tr>
                            <th style="width:36px;">#</th>
                            <th>Nama Item</th>
                            <th>Deskripsi</th>
                            <th class="text-center" style="width:70px;">Satuan</th>
                            <th class="text-center" style="width:70px;">Qty</th>
                            <th class="text-end" style="width:130px;">Harga</th>
                            <th class="text-end" style="width:130px;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoice->items as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-semibold">{{ $item->item_name }}</td>
                            <td class="text-muted">{{ $item->description ?? '-' }}</td>
                            <td class="text-center">{{ $item->unit }}</td>
                            <td class="text-center">{{ number_format($item->qty, 2) }}</td>
                            <td class="text-end">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                            <td class="text-end fw-semibold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada item produksi</td></tr>
                        @endforelse
                    </tbody>
                    @if($invoice->items->isNotEmpty())
                    <tfoot>
                        <tr style="background:#f0f4fc;">
                            <td colspan="6" class="text-end fw-bold" style="font-size:13px;">Total Produksi</td>
                            <td class="text-end fw-bold total-value" style="font-size:13px;">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>

        {{-- T&C --}}
        @if($invoice->term_and_condition)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <span class="fw-semibold">Syarat & Ketentuan</span>
            </div>
            <div class="card-body">
                <p style="font-size:13px;white-space:pre-wrap;margin:0">{{ $invoice->term_and_condition }}</p>
            </div>
        </div>
        @endif

        {{-- Notes --}}
        @if($invoice->notes)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <span class="fw-semibold">Catatan</span>
            </div>
            <div class="card-body">
                <p style="font-size:13px;white-space:pre-wrap;margin:0">{{ $invoice->notes }}</p>
            </div>
        </div>
        @endif

    </div>

    {{-- ── RIGHT COLUMN ── --}}
    <div class="col-12 col-xl-4 d-flex flex-column gap-3">

        {{-- Ringkasan Total --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <span class="fw-semibold">Ringkasan Total</span>
            </div>
            <div class="card-body p-0">
                <div class="d-flex justify-content-between align-items-center px-3 py-2" style="border-bottom:1px solid #f1f5f9;">
                    <span style="font-size:13px;color:#475569;">Subtotal Produksi</span>
                    <span class="total-value" style="font-size:13px;">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center px-3 py-2" style="border-bottom:1px solid #f1f5f9;">
                    <span style="font-size:13px;color:#475569;">Subtotal Labor</span>
                    <span class="total-value" style="font-size:13px;">Rp {{ number_format($invoice->subtotal_labor ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center px-3 py-2" style="border-bottom:1px solid #f1f5f9;">
                    <span style="font-size:13px;color:#475569;">Subtotal</span>
                    <span class="total-value" style="font-size:13px;">Rp {{ number_format($invoice->subtotal+$invoice->subtotal_labor ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center px-3 py-2" style="border-bottom:1px solid #f1f5f9;">
                    <span style="font-size:13px;color:#475569;">PPN ({{ $invoice->tax_percentage }}%)</span>
                    <span class="total-value" style="font-size:13px;">Rp {{ number_format($invoice->tax_amount, 0, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center px-3 py-3" style="background:#f8faff;border-radius:0 0 .5rem .5rem;">
                    <strong style="font-size:15px;">Total</strong>
                    <strong class="total-value" style="font-size:17px;">Rp {{ number_format($invoice->total, 0, ',', '.') }}</strong>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
