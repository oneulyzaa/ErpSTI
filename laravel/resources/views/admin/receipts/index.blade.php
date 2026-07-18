@extends('layouts.app')
@section('title', 'Daftar Tanda Terima')
@section('breadcrumb', 'Tanda Terima')

@push('styles')
<style>
    .badge-draft     { background:#e2e8f0; color:#475569; }
    .badge-confirmed { background:#dcfce7; color:#15803d; }
    .badge-cancelled { background:#fee2e2; color:#b91c1c; }
    .tt-no { font-family: monospace; font-size: 13px; font-weight: 600; color: #1B5DBC; }
    .table-actions .btn { padding: 3px 9px; font-size: 12px; }
</style>
@endpush

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Tanda Terima Pembayaran</h4>
        <p class="text-muted mb-0" style="font-size:13px">Kelola semua tanda terima pembayaran</p>
    </div>
    <a href="{{ route('admin.receipts.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
        <i class="bi bi-plus-lg"></i> Buat Tanda Terima
    </a>
</div>

{{-- Filter --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-sm-5 col-md-4">
                <label class="form-label fw-semibold mb-1" style="font-size:13px">Cari</label>
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="No. Tanda Terima, klien, invoice..." value="{{ request('search') }}">
            </div>
            <div class="col-6 col-sm-auto">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-search"></i> Filter
                </button>
            </div>
            @if(request()->hasAny(['search']))
            <div class="col-6 col-sm-auto">
                <a href="{{ route('admin.receipts.index') }}" class="btn btn-outline-secondary btn-sm w-100">
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
        <span class="fw-semibold">Daftar Tanda Terima</span>
        <span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $receipts->total() }} data</span>
    </div>

    @if($receipts->isEmpty())
    <div class="card-body text-center py-5">
        <i class="bi bi-journal-check text-muted" style="font-size:48px"></i>
        <div class="mt-3 fw-semibold text-muted">Belum ada Tanda Terima</div>
        <div class="text-muted mb-3" style="font-size:13px">Mulai buat Tanda Terima pertama</div>
        <a href="{{ route('admin.receipts.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Buat Tanda Terima
        </a>
    </div>
    @else
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="text-muted fw-semibold" style="font-size:12px;width:40px">#</th>
                    <th class="text-muted fw-semibold" style="font-size:12px">NO. TANDA TERIMA</th>
                    <th class="text-muted fw-semibold" style="font-size:12px">NO. INVOICE</th>
                    <th class="text-muted fw-semibold" style="font-size:12px">NO. PO</th>
                    <th class="text-muted fw-semibold" style="font-size:12px">PERUSAHAAN</th>
                    <th class="text-muted fw-semibold" style="font-size:12px">TANGGAL</th>
                    <th class="text-muted fw-semibold" style="font-size:12px">JUMLAH</th>
                    <th class="text-muted fw-semibold" style="font-size:12px">METODE</th>
                    <th class="text-muted fw-semibold text-center" style="font-size:12px">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @foreach($receipts as $r)
                @php
                    $methodLabels = ['cash'=>'Cash','transfer'=>'Transfer','cheque'=>'Cheque','other'=>'Lainnya'];
                    // Ambil data perusahaan dari relasi invoice -> salesOrder -> client
                    $client = $r->invoice->salesOrder->client ?? null;
                    $companyName = $client->nama_perusahaan ?? '-';
                    $nomorPo = $r->nomor_po ?: ($r->invoice->nomor_po ?? '-');
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="tt-no">{{ $r->nomor_receipt  }}</td>
                    <td>{{ $r->nomor_invoice  ?: '-' }}</td>
                    <td>{{ $nomorPo }}</td>
                    <td>{{ $companyName }}</td>
                    <td>{{ $r->tanggal_bayar->format('d M Y') }}</td>
                    <td class="fw-semibold" style="font-family:monospace">Rp {{ number_format($r->jumlah_bayar, 0, ',', '.') }}</td>
                    <td>{{ $methodLabels[$r->metode_bayar] ?? $r->metode_bayar }}</td>
                    <td class="text-center table-actions">
                        <a href="{{ route('admin.receipts.pdf', $r) }}" class="btn btn-danger btn-sm" target="_blank" title="Cetak PDF"><i class="bi bi-file-pdf"></i></a>
                        <a href="{{ route('admin.receipts.show', $r) }}" class="btn btn-info btn-sm"><i class="bi bi-eye"></i></a>
                        <a href="{{ route('admin.receipts.edit', $r) }}" class="btn btn-primary btn-sm"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('admin.receipts.destroy', $r) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus Tanda Terima ini?')"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white py-3">
        {{ $receipts->links() }}
    </div>
    @endif
</div>
@endsection
