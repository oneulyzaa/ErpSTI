@extends('layouts.app')
@section('title', 'Detail ' . $production->production_number)
@section('breadcrumb', 'Detail Produksi')

@push('styles')
<style>
    .badge-planned     { background:#e2e8f0; color:#475569; }
    .badge-in_progress { background:#fef3c7; color:#92400e; }
    .badge-completed   { background:#dcfce7; color:#15803d; }
    .badge-cancelled   { background:#fee2e2; color:#b91c1c; }
    .badge-pending        { background:#e2e8f0; color:#475569; }
    .badge-pending_item   { background:#e2e8f0; color:#475569; }
    .badge-in_progress_item { background:#fef3c7; color:#92400e; }
    .badge-completed_item   { background:#dcfce7; color:#15803d; }
    .mat-table-header th { background: #2c4f8a !important; color: #fff !important; font-size: 10px; text-transform: uppercase; }
</style>
@endpush

@section('content')

<div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h4 class="fw-bold mb-0" style="font-family:monospace">{{ $production->production_number }}</h4>
            <span class="badge badge-{{ $production->status }} rounded-pill px-2 py-1">{{ ucfirst(str_replace('_',' ',$production->status)) }}</span>
        </div>
        <p class="text-muted mb-0" style="font-size:13px">
            Tanggal: {{ $production->date->format('d M Y') }}
            @if($production->target_date)
                &nbsp;·&nbsp; Target: {{ $production->target_date->format('d M Y') }}
            @endif
            @if($production->so_number)
                &nbsp;·&nbsp; Ref. SO: {{ $production->so_number }}
            @endif
        </p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('admin.productions.pdf', $production) }}" target="_blank"
           class="btn btn-success btn-sm d-flex align-items-center gap-2 px-3">
            <i class="bi bi-file-earmark-pdf-fill"></i> Cetak PDF
        </a>
        <a href="{{ route('admin.productions.edit', $production) }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <a href="{{ route('admin.productions.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <form action="{{ route('admin.productions.destroy', $production) }}" method="POST"
              onsubmit="return confirm('Hapus Rencana Produksi ini?')">
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
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;margin-bottom:10px;">Info Produksi</div>
                        <div class="fw-bold" style="font-size:15px">{{ $production->project_name ?: '-' }}</div>
                        <div class="text-muted mt-1" style="font-size:13px;line-height:1.8;">
                            Klien: {{ $production->client_company ?: '-' }}<br>
                            No. Produksi: {{ $production->production_number }}<br>
                            Ref. SO: {{ $production->so_number ?: '-' }}
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 p-4">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;margin-bottom:10px;">PT. Sistem Teknologi Integrator</div>
                        <div class="text-muted mt-1" style="font-size:13px;line-height:1.8;">
                            Ruko Palazo Blok AB 46, Ciantra<br>
                            Cikarang Selatan, Bekasi 17530
                        </div>
                    </div>
                </div>
                @if($production->notes)
                <div class="p-4 border-top bg-light">
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;margin-bottom:6px;">Catatan</div>
                    <div style="font-size:14px;white-space:pre-line;">{{ $production->notes }}</div>
                </div>
                @endif
            </div>
        </div>

        {{-- Products --}}
        @foreach($production->items as $pi => $product)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-semibold">#{{ $loop->iteration }} {{ $product->product_name }}</span>
                    <span class="badge badge-{{ $product->status }}_item">{{ ucfirst(str_replace('_',' ',$product->status)) }}</span>
                </div>
                <span class="text-muted" style="font-size:13px;">Qty: {{ number_format($product->product_qty, 2) }} {{ $product->unit }}</span>
            </div>
            @if($product->materials->isNotEmpty())
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size:12px;">
                    <thead>
                        <tr class="mat-table-header">
                            <th style="width:36px;">#</th>
                            <th>Bahan Baku</th>
                            <th style="width:80px;text-align:center;">Satuan</th>
                            <th style="width:120px;text-align:right;">Qty Dibutuhkan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($product->materials as $mi => $mat)
                        <tr>
                            <td class="text-center text-muted" style="font-family:monospace;">{{ $loop->iteration }}</td>
                            <td class="fw-semibold">{{ $mat->nama_bahan_baku }}</td>
                            <td class="text-center">{{ $mat->satuan }}</td>
                            <td class="text-end" style="font-family:monospace;">{{ number_format($mat->qty_required, 2, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="card-body text-muted" style="font-size:13px;">
                <i class="bi bi-info-circle"></i> Belum ada bahan baku yang ditentukan.
            </div>
            @endif
        </div>
        @endforeach

    </div>

    {{-- RIGHT --}}
    <div class="col-12 col-xl-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3"><span class="fw-semibold">Ringkasan</span></div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2" style="font-size:13px;">
                    <span>Jumlah Produk</span>
                    <span class="fw-semibold">{{ $production->items->count() }}</span>
                </div>
                @php
                    $totalMaterials = $production->items->sum(fn($p) => $p->materials->count());
                @endphp
                <div class="d-flex justify-content-between mb-2" style="font-size:13px;">
                    <span>Total Jenis Bahan Baku</span>
                    <span class="fw-semibold">{{ $totalMaterials }}</span>
                </div>
            </div>
            <div class="card-footer bg-white border-top">
                <a href="{{ route('admin.productions.pdf', $production) }}" target="_blank"
                   class="btn btn-success w-100 d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-file-earmark-pdf-fill"></i> Cetak / Download PDF
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
