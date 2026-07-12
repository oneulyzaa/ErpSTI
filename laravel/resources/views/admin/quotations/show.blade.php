@extends('layouts.app')
@section('title', 'Detail ' . $quotation->nomor_quotation)
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
    .mat-section-header th { background:#2c4f8a !important; color:#fff !important; font-size:10px; text-transform:uppercase; }
    .mat-table {
        border-collapse: collapse;
        width: 100%;
        font-size: 12.5px;
    }
    .mat-table thead th {
        background: #f8faff;
        color: #64748b;
        font-size: 10.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .04em;
        padding: 8px 10px;
        border: none;
        border-bottom: 1.5px solid #e2e8f0;
    }
    .mat-table tbody td {
        padding: 9px 10px;
        border: none;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
        vertical-align: middle;
    }
    .mat-table tbody tr:last-child td {
        border-bottom: none;
    }
    .mat-table tbody tr:hover td {
        background: #f8faff;
    }
    .mat-table .mat-num {
        font-family: monospace;
    }
    .mat-wrap {
        border: 1px solid #eef1f6;
        border-radius: 10px;
        overflow: hidden;
    }
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
            <h4 class="fw-bold mb-0" style="font-family:monospace">{{ $quotation->nomor_quotation }}</h4>
            <span class="badge badge-{{ $s[0] }} rounded-pill px-2 py-1">{{ $s[1] }}</span>
        </div>
        <p class="text-muted mb-0" style="font-size:13px">
            Tanggal: {{ $quotation->tanggal_pembuatan->format('d M Y') }} &nbsp;·&nbsp; Berlaku s/d: {{ $quotation->valid_sampai->format('d M Y') }}
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
                            $clientName = $quotation->client?->nama_perusahaan ?? '-';
                            $contactName = $quotation->client?->nama_kontak ?? '-';
                            $contactEmail = $quotation->client?->email_perusahaan ?? '';
                            $contactAddress = $quotation->client?->alamat_perusahaan ?? '';
                        @endphp
                        <div class="fw-bold" style="font-size:15px">{{ $clientName }}</div>
                        <div class="text-muted mt-1" style="font-size:13px;line-height:1.8;">
                            Kontak: {{ $contactName }}<br>
                            @if($contactEmail){{ $contactEmail }}<br>@endif
                            @if($contactAddress){{ $contactAddress }}@endif
                        </div>
                    </div>
                </div>
                @if($quotation->nama_project)
                <div class="p-4 border-top" style="background:#f8faff;">
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;margin-bottom:6px;">Nama Project</div>
                    <div style="font-size:14px;font-weight:600;">{{ $quotation->nama_project }}</div>
                </div>
                @endif
                @if($quotation->keterangan)
                <div class="p-4 border-top bg-light">
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;margin-bottom:6px;">Keterangan</div>
                    <div style="font-size:14px;">{{ $quotation->keterangan }}</div>
                </div>
                @endif
            </div>
        </div>

        {{-- Produk / Items --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header border-bottom py-3 d-flex align-items-center justify-content-between text-white" style="background:#1B5DBC;">
                <span class="fw-semibold">Produk</span>
                <span class="badge bg-white bg-opacity-25 text-white">{{ $quotation->items->count() }} produk</span>
            </div>
            <div class="card-body p-0">
                @foreach($quotation->items as $i => $item)
                @php
                    $itemSubtotal = ($item->jumlah_item ?? 0) * ($item->harga_item ?? 0);
                @endphp
                <div class="p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div class="d-flex align-items-start justify-content-between mb-2">
                        <div>
                            <span class="fw-bold" style="font-size:14px;">#{{ $i+1 }} {{ $item->nama_item }}</span>
                            @if($item->deskripsi_item)
                            <small class="text-muted d-block">{{ $item->deskripsi_item }}</small>
                            @endif
                        </div>
                        <div class="text-end" style="font-size:13px;">
                            <span class="text-muted">{{ number_format($item->jumlah_item, 2, ',', '.') }} {{ $item->satuan }}</span>
                            &nbsp;&nbsp;
                            {{-- Hanya tampilkan unit price & subtotal jika TIDAK punya materials --}}
                            @unless($item->materials && $item->materials->count())
                            x <span>Rp {{ number_format($item->harga_item, 0, ',', '.') }}</span>
                            &nbsp;=&nbsp;
                            <span class="fw-bold" style="font-family:monospace;">Rp {{ number_format($itemSubtotal, 0, ',', '.') }}</span>
                            @endunless

                            {{-- Jika punya materials, tampilkan total materials sebagai subtotal produk --}}
                            @if($item->materials && $item->materials->count())
                            &nbsp;
                            <span class="fw-bold" style="font-family:monospace;">
                                = Rp {{ number_format($item->materials->sum(fn($m) => ($m->jumlah_material ?? 0) * ($m->harga_material ?? 0)), 0, ',', '.') }}
                            </span>
                            @endif
                        </div>
                    </div>

                    {{-- Materials under this product --}}
                    @if($item->materials && $item->materials->count())
                    <div class="ms-3 mt-3">
                        <div style="font-size:10.5px;font-weight:700;color:#1B5DBC;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px;">Material</div>
                        <div class="mat-wrap">
                            <table class="mat-table">
                                <thead>
                                    <tr>
                                        <th style="width:28px;">#</th>
                                        <th>Nama Material</th>
                                        <th style="width:70px;text-align:center;">Satuan</th>
                                        <th style="width:80px;text-align:right;">Qty</th>
                                        <th style="width:130px;text-align:right;">Harga Satuan</th>
                                        <th style="width:155px;text-align:right;">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($item->materials as $mi => $mat)
                                    @php
                                        $matSubtotal = ($mat->jumlah_material ?? 0) * ($mat->harga_material ?? 0);
                                    @endphp
                                    <tr>
                                        <td class="mat-num text-center text-muted">{{ $mi+1 }}</td>
                                        <td class="fw-medium">{{ $mat->nama_material }}</td>
                                        <td class="text-center text-muted">{{ $mat->satuan_material }}</td>
                                        <td class="mat-num text-end">{{ number_format($mat->jumlah_material, 2, ',', '.') }}</td>
                                        <td class="mat-num text-end text-muted">Rp {{ number_format($mat->harga_material, 0, ',', '.') }}</td>
                                        <td class="mat-num text-end fw-semibold">Rp {{ number_format($matSubtotal, 0, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="5" class="text-end fw-semibold" style="padding:9px 10px;background:#f8faff;border-top:1.5px solid #e2e8f0;font-size:11.5px;color:#475569;">Subtotal Material</td>
                                        <td class="mat-num text-end fw-bold" style="padding:9px 10px;background:#f8faff;border-top:1.5px solid #e2e8f0;color:#1B5DBC;">Rp {{ number_format($item->materials->sum(fn($m) => ($m->jumlah_material ?? 0) * ($m->harga_material ?? 0)), 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    @endif

                </div>
                @endforeach

                <div class="p-3 bg-light border-top">
                    <div class="d-flex justify-content-between">
                        <span class="fw-semibold">Total Produksi</span>
                        <span class="fw-bold" style="font-family:monospace;">Rp {{ number_format($quotation->subtotal_material, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Labor --}}
        @if($quotation->labors && $quotation->labors->count())
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
                            <th style="width:36px;" class="text-muted fw-semibold">#</th>
                            <th class="text-muted fw-semibold">PEKERJAAN</th>
                            <th class="text-muted fw-semibold text-center">MP</th>
                            <th class="text-muted fw-semibold text-center">DAYS</th>
                            <th class="text-muted fw-semibold text-end">RATE / HARI</th>
                            <th class="text-muted fw-semibold text-end">SUBTOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($quotation->labors as $i => $labor)
                        @php
                            $laborSubtotal = ($labor->jumlah_sdm ?? 0) * ($labor->jumlah_hari ?? 0) * ($labor->rate_hari ?? 0);
                        @endphp
                        <tr>
                            <td class="text-center text-muted" style="font-family:monospace;font-size:12px;">{{ $i+1 }}</td>
                            <td class="fw-semibold">{{ $labor->nama_labor }}</td>
                            <td class="text-center" style="font-family:monospace;">{{ $labor->jumlah_sdm }}</td>
                            <td class="text-center" style="font-family:monospace;">{{ number_format($labor->jumlah_hari, 0) }}</td>
                            <td class="text-end" style="font-family:monospace;">Rp {{ number_format($labor->rate_hari, 0, ',', '.') }}</td>
                            <td class="text-end fw-semibold" style="font-family:monospace;">Rp {{ number_format($laborSubtotal, 0, ',', '.') }}</td>
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
        @endif
        {{-- Biaya Lain-Lain --}}
        @if($quotation->otherCosts && $quotation->otherCosts->count())
        <div class="card border-0 shadow-sm">
            <div class="card-header py-3 d-flex align-items-center justify-content-between"
                 style="background:#1B5DBC;">
                <span class="fw-semibold text-white">Biaya Lain-Lain</span>
                <span class="badge bg-white bg-opacity-25 text-white">{{ $quotation->otherCosts->count() }} item</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="font-size:13px;">
                    <thead class="table-light">
                        <tr>
                            <th style="width:36px;" class="text-muted fw-semibold">#</th>
                            <th class="text-muted fw-semibold">NAMA BIAYA</th>
                            <th class="text-muted fw-semibold text-end">JUMLAH</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($quotation->otherCosts as $i => $cost)
                        <tr>
                            <td class="text-center text-muted" style="font-family:monospace;font-size:12px;">{{ $i+1 }}</td>
                            <td class="fw-semibold">{{ $cost->nama_biaya }}</td>
                            <td class="text-end fw-semibold" style="font-family:monospace;">Rp {{ number_format($cost->jumlah_biaya, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="2" class="text-end fw-semibold" style="font-size:12px;">Total Biaya Lain-Lain</td>
                            <td class="text-end fw-bold" style="font-family:monospace;">Rp {{ number_format($quotation->subtotal_lainlain, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        @endif

    </div>

    {{-- RIGHT --}}
    <div class="col-12 col-xl-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3"><span class="fw-semibold">Ringkasan</span></div>
            <div class="card-body">
                <div class="summary-row"><span>Total Produksi</span><span class="summary-val">Rp {{ number_format($quotation->subtotal_material, 0, ',', '.') }}</span></div>
                @if($quotation->labors && $quotation->labors->count())
                <div class="summary-row"><span>Total Labor</span><span class="summary-val">Rp {{ number_format($quotation->subtotal_labor, 0, ',', '.') }}</span></div>
                @endif
                @if($quotation->otherCosts && $quotation->otherCosts->count())
                <div class="summary-row"><span>Total Biaya Lain-Lain</span><span class="summary-val">Rp {{ number_format($quotation->subtotal_lainlain, 0, ',', '.') }}</span></div>
                @endif
                <div class="summary-row"><span>Diskon</span><span class="summary-val">Rp {{ number_format($quotation->diskon ?? 0, 0, ',', '.') }}</span></div>
                <div class="summary-row total-row"><span>GRAND TOTAL</span><span class="summary-val">Rp {{ number_format($quotation->grandtotal, 0, ',', '.') }}</span></div>
            </div>
        </div>

        @if($quotation->termin)
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-white border-bottom py-3"><span class="fw-semibold">Terms & Conditions</span></div>
            <div class="card-body" style="font-size:13px;white-space:pre-line;">{{ $quotation->termin }}</div>
        </div>
        @endif
    </div>
</div>
@endsection