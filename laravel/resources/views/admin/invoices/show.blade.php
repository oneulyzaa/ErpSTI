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
                        <span class="info-label">Nomor PO</span>
                        <div class="info-value">{{ $invoice->nomor_po ?: '-' }}</div>
                    </div>
                    <div class="col-12 col-sm-4">
                        <span class="info-label">Nama Project</span>
                        <div class="info-value">{{ $invoice->project_name ?: '-' }}</div>
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

        {{-- Nama Project --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <span class="fw-semibold">Biaya Lain-Lain</span>
            </div>
            <div class="table-responsive">
                @if($invoice->project_name)
                <table class="table mb-0" style="font-size:13px">
                    <thead class="table-items">
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Description</th>
                            <th class="text-center">Part No</th>
                            <th class="text-center">Qty</th>
                            <th class="text-center">Unit Price</th>
                            <th class="text-center">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td class="fw-semibold">{{ $invoice->project_name }}</td>
                            <td class="text-center">-</td>
                            <td class="text-center">{{ number_format(1, 2, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
                            <td class="text-end fw-semibold">Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr style="background:#f0f4fc;">
                            <td colspan="5" class="text-end fw-bold" style="font-size:13px;">Total</td>
                            <td class="text-end fw-bold total-value" style="font-size:13px;">Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
                @endif
            </div>
        </div>
        {{--
            ── Item Produksi — disembunyikan dari tampilan, data tetap ada di DOM ──
            Staff finance tidak perlu melihat rincian produksi/material.
        --}}
        <div class="card border-0 shadow-sm d-none">
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
                        @if($item->materials && $item->materials->count())
                        <tr>
                            <td colspan="7" class="p-0">
                                <table class="table table-sm mb-0" style="font-size:11px;background:#f8fafc;">
                                    <thead>
                                        <tr style="background:#e8f0fe;">
                                            <th style="width:36px;"></th>
                                            <th style="font-size:10px;color:#64748b;">MATERIAL / BAHAN BAKU</th>
                                            <th style="width:70px;text-align:center;font-size:10px;color:#64748b;">SATUAN</th>
                                            <th style="width:70px;text-align:right;font-size:10px;color:#64748b;">QTY</th>
                                            <th style="width:130px;text-align:right;font-size:10px;color:#64748b;">HARGA</th>
                                            <th style="width:130px;text-align:right;font-size:10px;color:#64748b;">SUBTOTAL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($item->materials as $m => $mat)
                                        <tr>
                                            <td class="text-center text-muted" style="font-family:monospace;">{{ $m+1 }}</td>
                                            <td>{{ $mat->material_name }}</td>
                                            <td class="text-center">{{ $mat->satuan }}</td>
                                            <td class="text-center" style="font-family:monospace;">{{ number_format($mat->qty_required, 2, ',', '.') }}</td>
                                            <td class="text-end">Rp {{ number_format($mat->unit_price, 0, ',', '.') }}</td>
                                            <td class="text-end fw-semibold" style="color:#1B5DBC;">Rp {{ number_format($mat->subtotal, 0, ',', '.') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        @endif
                        @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada item produksi</td></tr>
                        @endforelse
                    </tbody>
                    @if($invoice->items->isNotEmpty())
                    <tfoot>
                        <tr style="background:#f0f4fc;">
                            <td colspan="6" class="text-end fw-bold" style="font-size:13px;">Total Produksi</td>
                            <td class="text-end fw-bold total-value" style="font-size:13px;">Rp {{ number_format($invoice->subtotal ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>

        {{--
            ── Biaya Tenaga Kerja — disembunyikan dari tampilan, data tetap ada di DOM ──
        --}}
        @if($invoice->labors && $invoice->labors->count())
        <div class="card border-0 shadow-sm d-none">
            <div class="card-header bg-white border-bottom py-3">
                <span class="fw-semibold">Biaya Tenaga Kerja</span>
            </div>
            <div class="table-responsive">
                <table class="table mb-0" style="font-size:13px">
                    <thead class="table-items">
                        <tr>
                            <th style="width:36px;">#</th>
                            <th>Nama Pekerjaan</th>
                            <th class="text-center" style="width:80px;">MP</th>
                            <th class="text-center" style="width:80px;">Hari</th>
                            <th class="text-end" style="width:130px;">Rate/Hari</th>
                            <th class="text-end" style="width:130px;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->labors as $i => $labor)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td class="fw-semibold">{{ $labor->labor_name }}</td>
                            <td class="text-center">{{ number_format($labor->mp, 0, ',', '.') }}</td>
                            <td class="text-center">{{ number_format($labor->days, 0, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($labor->rate, 0, ',', '.') }}</td>
                            <td class="text-end fw-semibold">Rp {{ number_format($labor->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="background:#f0f4fc;">
                            <td colspan="5" class="text-end fw-bold" style="font-size:13px;">Total Biaya Tenaga Kerja</td>
                            <td class="text-end fw-bold total-value" style="font-size:13px;">Rp {{ number_format($invoice->subtotal_labor ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        @endif

        {{--
            ── Biaya Lain-Lain — disembunyikan dari tampilan, data tetap ada di DOM ──
        --}}
        @if($invoice->otherCosts && $invoice->otherCosts->count())
        <div class="card border-0 shadow-sm d-none">
            <div class="card-header bg-white border-bottom py-3">
                <span class="fw-semibold">Biaya Lain-Lain</span>
            </div>
            <div class="table-responsive">
                <table class="table mb-0" style="font-size:13px">
                    <thead class="table-items">
                        <tr>
                            <th style="width:36px;">#</th>
                            <th>Nama Biaya</th>
                            <th class="text-center" style="width:80px;">Qty</th>
                            <th class="text-end" style="width:130px;">Rate</th>
                            <th class="text-end" style="width:130px;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->otherCosts as $i => $cost)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td class="fw-semibold">{{ $cost->cost_name }}</td>
                            <td class="text-center">{{ number_format($cost->qty, 2, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($cost->rate, 0, ',', '.') }}</td>
                            <td class="text-end fw-semibold">Rp {{ number_format($cost->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="background:#f0f4fc;">
                            <td colspan="4" class="text-end fw-bold" style="font-size:13px;">Total Biaya Lain-Lain</td>
                            <td class="text-end fw-bold total-value" style="font-size:13px;">Rp {{ number_format($invoice->subtotal_other_cost ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        @endif

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

    </div>{{-- end left --}}

    {{-- ── RIGHT COLUMN ── --}}
    <div class="col-12 col-xl-4 d-flex flex-column gap-3">

        {{-- ── Ringkasan Total ── --}}
        @php
            $subtotalAll   = ($invoice->subtotal ?? 0)
                           + ($invoice->subtotal_labor ?? 0)
                           + ($invoice->subtotal_other_cost ?? 0);
            $discount      = $invoice->discount ?? 0;
            $afterDiscount = max($subtotalAll - $discount, 0);
            $dpp           = $afterDiscount * (11 / 12);
        @endphp

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <span class="fw-semibold">Ringkasan Total</span>
            </div>
            <div class="card-body p-0">
                {{-- Total = subtotalAll (produksi + labor + biaya lain-lain) --}}
                <div class="d-flex justify-content-between align-items-center px-3 py-2" style="border-bottom:1px solid #f1f5f9;">
                    <span style="font-size:13px;color:#475569;font-weight:600;">Total</span>
                    <span class="total-value" style="font-size:13px;font-weight:600;">Rp {{ number_format($subtotalAll, 0, ',', '.') }}</span>
                </div>

                {{-- Diskon — selalu ditampilkan (0 jika tidak ada) --}}
                <div class="d-flex justify-content-between align-items-center px-3 py-2" style="border-bottom:1px solid #f1f5f9;">
                    <span style="font-size:13px;color:#475569;">Diskon</span>
                    <span class="total-value" style="font-size:13px;">Rp {{ number_format($discount, 0, ',', '.') }}</span>
                </div>

                {{-- DPP = (subtotalAll - diskon) × 11/12 --}}
                <div class="d-flex justify-content-between align-items-center px-3 py-2" style="border-bottom:1px solid #f1f5f9;">
                    <span style="font-size:13px;color:#475569;">
                        Dasar Pengenaan Pajak
                        <span style="font-size:11px;color:#94a3b8;">(× 11/12)</span>
                    </span>
                    <span class="total-value" style="font-size:13px;">Rp {{ number_format($dpp, 0, ',', '.') }}</span>
                </div>

                {{-- PPN = (subtotalAll - diskon) × tax_percentage% → tersimpan di tax_amount --}}
                <div class="d-flex justify-content-between align-items-center px-3 py-2" style="border-bottom:1px solid #f1f5f9;">
                    <span style="font-size:13px;color:#475569;">PPN ({{ $invoice->tax_percentage }}%)</span>
                    <span class="total-value" style="font-size:13px;">Rp {{ number_format($invoice->tax_amount ?? 0, 0, ',', '.') }}</span>
                </div>

                {{-- Amount Total = (subtotalAll - diskon) + PPN → tersimpan di total --}}
                <div class="d-flex justify-content-between align-items-center px-3 py-3" style="background:#f8faff;border-radius:0 0 .5rem .5rem;">
                    <strong style="font-size:15px;">Amount Total</strong>
                    <strong class="total-value" style="font-size:17px;">Rp {{ number_format($invoice->total ?? 0, 0, ',', '.') }}</strong>
                </div>

            </div>
        </div>

    </div>{{-- end right --}}
</div>
@endsection