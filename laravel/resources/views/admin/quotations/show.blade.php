@extends('layouts.app')
@section('title', 'Detail ' . $quotation->quote_number)
@section('breadcrumb', 'Detail Quotation')

@push('styles')
<style>
    .summary-row { display:flex; justify-content:space-between; align-items:center; padding:7px 0; font-size:14px; color:#475569; border-bottom:1px solid #f1f5f9; }
    .summary-row:last-child { border-bottom:none; }
    .summary-row.total-row { font-size:17px; font-weight:700; color:#1e293b; border-top:2px solid #e2e8f0; border-bottom:none; margin-top:4px; padding-top:12px; }
    .summary-val { font-family:monospace; font-size:13px; color:#1e293b; }
    .summary-row.total-row .summary-val { font-size:17px; color:#1B5DBC; }
    .badge-draft    { background:#e2e8f0; color:#475569; }
    .badge-sent     { background:#dbeafe; color:#1d4ed8; }
    .badge-approved { background:#dcfce7; color:#15803d; }
    .badge-rejected { background:#fee2e2; color:#b91c1c; }
    .badge-expired  { background:#fef9c3; color:#92400e; }
</style>
@endpush

@section('content')

@php
    $statusMap = ['draft'=>['draft','Draft'],'sent'=>['sent','Terkirim'],'approved'=>['approved','Disetujui'],'rejected'=>['rejected','Ditolak'],'expired'=>['expired','Kadaluarsa']];
    $s = $statusMap[$quotation->status] ?? ['draft','-'];
@endphp

<div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h4 class="fw-bold mb-0" style="font-family:monospace">{{ $quotation->quote_number }}</h4>
            <span class="badge badge-{{ $s[0] }} rounded-pill px-2 py-1">{{ $s[1] }}</span>
        </div>
        <p class="text-muted mb-0" style="font-size:13px">
            Tanggal: {{ $quotation->date->format('d M Y') }} &nbsp;·&nbsp; Berlaku s/d: {{ $quotation->valid_until->format('d M Y') }}
        </p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('admin.quotations.pdf', $quotation) }}" target="_blank"
           class="btn btn-success btn-sm d-flex align-items-center gap-2 px-3">
            <i class="bi bi-file-earmark-pdf-fill"></i> Cetak PDF
        </a>
        <a href="{{ route('admin.quotations.edit', $quotation) }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <a href="{{ route('admin.quotations.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <form action="{{ route('admin.quotations.destroy', $quotation) }}" method="POST"
              onsubmit="return confirm('Hapus quotation ini?')">
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
                        @php
                            $clientName = $quotation->client?->nama_perusahaan ?? $quotation->client_company;
                            $contactName = $quotation->client?->nama_kontak_perusahaan ?? $quotation->client_name;
                            $contactEmail = $quotation->client?->email_perusahaan ?? $quotation->client_email;
                            $contactAddress = $quotation->client_address ?? ($quotation->client?->alamat_pengiriman_perusahaah ?? '');
                        @endphp
                        <div class="fw-bold" style="font-size:15px">{{ $clientName }}</div>
                        <div class="text-muted mt-1" style="font-size:13px;line-height:1.8;">
                            @if($quotation->client_attention)Attn: {{ $quotation->client_attention }}<br>@endif
                            @if($quotation->client_cc)CC: {{ $quotation->client_cc }}<br>@endif
                            Kontak: {{ $contactName }}<br>
                            @if($contactEmail){{ $contactEmail }}<br>@endif
                            @if($contactAddress){{ $contactAddress }}@endif
                        </div>
                    </div>
                </div>
                @if($quotation->project_name)
                <div class="p-4 border-top" style="background:#f8faff;">
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;margin-bottom:6px;">Nama Project</div>
                    <div style="font-size:14px;font-weight:600;">{{ $quotation->project_name }}</div>
                </div>
                @endif
                @if($quotation->description_of_work)
                <div class="p-4 border-top bg-light">
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;margin-bottom:6px;">Description of Work</div>
                    <div style="font-size:14px;">{{ $quotation->description_of_work }}</div>
                </div>
                @endif
            </div>
        </div>

        {{-- Produksi --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                <span class="fw-semibold">Produksi</span>
                <span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $quotation->items->count() }} item</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="font-size:13px;">
                    <thead class="table-light">
                        <tr>
                            <th style="width:36px;" class="text-muted fw-semibold" style="font-size:11px">#</th>
                            <th class="text-muted fw-semibold" style="font-size:11px">PRODUK / JASA</th>
                            <th class="text-muted fw-semibold" style="font-size:11px">DESKRIPSI</th>
                            <th class="text-muted fw-semibold text-center" style="font-size:11px">SATUAN</th>
                            <th class="text-muted fw-semibold text-end" style="font-size:11px">QTY</th>
                            <th class="text-muted fw-semibold text-end" style="font-size:11px">HARGA SATUAN</th>
                            <th class="text-muted fw-semibold text-end" style="font-size:11px">SUBTOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($quotation->items as $i => $item)
                        <tr>
                            <td class="text-center text-muted" style="font-family:monospace;font-size:12px;">{{ $i+1 }}</td>
                            <td class="fw-semibold">{{ $item->material_name }}</td>
                            <td class="text-muted">{{ $item->description ?: '-' }}</td>
                            <td class="text-center">{{ $item->unit }}</td>
                            <td class="text-end" style="font-family:monospace;">{{ number_format($item->qty, 2, ',', '.') }}</td>
                            <td class="text-end" style="font-family:monospace;">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                            <td class="text-end fw-semibold" style="font-family:monospace;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="6" class="text-end fw-semibold" style="font-size:12px;">Total Produksi</td>
                            <td class="text-end fw-bold" style="font-family:monospace;">Rp {{ number_format($quotation->subtotal_material, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Labor --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header py-3 d-flex align-items-center justify-content-between"
                 style="background:#1B5DBC;">
                <span class="fw-semibold text-white">Labor</span>
                <span class="badge bg-white bg-opacity-25 text-white">{{ $quotation->labors->count() }} item</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="font-size:13px;">
                    <thead class="table-light">
                        <tr>
                            <th style="width:36px;" class="text-muted fw-semibold" style="font-size:11px">#</th>
                            <th class="text-muted fw-semibold" style="font-size:11px">PEKERJAAN</th>
                            <th class="text-muted fw-semibold text-center" style="font-size:11px">MP</th>
                            <th class="text-muted fw-semibold text-center" style="font-size:11px">DAYS</th>
                            <th class="text-muted fw-semibold text-end" style="font-size:11px">RATE / HARI</th>
                            <th class="text-muted fw-semibold text-end" style="font-size:11px">SUBTOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($quotation->labors as $i => $labor)
                        <tr>
                            <td class="text-center text-muted" style="font-family:monospace;font-size:12px;">{{ $i+1 }}</td>
                            <td class="fw-semibold">{{ $labor->labor_name }}</td>
                            <td class="text-center" style="font-family:monospace;">{{ $labor->mp }}</td>
                            <td class="text-center" style="font-family:monospace;">{{ number_format($labor->days, 0) }}</td>
                            <td class="text-end" style="font-family:monospace;">Rp {{ number_format($labor->rate, 0, ',', '.') }}</td>
                            <td class="text-end fw-semibold" style="font-family:monospace;">Rp {{ number_format($labor->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="5" class="text-end fw-semibold" style="font-size:12px;">Total Labor</td>
                            <td class="text-end fw-bold" style="font-family:monospace;">Rp {{ number_format($quotation->subtotal_labor, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>

    {{-- RIGHT --}}
    <div class="col-12 col-xl-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3"><span class="fw-semibold">Ringkasan</span></div>
            <div class="card-body">
                <div class="summary-row"><span>Total Produksi</span><span class="summary-val">Rp {{ number_format($quotation->subtotal_material, 0, ',', '.') }}</span></div>
                <div class="summary-row"><span>Total Labor</span><span class="summary-val">Rp {{ number_format($quotation->subtotal_labor, 0, ',', '.') }}</span></div>
                <div class="summary-row total-row"><span>GRAND TOTAL</span><span class="summary-val">Rp {{ number_format($quotation->total, 0, ',', '.') }}</span></div>
            </div>
            <div class="card-footer bg-white border-top">
                <a href="{{ route('admin.quotations.pdf', $quotation) }}" target="_blank"
                   class="btn btn-success w-100 d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-file-earmark-pdf-fill"></i> Cetak / Download PDF
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
