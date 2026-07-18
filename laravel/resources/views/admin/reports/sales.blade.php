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
            Ringkasan nilai project dari Sales Order yang telah dibuat
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
    $grandTotal = $salesOrders->sum('grandtotal');
@endphp
<div class="grand-total-card mb-4 d-flex align-items-center justify-content-between">
    <div>
        <div class="label">Grand Total Nilai Project</div>
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

        {{-- Filter Invoice Status --}}
        <div class="col-6 col-sm-6 col-md-3 col-lg-2">
            <div class="filter-label">Status Invoice</div>
            <select name="invoice_status" class="form-select form-select-sm">
                <option value="">Semua</option>
                @foreach($invoiceStatuses as $v => $l)
                    <option value="{{ $v }}" {{ request('invoice_status') === $v ? 'selected' : '' }}>{{ $l }}</option>
                @endforeach
            </select>
        </div>

        {{-- Filter Payment Status --}}
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
                    <th>No</th>
                    <th>Nomor Sales Order</th>
                    <th>Nama Klien</th>
                    <th>Nomor PO</th>
                    <th>Nama Project</th>
                    <th>Tanggal SO</th>
                    <th>Status Invoice</th>
                    <th style="text-align:right;">Nilai Project</th>
                </tr>
            </thead>
            <tbody>
                @foreach($salesOrders as $so)
                @php
                    // ── Invoice ──
                    $invColl = $invoices->get($so->nomor_salesorder, collect());
                    $inv     = $invColl->first();
                    $invStatus = $inv ? $inv->status_pembayaran : '-';
                    $invBadge  = $invStatus === 'paid' ? 'success' :
                                 ($invStatus === 'sent' ? 'primary' :
                                 ($invStatus === 'overdue' ? 'danger' :
                                 ($invStatus === 'draft' ? 'secondary' : 'danger')));
                    $clientName = $so->client ? $so->client->nama_perusahaan : '-';
                    $clientContact = $so->client ? $so->client->nama_kontak : '-';
                @endphp
                <tr>
                    <td>{{ $loop->iteration + ($salesOrders->currentPage() - 1) * $salesOrders->perPage() }}</td>
                    <td>
                        <span style="font-family:monospace;color:#1B5DBC;font-weight:600;">{{ $so->nomor_salesorder }}</span>
                    </td>
                    <td>
                        <div>{{ $clientName }}</div>
                        <div class="text-muted" style="font-size:12px">{{ $clientContact }}</div>
                    </td>
                    <td>
                        <span style="font-family:monospace;">{{ $so->nomor_po ?: '-' }}</span>
                    </td>
                    <td>
                        <div class="truncate-text" title="{{ $so->nama_project ?: '-' }}">{{ $so->nama_project ?: '-' }}</div>
                    </td>
                    <td style="white-space:nowrap;">{{ $so->tanggal_pembuatan->format('d/m/Y') }}</td>
                    <td>
                        @if($inv)
                            <span class="badge bg-{{ $invBadge }}">{{ ucfirst($invStatus) }}</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td style="font-family:monospace;font-weight:600;text-align:right;">
                        Rp {{ number_format($so->grandtotal, 0, ',', '.') }}
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