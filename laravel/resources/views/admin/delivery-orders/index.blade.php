@extends('layouts.app')
@section('title', 'Daftar Delivery Order')
@section('breadcrumb', 'Delivery Order')

@push('styles')
<style>
    .badge-draft     { background:#e2e8f0; color:#475569; }
    .badge-confirmed { background:#dbeafe; color:#1d4ed8; }
    .badge-shipped   { background:#fef3c7; color:#92400e; }
    .badge-delivered { background:#dcfce7; color:#15803d; }
    .badge-cancelled { background:#fee2e2; color:#b91c1c; }
    .do-no { font-family: monospace; font-size: 13px; font-weight: 600; color: #1B5DBC; }
    .table-actions .btn { padding: 3px 9px; font-size: 12px; }
</style>
@endpush

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Delivery Order</h4>
        <p class="text-muted mb-0" style="font-size:13px">Kelola semua surat jalan (Delivery Order)</p>
    </div>
    <a href="{{ route('admin.delivery-orders.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
        <i class="bi bi-plus-lg"></i> Buat Delivery Order
    </a>
</div>

{{-- Filter --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-sm-5 col-md-4">
                <label class="form-label fw-semibold mb-1" style="font-size:13px">Cari</label>
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="No. DO, klien, perusahaan..." value="{{ request('search') }}">
            </div>
            <div class="col-6 col-sm-3 col-md-2">
                <label class="form-label fw-semibold mb-1" style="font-size:13px">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    @foreach(['draft'=>'Draft','confirmed'=>'Confirmed','shipped'=>'Shipped','delivered'=>'Delivered','cancelled'=>'Cancelled'] as $v => $l)
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
                <a href="{{ route('admin.delivery-orders.index') }}" class="btn btn-outline-secondary btn-sm w-100">
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
        <span class="fw-semibold">Daftar Delivery Order</span>
        <span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $deliveryOrders->total() }} data</span>
    </div>

    @if($deliveryOrders->isEmpty())
    <div class="card-body text-center py-5">
        <i class="bi bi-truck text-muted" style="font-size:48px"></i>
        <div class="mt-3 fw-semibold text-muted">Belum ada Delivery Order</div>
        <div class="text-muted mb-3" style="font-size:13px">Mulai buat Delivery Order pertama Anda</div>
        <a href="{{ route('admin.delivery-orders.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Buat Delivery Order
        </a>
    </div>
    @else
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="text-muted fw-semibold" style="font-size:12px;width:40px">#</th>
                    <th class="text-muted fw-semibold" style="font-size:12px">NO. DO</th>
                    <th class="text-muted fw-semibold" style="font-size:12px">NO. SO</th>
                    <th class="text-muted fw-semibold" style="font-size:12px">KLIEN</th>
                    <th class="text-muted fw-semibold" style="font-size:12px">PERUSAHAAN</th>
                    <th class="text-muted fw-semibold" style="font-size:12px">TANGGAL</th>
                    <th class="text-muted fw-semibold" style="font-size:12px">STATUS</th>
                    <th class="text-muted fw-semibold text-center" style="font-size:12px">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @foreach($deliveryOrders as $i => $do)
                @php
                    $statusMap = [
                        'draft'     => ['draft',     'Draft'],
                        'confirmed' => ['confirmed', 'Confirmed'],
                        'shipped'   => ['shipped',   'Shipped'],
                        'delivered' => ['delivered', 'Delivered'],
                        'cancelled' => ['cancelled', 'Cancelled'],
                    ];
                    $s = $statusMap[$do->status] ?? ['draft','-'];
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="do-no">{{ $do->nomor_deliveryorder }}</td>
                    <td>{{ $do->nomor_salesorder ?: '-' }}</td>
                    <td>{{ $do->client->nama_kontak ?? '-' }}</td>
                    <td>{{ $do->client->nama_perusahaan ?? '-' }}</td>
                    <td>{{ $do->tanggal_pembuatan ? $do->tanggal_pembuatan->format('d M Y') : '-' }}</td>
                    <td><span class="badge badge-{{ $s[0] }}">{{ $s[1] }}</span></td>
                    <td class="text-center table-actions">
                        <a href="{{ route('admin.delivery-orders.show', $do) }}" class="btn btn-info btn-sm"><i class="bi bi-eye"></i></a>
                        <a href="{{ route('admin.delivery-orders.edit', $do) }}" class="btn btn-primary btn-sm"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('admin.delivery-orders.destroy', $do) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus Delivery Order ini?')"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white py-3">
        {{ $deliveryOrders->links() }}
    </div>
    @endif
</div>
@endsection
