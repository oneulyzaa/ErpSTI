@extends('layouts.app')
@section('title', 'Detail ' . $invoice->nomor_invoice)
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
@php
    $client = $invoice->salesOrder->client ?? null;
    $so = $invoice->salesOrder;
@endphp
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Invoice: {{ $invoice->nomor_invoice }}</h4>
        <p class="text-muted mb-0" style="font-size:13px">{{ $client->nama_perusahaan ?? '-' }}</p>
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
                        <div class="info-value">{{ $invoice->nomor_invoice }}</div>
                    </div>
                    <div class="col-12 col-sm-4">
                        <span class="info-label">No. SO</span>
                        <div class="info-value">{{ $invoice->nomor_salesorder ?: '-' }}</div>
                    </div>
                    <div class="col-12 col-sm-4">
                        <span class="info-label">Referensi PO</span>
                        <div class="info-value">{{ $invoice->referensi_po ?: '-' }}</div>
                    </div>
                    <div class="col-12 col-sm-4">
                        <span class="info-label">Nama Project</span>
                        <div class="info-value">{{ $invoice->nama_project ?: '-' }}</div>
                    </div>
                    <div class="col-12 col-sm-4">
                        <span class="info-label">Status</span>
                        <div class="info-value mt-1">
                            <span class="badge badge-{{ $invoice->status_pembayaran }} px-2 py-1" style="font-size:12px;">{{ ucfirst($invoice->status_pembayaran) }}</span>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <span class="info-label">Tanggal</span>
                        <div class="info-value">{{ $invoice->tanggal_invoice->format('d M Y') }}</div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <span class="info-label">Jatuh Tempo</span>
                        <div class="info-value">{{ $invoice->jatuh_tempo?->format('d M Y') ?: '-' }}</div>
                    </div>
                    @if($invoice->keterangan)
                    <div class="col-12">
                        <span class="info-label">Keterangan</span>
                        <div class="info-value">{{ $invoice->keterangan }}</div>
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
                        <div class="info-value">{{ $client->nama_perusahaan ?? '-' }}</div>
                    </div>
                    <div class="col-12 col-sm-3">
                        <span class="info-label">Kontak</span>
                        <div class="info-value">{{ $client->nama_kontak ?? '-' }}</div>
                    </div>
                    <div class="col-12 col-sm-3">
                        <span class="info-label">Email</span>
                        <div class="info-value">{{ $client->email_perusahaan ?? '-' }}</div>
                    </div>
                    <div class="col-12">
                        <span class="info-label">Alamat</span>
                        <div class="info-value" style="white-space:pre-wrap;">{{ $client->alamat_perusahaan ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Ringkasan Biaya --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <span class="fw-semibold">Ringkasan Biaya</span>
            </div>
            <div class="table-responsive">
                <table class="table mb-0" style="font-size:13px">
                    <thead class="table-items">
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Keterangan</th>
                            <th class="text-center">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td class="fw-semibold">Biaya Produksi & Material</td>
                            <td class="text-end">Rp {{ number_format($invoice->subtotal_produksi + $invoice->subtotal_material ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td class="fw-semibold">Biaya Tenaga Kerja</td>
                            <td class="text-end">Rp {{ number_format($invoice->subtotal_labor ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td class="fw-semibold">Biaya Lain-Lain</td>
                            <td class="text-end">Rp {{ number_format($invoice->subtotal_lainlain ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr style="background:#f0f4fc;">
                            <td colspan="2" class="text-end fw-bold" style="font-size:13px;">Total Sebelum Diskon</td>
                            @php
                                $subtotalAll = ($invoice->subtotal_produksi ?? 0) + ($invoice->subtotal_material ?? 0) + ($invoice->subtotal_labor ?? 0) + ($invoice->subtotal_lainlain ?? 0);
                            @endphp
                            <td class="text-end fw-bold total-value" style="font-size:13px;">Rp {{ number_format($subtotalAll, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>{{-- end left --}}

    {{-- ── RIGHT COLUMN ── --}}
    <div class="col-12 col-xl-4 d-flex flex-column gap-3">

        {{-- ── Ringkasan Total ── --}}
        @php
            $subtotalAll   = ($invoice->subtotal_produksi ?? 0)
                           + ($invoice->subtotal_material ?? 0)
                           + ($invoice->subtotal_labor ?? 0)
                           + ($invoice->subtotal_lainlain ?? 0);
            $discount      = $invoice->diskon ?? 0;
            $afterDiscount = max($subtotalAll - $discount, 0);
            $pajakPersen   = $invoice->pajak ?? 0;
            $pajakAmount   = $afterDiscount * ($pajakPersen / 100);
        @endphp

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <span class="fw-semibold">Ringkasan Total</span>
            </div>
            <div class="card-body p-0">
                {{-- Total = subtotalAll --}}
                <div class="d-flex justify-content-between align-items-center px-3 py-2" style="border-bottom:1px solid #f1f5f9;">
                    <span style="font-size:13px;color:#475569;font-weight:600;">Total</span>
                    <span class="total-value" style="font-size:13px;font-weight:600;">Rp {{ number_format($subtotalAll, 0, ',', '.') }}</span>
                </div>

                {{-- Diskon --}}
                <div class="d-flex justify-content-between align-items-center px-3 py-2" style="border-bottom:1px solid #f1f5f9;">
                    <span style="font-size:13px;color:#475569;">Diskon</span>
                    <span class="total-value" style="font-size:13px;">Rp {{ number_format($discount, 0, ',', '.') }}</span>
                </div>

                {{-- Setelah Diskon --}}
                <div class="d-flex justify-content-between align-items-center px-3 py-2" style="border-bottom:1px solid #f1f5f9;">
                    <span style="font-size:13px;color:#475569;">Setelah Diskon</span>
                    <span class="total-value" style="font-size:13px;">Rp {{ number_format($afterDiscount, 0, ',', '.') }}</span>
                </div>

                {{-- Pajak --}}
                <div class="d-flex justify-content-between align-items-center px-3 py-2" style="border-bottom:1px solid #f1f5f9;">
                    <span style="font-size:13px;color:#475569;">Pajak ({{ $pajakPersen }}%)</span>
                    <span class="total-value" style="font-size:13px;">Rp {{ number_format($pajakAmount, 0, ',', '.') }}</span>
                </div>

                {{-- Grand Total --}}
                <div class="d-flex justify-content-between align-items-center px-3 py-3" style="background:#f8faff;border-radius:0 0 .5rem .5rem;">
                    <strong style="font-size:15px;">Grand Total</strong>
                    <strong class="total-value" style="font-size:17px;">Rp {{ number_format($invoice->grandtotal ?? 0, 0, ',', '.') }}</strong>
                </div>

            </div>
        </div>

    </div>{{-- end right --}}
</div>
@endsection