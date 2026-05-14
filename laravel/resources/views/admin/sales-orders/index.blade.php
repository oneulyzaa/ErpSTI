@extends('layouts.app')
@section('title', 'Daftar Sales Order')
@section('breadcrumb', 'Sales Order')

@push('styles')
<style>
    .badge-draft       { background:#e2e8f0; color:#475569; }
    .badge-confirmed   { background:#dbeafe; color:#1d4ed8; }
    .badge-in_progress { background:#fef3c7; color:#92400e; }
    .badge-completed   { background:#dcfce7; color:#15803d; }
    .badge-cancelled   { background:#fee2e2; color:#b91c1c; }
    .so-no { font-family: monospace; font-size: 13px; font-weight: 600; color: #1B5DBC; }
    .table-actions .btn { padding: 3px 9px; font-size: 12px; }
</style>
@endpush

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Sales Order</h4>
        <p class="text-muted mb-0" style="font-size:13px">Kelola semua pesanan penjualan (Sales Order)</p>
    </div>
    <a href="{{ route('admin.sales-orders.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
        <i class="bi bi-plus-lg"></i> Buat Sales Order
    </a>
</div>

{{-- Filter --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-sm-5 col-md-4">
                <label class="form-label fw-semibold mb-1" style="font-size:13px">Cari</label>
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="No. SO, klien, project..." value="{{ request('search') }}">
            </div>
            <div class="col-6 col-sm-3 col-md-2">
                <label class="form-label fw-semibold mb-1" style="font-size:13px">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    @foreach(['draft'=>'Draft','confirmed'=>'Confirmed','in_progress'=>'In Progress','completed'=>'Completed','cancelled'=>'Cancelled'] as $v => $l)
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
                <a href="{{ route('admin.sales-orders.index') }}" class="btn btn-outline-secondary btn-sm w-100">
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
        <span class="fw-semibold">Daftar Sales Order</span>
        <span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $salesOrders->total() }} data</span>
    </div>

    @if($salesOrders->isEmpty())
    <div class="card-body text-center py-5">
        <i class="bi bi-cart-check text-muted" style="font-size:48px"></i>
        <div class="mt-3 fw-semibold text-muted">Belum ada Sales Order</div>
        <div class="text-muted mb-3" style="font-size:13px">Mulai buat Sales Order pertama Anda</div>
        <a href="{{ route('admin.sales-orders.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Buat Sales Order
        </a>
    </div>
    @else
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="text-muted fw-semibold" style="font-size:12px;width:40px">#</th>
                    <th class="text-muted fw-semibold" style="font-size:12px">NO. SO</th>
                    <th class="text-muted fw-semibold" style="font-size:12px">PROJECT</th>
                    <th class="text-muted fw-semibold" style="font-size:12px">KLIEN</th>
                    <th class="text-muted fw-semibold" style="font-size:12px">TANGGAL</th>
                    <th class="text-muted fw-semibold text-end" style="font-size:12px">TOTAL</th>
                    <th class="text-muted fw-semibold" style="font-size:12px">STATUS</th>
                    <th class="text-muted fw-semibold text-center" style="font-size:12px">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @foreach($salesOrders as $i => $so)
                @php
                    $statusMap = [
                        'draft'       => ['draft',       'Draft'],
                        'confirmed'   => ['confirmed',   'Confirmed'],
                        'in_progress' => ['in_progress', 'In Progress'],
                        'completed'   => ['completed',   'Completed'],
                        'cancelled'   => ['cancelled',   'Cancelled'],
                    ];
                    $s = $statusMap[$so->status] ?? ['draft','-'];
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="so-no">{{ $so->so_number }}</td>
                    <td>{{ $so->project_name ?: '-' }}</td>
                    <td>{{ $so->client_name }}</td>
                    <td>{{ $so->date->format('d M Y') }}</td>
                    <td class="text-end">{{ number_format($so->total,0,',','.') }}</td>
                    <td><span class="badge badge-{{ $s[0] }}">{{ $s[1] }}</span></td>
                    <td class="text-center table-actions">
                        <a href="{{ route('admin.sales-orders.show', $so) }}" class="btn btn-info btn-sm"><i class="bi bi-eye"></i></a>
                        <a href="{{ route('admin.sales-orders.edit', $so) }}" class="btn btn-primary btn-sm"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('admin.sales-orders.destroy', $so) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus Sales Order ini?')"><i class="bi bi-trash"></i></button>
                        </form>
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
