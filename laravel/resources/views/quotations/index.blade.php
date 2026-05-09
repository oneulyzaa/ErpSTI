@extends('layouts.app')
@section('title', 'Daftar Quotation')
@section('breadcrumb', 'Quotation')

@push('styles')
<style>
    .badge-draft    { background:#e2e8f0; color:#475569; }
    .badge-sent     { background:#dbeafe; color:#1d4ed8; }
    .badge-approved { background:#dcfce7; color:#15803d; }
    .badge-rejected { background:#fee2e2; color:#b91c1c; }
    .badge-expired  { background:#fef9c3; color:#92400e; }
    .quote-no { font-family: monospace; font-size: 13px; font-weight: 600; color: #1B5DBC; }
    .table-actions .btn { padding: 3px 9px; font-size: 12px; }
</style>
@endpush

@section('content')

{{-- Header --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Quotation</h4>
        <p class="text-muted mb-0" style="font-size:13px">Kelola semua penawaran harga kepada klien</p>
    </div>
    <a href="{{ route('quotations.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
        <i class="bi bi-plus-lg"></i> Buat Quotation
    </a>
</div>

{{-- Filter --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-sm-5 col-md-4">
                <label class="form-label fw-semibold mb-1" style="font-size:13px">Cari</label>
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="No. quotation, klien..." value="{{ request('search') }}">
            </div>
            <div class="col-6 col-sm-3 col-md-2">
                <label class="form-label fw-semibold mb-1" style="font-size:13px">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    @foreach(['draft'=>'Draft','sent'=>'Terkirim','approved'=>'Disetujui','rejected'=>'Ditolak','expired'=>'Kadaluarsa'] as $v => $l)
                        <option value="{{ $v }}" {{ request('status') === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-sm-auto">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-search"></i> Filter
                </button>
            </div>
            @if(request()->hasAny(['search','status']))
            <div class="col-6 col-sm-auto">
                <a href="{{ route('quotations.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                    <i class="bi bi-x-lg"></i> Reset
                </a>
            </div>
            @endif
        </form>
    </div>
</div>

{{-- Table --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between py-3">
        <span class="fw-semibold">Daftar Quotation</span>
        <span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $quotations->total() }} data</span>
    </div>

    @if($quotations->isEmpty())
    <div class="card-body text-center py-5">
        <i class="bi bi-file-earmark-text text-muted" style="font-size:48px"></i>
        <div class="mt-3 fw-semibold text-muted">Belum ada quotation</div>
        <div class="text-muted mb-3" style="font-size:13px">Mulai buat quotation pertama Anda</div>
        <a href="{{ route('quotations.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Buat Quotation
        </a>
    </div>
    @else
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="text-muted fw-semibold" style="font-size:12px;width:40px">#</th>
                    <th class="text-muted fw-semibold" style="font-size:12px">NO. QUOTATION</th>
                    <th class="text-muted fw-semibold" style="font-size:12px">KLIEN</th>
                    <th class="text-muted fw-semibold" style="font-size:12px">PERUSAHAAN</th>
                    <th class="text-muted fw-semibold" style="font-size:12px">TANGGAL</th>
                    <th class="text-muted fw-semibold" style="font-size:12px">BERLAKU S/D</th>
                    <th class="text-muted fw-semibold text-end" style="font-size:12px">TOTAL</th>
                    <th class="text-muted fw-semibold" style="font-size:12px">STATUS</th>
                    <th class="text-muted fw-semibold text-center" style="font-size:12px">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quotations as $i => $q)
                @php
                    $statusMap = [
                        'draft'    => ['draft',    'Draft'],
                        'sent'     => ['sent',     'Terkirim'],
                        'approved' => ['approved', 'Disetujui'],
                        'rejected' => ['rejected', 'Ditolak'],
                        'expired'  => ['expired',  'Kadaluarsa'],
                    ];
                    $s = $statusMap[$q->status] ?? ['draft','-'];
                @endphp
                <tr>
                    <td class="text-muted" style="font-size:12px">{{ $quotations->firstItem() + $i }}</td>
                    <td>
                        <a href="{{ route('quotations.show', $q) }}" class="quote-no text-decoration-none">
                            {{ $q->quote_number }}
                        </a>
                    </td>
                    <td>{{ $q->client_name }}</td>
                    <td class="text-muted" style="font-size:13px">{{ $q->client_company }}</td>
                    <td style="font-size:13px">{{ $q->date->format('d M Y') }}</td>
                    <td style="font-size:13px;color:{{ $q->valid_until->isPast() && $q->status !== 'approved' ? '#b91c1c' : '' }}">
                        {{ $q->valid_until->format('d M Y') }}
                    </td>
                    <td class="text-end fw-semibold" style="font-family:monospace;font-size:13px">
                        Rp {{ number_format($q->total, 0, ',', '.') }}
                    </td>
                    <td>
                        <span class="badge badge-{{ $s[0] }} rounded-pill px-2 py-1">{{ $s[1] }}</span>
                    </td>
                    <td class="text-center table-actions">
                        <div class="d-flex gap-1 justify-content-center">
                            <a href="{{ route('quotations.show', $q) }}" class="btn btn-outline-secondary btn-sm" title="Lihat">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('quotations.edit', $q) }}" class="btn btn-outline-primary btn-sm" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('quotations.destroy', $q) }}" method="POST"
                                  onsubmit="return confirm('Hapus quotation {{ $q->quote_number }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($quotations->hasPages())
    <div class="card-footer bg-white border-top d-flex justify-content-end py-2">
        {{ $quotations->links() }}
    </div>
    @endif
    @endif
</div>

@endsection
