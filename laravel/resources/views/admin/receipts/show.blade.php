@extends('layouts.app')
@section('title', 'Detail ' . $receipt->nomor_receipt)
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

@php
    // Ambil data client dari relasi invoice -> salesOrder -> client
    $client = $receipt->invoice->salesOrder->client ?? null;
    $companyName = $client->nama_perusahaan ?? '-';
    $contactName = $client->nama_kontak ?? '-';
    $contactEmail = $client->email_perusahaan ?? '-';
    $contactPhone = $client->nomor_teelpon ?? '-';
    $clientAddress = $client->alamat_perusahaan ?? '-';
    
    // Method labels
    $methodLabels = ['cash' => 'Tunai', 'transfer' => 'Transfer Bank', 'cheque' => 'Cek/Giro', 'other' => 'Lainnya'];
@endphp

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Tanda Terima: {{ $receipt->nomor_receipt }}</h4>
        <p class="text-muted mb-0" style="font-size:13px">{{ $companyName }}</p>
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
                        <div class="info-value">{{ $receipt->nomor_receipt }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="info-label">Referensi Invoice</div>
                        <div class="info-value">{{ $receipt->nomor_invoice ?: '-' }}</div>
                    </div>
                     <div class="col-6 col-md-3">
                         <div class="info-label">Nomor PO</div>
                         <div class="info-value">{{ $receipt->nomor_po ?: '-' }}</div>
                     </div>
                     <div class="col-6 col-md-3">
                         <div class="info-label">Project</div>
                         <div class="info-value">{{ $receipt->nama_project ?: '-' }}</div>
                     </div>
                     <div class="col-6 col-md-3">
                         <div class="info-label">Tanggal</div>
                         <div class="info-value">{{ $receipt->tanggal_bayar->format('d M Y') }}</div>
                     </div>
                    <div class="col-6 col-md-3">
                        <div class="info-label">Metode Pembayaran</div>
                        <div class="info-value">{{ $methodLabels[$receipt->metode_bayar] ?? $receipt->metode_bayar }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="info-label">Keterangan</div>
                        <div class="info-value">{{ $receipt->keterangan ?: '-' }}</div>
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
                        <div class="info-value">{{ $companyName }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="info-label">Kontak</div>
                        <div class="info-value">{{ $contactName }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="info-label">Email</div>
                        <div class="info-value">{{ $contactEmail }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="info-label">Telepon</div>
                        <div class="info-value">{{ $contactPhone }}</div>
                    </div>
                    @if($clientAddress && $clientAddress != '-')
                    <div class="col-12">
                        <div class="info-label">Alamat</div>
                        <div class="info-value">{{ $clientAddress }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-header bg-white border-bottom py-3">
                <span class="fw-semibold">Jumlah Pembayaran</span>
            </div>
            <div class="card-body py-4">
                <div class="amount-big">Rp {{ number_format($receipt->jumlah_bayar, 0, ',', '.') }}</div>
                <div class="text-muted mt-2" style="font-size:13px">
                    {{ $methodLabels[$receipt->metode_bayar] ?? $receipt->metode_bayar }}
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
                <div class="info-value">{{ $receipt->invoice->nomor_invoice }}</div>
                <div class="info-label mt-2">Total Invoice</div>
                <div class="info-value">Rp {{ number_format($receipt->invoice->grandtotal ?? 0, 0, ',', '.') }}</div>
                <div class="info-label mt-2">Status Invoice</div>
                <div class="info-value"><span class="badge badge-{{ $receipt->invoice->status_pembayaran ?? 'draft' }}">{{ ucfirst($receipt->invoice->status_pembayaran ?? 'draft') }}</span></div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection