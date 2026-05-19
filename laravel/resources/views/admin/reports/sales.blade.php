@extends('layouts.app')
@section('title', 'Laporan Penjualan')
@section('breadcrumb', 'Lap. Penjualan')

@push('styles')
<style>
    .badge-so-draft       { background:#e2e8f0; color:#475569; }
    .badge-so-confirmed   { background:#dbeafe; color:#1d4ed8; }
    .badge-so-in_progress { background:#fef3c7; color:#92400e; }
    .badge-so-completed   { background:#dcfce7; color:#15803d; }
    .badge-so-cancelled   { background:#fee2e2; color:#b91c1c; }

    .progress-module {
        font-size: 12px;
        padding: 2px 8px;
        border-radius: 4px;
        display: inline-block;
        white-space: nowrap;
    }
    .status-dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 4px;
    }
    .report-table th {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #64748b;
        background: #f8fafc;
        white-space: nowrap;
    }
    .report-table td {
        font-size: 13px;
        vertical-align: middle;
    }
    .truncate-text {
        max-width: 160px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .filter-section {
        background: #f8fafc;
        border-radius: 8px;
        padding: 16px 20px;
    }
    .filter-label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: #94a3b8;
        margin-bottom: 4px;
    }
    .grand-total-card {
        background: linear-gradient(135deg, #1e3a5f, #1B5DBC);
        color: #fff;
        border-radius: 10px;
        padding: 16px 24px;
    }
    .grand-total-card .label {
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .06em;
        opacity: .8;
    }
    .grand-total-card .value {
        font-size: 28px;
        font-weight: 700;
        font-family: monospace;
    }
</style>
@endpush

@section('content')

{{-- ── HEADER ─────────────────────────────────────────── --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Laporan Penjualan</h4>
        <p class="text-muted mb-0" style="font-size:13px">
      Ringkasan penjualan dari Quotation → Sales Order → Produksi → Delivery Order → Invoice → Pembayaran
        </p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('admin.reports.sales.pdf', request()->query()) }}" class="btn btn-danger btn-sm d-flex align-items-center gap-1" target="_blank">
            <i class="bi bi-file-pdf"></i> Download PDF
        </a>
        <a href="{{ route('admin.reports.sales.excel', request()->query()) }}" class="btn btn-success btn-sm d-flex align-items-center gap-1">
            <i class="bi bi-file-earmark-excel"></i> Download XLS
        </a>
        <span class="badge bg-secondary bg-opacity-10 text-secondary fs-6 px-3 py-2">
            {{ $salesOrders->total() }} Sales Order
        </span>
    </div>
</div>

{{-- ── GRAND TOTAL ─────────────────────────────────────── --}}
@php
    $grandTotal = $salesOrders->sum('total');
@endphp
<div class="grand-total-card mb-4 d-flex align-items-center justify-content-between">
    <div>
        <div class="label">Grand Total Sales Order</div>
        <div class="value">Rp {{ number_format($grandTotal, 0, ',', '.') }}</div>
    </div>
    <div style="font-size:13px;opacity:.8;">
        {{ $salesOrders->firstItem() ?? 0 }}–{{ $salesOrders->lastItem() ?? 0 }} dari {{ $salesOrders->total() }} data
    </div>
</div>

{{-- ── FILTERS ─────────────────────────────────────────── --}}
<form method="GET" class="filter-section mb-4">
    <div class="row g-3 align-items-end">

        {{-- Tanggal --}}
        <div class="col-12 col-sm-6 col-md-3 col-lg-2">
            <div class="filter-label">Dari Tanggal</div>
            <input type="date" name="date_from" class="form-control form-control-sm"
                   value="{{ $dateFrom ?? '' }}">
        </div>
        <div class="col-12 col-sm-6 col-md-3 col-lg-2">
            <div class="filter-label">Sampai Tanggal</div>
            <input type="date" name="date_to" class="form-control form-control-sm"
                   value="{{ $dateTo ?? '' }}">
        </div>

        {{-- Pencarian --}}
        <div class="col-12 col-sm-6 col-md-3 col-lg-2">
            <div class="filter-label">Cari</div>
            <input type="text" name="search" class="form-control form-control-sm"
                   placeholder="No. SO, Proyek, Klien..." value="{{ request('search') }}">
        </div>

        {{-- Status per Modul --}}
        <div class="col-6 col-sm-6 col-md-3 col-lg-2">
            <div class="filter-label">Quotation</div>
            <select name="quotation_status" class="form-select form-select-sm">
                <option value="">Semua</option>
                @foreach($quotationStatuses as $v => $l)
                    <option value="{{ $v }}" {{ request('quotation_status') === $v ? 'selected' : '' }}>{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-sm-6 col-md-3 col-lg-2">
            <div class="filter-label">Sales Order</div>
            <select name="so_status" class="form-select form-select-sm">
                <option value="">Semua</option>
                @foreach($soStatuses as $v => $l)
                    <option value="{{ $v }}" {{ request('so_status') === $v ? 'selected' : '' }}>{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-sm-6 col-md-3 col-lg-2">
            <div class="filter-label">Produksi</div>
            <select name="production_status" class="form-select form-select-sm">
                <option value="">Semua</option>
                @foreach($productionStatuses as $v => $l)
                    <option value="{{ $v }}" {{ request('production_status') === $v ? 'selected' : '' }}>{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-sm-6 col-md-3 col-lg-2">
            <div class="filter-label">Delivery Order</div>
            <select name="do_status" class="form-select form-select-sm">
                <option value="">Semua</option>
                @foreach($doStatuses as $v => $l)
                    <option value="{{ $v }}" {{ request('do_status') === $v ? 'selected' : '' }}>{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-sm-6 col-md-3 col-lg-2">
            <div class="filter-label">Invoice</div>
            <select name="invoice_status" class="form-select form-select-sm">
                <option value="">Semua</option>
                @foreach($invoiceStatuses as $v => $l)
                    <option value="{{ $v }}" {{ request('invoice_status') === $v ? 'selected' : '' }}>{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-sm-6 col-md-3 col-lg-2">
            <div class="filter-label">Status Pembayaran</div>
            <select name="payment_status" class="form-select form-select-sm">
                <option value="">Semua</option>
                @foreach($paymentStatuses as $v => $l)
                    <option value="{{ $v }}" {{ request('payment_status') === $v ? 'selected' : '' }}>{{ $l }}</option>
                @endforeach
            </select>
        </div>

        {{-- Tombol --}}
        <div class="col-12 col-sm-6 col-md-3 col-lg-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary btn-sm flex-fill">
                <i class="bi bi-search"></i> Filter
            </button>
            <a href="{{ route('admin.reports.sales') }}" class="btn btn-outline-secondary btn-sm flex-fill">
                <i class="bi bi-x-lg"></i> Reset
            </a>
        </div>
    </div>
</form>

{{-- ── TABLE ───────────────────────────────────────────── --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
        <span class="fw-semibold">Data Penjualan</span>
    </div>

    @if($salesOrders->isEmpty())
    <div class="card-body text-center py-5">
        <i class="bi bi-bar-chart-line text-muted" style="font-size:48px"></i>
        <div class="mt-3 fw-semibold text-muted">Belum ada data penjualan</div>
        <div class="text-muted mb-3" style="font-size:13px">Buat Sales Order terlebih dahulu untuk melihat laporan</div>
    </div>
    @else
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 report-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>No. SO / Proyek</th>
                    <th>Klien</th>
                    <th>Tanggal</th>
                    <th>Quotation</th>
                    <th>SO</th>
                    <th>Produksi</th>
                    <th>DO</th>
                    <th>Invoice</th>
                    <th>Pembayaran</th>
                    <th style="text-align:right;">Nilai (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($salesOrders as $so)
                @php
                    // ── Quotation ──
                    $quo = $so->quotation;
                    $quoStatus = $quo ? $quo->status : '-';
                    $quoBadge  = $quoStatus === 'approved' ? 'success' :
                                 ($quoStatus === 'sent' ? 'primary' :
                                 ($quoStatus === 'rejected' ? 'danger' : 'secondary'));

                    // ── Production (ambil yg terbaru) ──
                    $prodColl = $productions->get($so->id, collect());
                    $prod     = $prodColl->first();
                    $prodStatus = $prod ? $prod->status : '-';
                    $prodBadge  = $prodStatus === 'completed' ? 'success' :
                                  ($prodStatus === 'in_progress' ? 'warning' :
                                  ($prodStatus === 'planned' ? 'info' : 'secondary'));

                    // ── Delivery Order ──
                    $doColl = $deliveryOrders->get($so->id, collect());
                    $do     = $doColl->first();
                    $doStatus = $do ? $do->status : '-';
                    $doBadge  = $doStatus === 'delivered' ? 'success' :
                                ($doStatus === 'shipped' ? 'primary' :
                                ($doStatus === 'confirmed' ? 'info' : 'secondary'));

                    // ── Invoice ──
                    $invColl = $invoices->get($so->id, collect());
                    $inv     = $invColl->first();
                    $invStatus = $inv ? $inv->status : '-';
                    $invBadge  = $invStatus === 'paid' ? 'success' :
                                 ($invStatus === 'sent' ? 'primary' :
                                 ($invStatus === 'overdue' ? 'danger' :
                                 ($invStatus === 'draft' ? 'secondary' : 'danger')));

                    // ── Payment (Receipt) ──
                    $totalPaid  = 0;
                    $totalInv   = 0;
                    if ($inv) {
                        $totalInv  = $inv->total;
                        $totalPaid = $inv->receipts->where('status', 'confirmed')->sum('amount');
                    }
                    $payStatus  = $totalInv > 0 && $totalPaid >= $totalInv ? 'Lunas' :
                                  ($totalPaid > 0 ? 'Sebagian (Rp '.number_format($totalPaid,0,',','.').')' : 'Belum');
                    $payBadge   = $totalPaid >= $totalInv && $totalInv > 0 ? 'success' :
                                  ($totalPaid > 0 ? 'warning' : 'danger');
                @endphp
                <tr>
                    <td>{{ $loop->iteration + ($salesOrders->currentPage() - 1) * $salesOrders->perPage() }}</td>
                    <td>
                        <div class="fw-semibold" style="font-family:monospace;color:#1B5DBC;">{{ $so->so_number }}</div>
                        <div class="text-muted truncate-text" style="font-size:12px">{{ $so->project_name ?: '-' }}</div>
                    </td>
                    <td>
                        <div>{{ $so->client_company }}</div>
                        <div class="text-muted" style="font-size:12px">{{ $so->client_name ?: '-' }}</div>
                    </td>
                    <td style="white-space:nowrap;">{{ $so->date->format('d/m/Y') }}</td>

                    {{-- Quotation --}}
                    <td>
                        @if($quo)
                            <span class="badge bg-{{ $quoBadge }}">{{ ucfirst($quoStatus) }}</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>

                    {{-- SO --}}
                    <td>
                        @php
                            $soBadge = $so->status === 'completed' ? 'success' :
                                       ($so->status === 'in_progress' ? 'warning' :
                                       ($so->status === 'confirmed' ? 'info' :
                                       ($so->status === 'cancelled' ? 'danger' : 'secondary')));
                        @endphp
                        <span class="badge bg-{{ $soBadge }}">{{ ucfirst(str_replace('_', ' ', $so->status)) }}</span>
                    </td>

                    {{-- Produksi --}}
                    <td>
                        @if($prod)
                            <span class="badge bg-{{ $prodBadge }}">{{ ucfirst(str_replace('_', ' ', $prodStatus)) }}</span>
                            @if($prodColl->count() > 1)
                                <span class="text-muted" style="font-size:11px;">+{{ $prodColl->count()-1 }} lagi</span>
                            @endif
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>

                    {{-- DO --}}
                    <td>
                        @if($do)
                            <span class="badge bg-{{ $doBadge }}">{{ ucfirst(str_replace('_', ' ', $doStatus)) }}</span>
                            @if($doColl->count() > 1)
                                <span class="text-muted" style="font-size:11px;">+{{ $doColl->count()-1 }} lagi</span>
                            @endif
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>

                    {{-- Invoice --}}
                    <td>
                        @if($inv)
                            <span class="badge bg-{{ $invBadge }}">{{ ucfirst($invStatus) }}</span>
                            @if($invColl->count() > 1)
                                <span class="text-muted" style="font-size:11px;">+{{ $invColl->count()-1 }} lagi</span>
                            @endif
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>

                    {{-- Payment --}}
                    <td>
                        @if($totalInv > 0)
                            <span class="badge bg-{{ $payBadge }}">{{ $payStatus }}</span>
                            <div style="font-size:11px;color:#94a3b8;margin-top:2px;">
                                Rp {{ number_format($totalPaid,0,',','.') }} / Rp {{ number_format($totalInv,0,',','.') }}
                            </div>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>

                    <td style="font-family:monospace;font-weight:600;text-align:right;">
                        Rp {{ number_format($so->total, 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-footer bg-white py-3">
        {{ $salesOrders->links() }}
    </div>
    @endif
</div>

@endsection
