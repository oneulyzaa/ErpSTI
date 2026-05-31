@extends('layouts.app')
@section('title', 'Rencana Produksi')
@section('breadcrumb', 'Produksi')

@push('styles')
<style>
    .badge-planned     { background:#e2e8f0; color:#475569; }
    .badge-in_progress { background:#fef3c7; color:#92400e; }
    .badge-completed   { background:#dcfce7; color:#15803d; }
    .badge-cancelled   { background:#fee2e2; color:#b91c1c; }
    .prd-no { font-family: monospace; font-size: 13px; font-weight: 600; color: #1B5DBC; }
    .table-actions .btn { padding: 3px 9px; font-size: 12px; }
</style>
@endpush

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Rencana Produksi</h4>
        <p class="text-muted mb-0" style="font-size:13px">Kelola bahan baku untuk setiap produk dari Sales Order</p>
    </div>
    <a href="{{ route('admin.productions.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
        <i class="bi bi-plus-lg"></i> Buat Rencana Produksi
    </a>
</div>

{{-- Filter --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-sm-5 col-md-4">
                <label class="form-label fw-semibold mb-1" style="font-size:13px">Cari</label>
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="No. Produksi, SO, project, klien..." value="{{ request('search') }}">
            </div>
            <div class="col-6 col-sm-3 col-md-2">
                <label class="form-label fw-semibold mb-1" style="font-size:13px">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    @foreach(['planned'=>'Planned','in_progress'=>'In Progress','completed'=>'Completed','cancelled'=>'Cancelled'] as $v => $l)
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
                <a href="{{ route('admin.productions.index') }}" class="btn btn-outline-secondary btn-sm w-100">
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
        <span class="fw-semibold">Daftar Rencana Produksi</span>
        <span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $productions->total() }} data</span>
    </div>

    @if($productions->isEmpty())
    <div class="card-body text-center py-5">
        <i class="bi bi-gear text-muted" style="font-size:48px"></i>
        <div class="mt-3 fw-semibold text-muted">Belum ada Rencana Produksi</div>
        <div class="text-muted mb-3" style="font-size:13px">Buat rencana produksi dari Sales Order yang sudah dikonfirmasi</div>
        <a href="{{ route('admin.productions.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Buat Rencana Produksi
        </a>
    </div>
    @else
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="text-muted fw-semibold" style="font-size:12px;width:40px">#</th>
                    <th class="text-muted fw-semibold" style="font-size:12px">NO. PRODUKSI</th>
                    <th class="text-muted fw-semibold" style="font-size:12px">REF. SO</th>
                    <th class="text-muted fw-semibold" style="font-size:12px">PROJECT</th>
                    <th class="text-muted fw-semibold" style="font-size:12px">KLIEN</th>
                    <th class="text-muted fw-semibold" style="font-size:12px">TANGGAL</th>
                    <th class="text-muted fw-semibold" style="font-size:12px">STATUS</th>
                    <th class="text-muted fw-semibold text-center" style="font-size:12px">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @foreach($productions as $i => $prd)
                @php
                    $statusMap = [
                        'planned'     => ['planned',     'Planned'],
                        'in_progress' => ['in_progress', 'In Progress'],
                        'completed'   => ['completed',   'Completed'],
                        'cancelled'   => ['cancelled',   'Cancelled'],
                    ];
                    $s = $statusMap[$prd->status] ?? ['planned','-'];
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="prd-no">{{ $prd->production_number }}</td>
                    <td>{{ $prd->so_number ?: '-' }}</td>
                    <td>{{ $prd->project_name ?: '-' }}</td>
                    <td>{{ $prd->client_company ?: '-' }}</td>
                    <td>{{ $prd->date->format('d M Y') }}</td>
                    <td><span class="badge badge-{{ $s[0] }}">{{ $s[1] }}</span></td>
                    <td class="text-center table-actions">
                        <a href="{{ route('admin.productions.show', $prd) }}" class="btn btn-info btn-sm"><i class="bi bi-eye"></i></a>
                        <a href="{{ route('admin.productions.edit', $prd) }}" class="btn btn-primary btn-sm"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('admin.productions.destroy', $prd) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus Rencana Produksi ini?')"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white py-3">
        {{ $productions->links() }}
    </div>
    @endif
</div>
@endsection
