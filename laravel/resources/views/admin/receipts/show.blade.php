@extends('layouts.app')
@section('title', 'Detail ' . $receipt->receipt_number)
@section('breadcrumb', 'Detail Tanda Terima')

@push('styles')
<style>
    .badge-draft     { background:#e2e8f0; color:#475569; }
    .badge-confirmed { background:#dcfce7; color:#15803d; }
    .badge-cancelled { background:#fee2e2; color:#b91c1c; }
    .info-label { font-size: 12px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; }
    .info-value { font-size: 14px; font-weight: 500; color: #1e293b; }
    .amount-big { font-family: monospace; font-size: 28px; font-weight: 700; color: #1B5DBC; }
</style>
@endpush

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Tanda Terima: {{ $receipt->receipt_number }}</h4>
        <p class="text-muted mb-0" style="font-size:13px">{{ $receipt->client_company }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.receipts.pdf', $receipt) }}" class="btn btn-danger d-flex align-items-center gap-2" target="_blank">
            <i class="bi bi-file-pdf"></i> Cetak PDF
        </a>
        <a href="{{ route('admin.receipts.edit', $receipt) }}" class="btn btn-primary d-flex align-items-center gap-2">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <a href="{{ route('admin.receipts.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="row g-3">
    <div class="col-12 col-lg-8">
        {{-- Info --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <span class="fw-semibold">Informasi Tanda Terima</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <div class="info-label">No. Tanda Terima</div>
                        <div class="info-value">{{ $receipt->receipt_number }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="info-label">Referensi Invoice</div>
                        <div class="info-value">{{ $receipt->invoice_number ?: '-' }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="info-label">Nomor PO</div>
                        <div class="info-value">{{ $receipt->nomor_po ?: '-' }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="info-label">Tanggal</div>
                        <div class="info-value">{{ $receipt->date->format('d M Y') }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="info-label">Status</div>
                        <div class="info-value"><span class="badge badge-{{ $receipt->status }}">{{ ucfirst($receipt->status) }}</span></div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="info-label">Metode Pembayaran</div>
                        <div class="info-value">{{ ucfirst($receipt->payment_method) }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="info-label">No. Referensi</div>
                        <div class="info-value">{{ $receipt->payment_reference ?: '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Client --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <span class="fw-semibold">Klien</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <div class="info-label">Perusahaan</div>
                        <div class="info-value">{{ $receipt->client_company }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="info-label">Kontak</div>
                        <div class="info-value">{{ $receipt->client_name ?: '-' }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="info-label">Attn</div>
                        <div class="info-value">{{ $receipt->client_attention ?: '-' }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="info-label">Email</div>
                        <div class="info-value">{{ $receipt->client_email ?: '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Description & Notes --}}
        @if($receipt->description)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <span class="fw-semibold">Deskripsi</span>
            </div>
            <div class="card-body">
                <p style="font-size:13px;white-space:pre-wrap">{{ $receipt->description }}</p>
            </div>
        </div>
        @endif

        @if($receipt->notes)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <span class="fw-semibold">Catatan</span>
            </div>
            <div class="card-body">
                <p style="font-size:13px;white-space:pre-wrap">{{ $receipt->notes }}</p>
            </div>
        </div>
        @endif
    </div>

    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-header bg-white border-bottom py-3">
                <span class="fw-semibold">Jumlah Pembayaran</span>
            </div>
            <div class="card-body py-4">
                <div class="amount-big">Rp {{ number_format($receipt->amount, 0, ',', '.') }}</div>
                @if($receipt->discount > 0)
                <div class="text-muted mt-2" style="font-size:13px;">
                    Diskon: Rp {{ number_format($receipt->discount, 0, ',', '.') }}
                </div>
                <div class="mt-1" style="font-size:13px;color:#475569;">
                    Total Setelah Diskon: <strong style="color:#1B5DBC;">Rp {{ number_format($receipt->amount - $receipt->discount, 0, ',', '.') }}</strong>
                </div>
                @endif
                <div class="text-muted mt-2" style="font-size:13px">
                    {{ $receipt->payment_method === 'cash' ? 'Tunai' : ($receipt->payment_method === 'transfer' ? 'Transfer Bank' : ($receipt->payment_method === 'cheque' ? 'Cek/Giro' : 'Lainnya')) }}
                </div>
            </div>
        </div>

        @if($receipt->invoice)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <span class="fw-semibold">Referensi Invoice</span>
            </div>
            <div class="card-body">
                <div class="info-label">Invoice</div>
                <div class="info-value">{{ $receipt->invoice->invoice_number }}</div>
                <div class="info-label mt-2">Total Invoice</div>
                <div class="info-value">Rp {{ number_format($receipt->invoice->total, 0, ',', '.') }}</div>
                <div class="info-label mt-2">Status Invoice</div>
                <div class="info-value"><span class="badge badge-{{ $receipt->invoice->status }}">{{ ucfirst($receipt->invoice->status) }}</span></div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
