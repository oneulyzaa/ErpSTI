@extends('layouts.app')

@php
    $isEdit      = isset($receipt);
    $action      = $isEdit ? route('admin.receipts.update', $receipt) : route('admin.receipts.store');
@endphp

@section('title', $isEdit ? 'Edit Tanda Terima' : 'Buat Tanda Terima Baru')
@section('breadcrumb', $isEdit ? 'Edit Tanda Terima' : 'Buat Tanda Terima')

@push('styles')
<style>
    .section-label {
        font-size: 11px; font-weight: 700; text-transform: uppercase;
        letter-spacing: .06em; color: #94a3b8;
        margin-bottom: 12px; padding-bottom: 8px;
        border-bottom: 1px solid #f1f5f9;
    }
    .amount-display { font-family: monospace; font-size: 24px; font-weight: 700; color: #1B5DBC; text-align: center; }
</style>
@endpush

@section('content')

{{-- Alert Error Validasi --}}
@if ($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>
    <strong>Validasi Gagal!</strong>
    <ul class="mb-0 mt-2">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

{{-- Alert Error Store --}}
@if (session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">{{ $isEdit ? 'Edit Tanda Terima' : 'Buat Tanda Terima Baru' }}</h4>
        <p class="text-muted mb-0" style="font-size:13px">PT. Sistem Teknologi Integrator</p>
    </div>
    <a href="{{ route('admin.receipts.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<form method="POST" action="{{ $action }}">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="row g-3 align-items-start">
        {{-- ── LEFT COLUMN ── --}}
        <div class="col-12 col-xl-8 d-flex flex-column gap-3">

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <span class="fw-semibold">Informasi Tanda Terima</span>
                </div>
                <div class="card-body">
                    <div class="section-label">Nomor & Referensi</div>
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-sm-4">
                            <label class="form-label fw-semibold" style="font-size:13px">No. Tanda Terima <span class="text-danger">*</span></label>
                            <input type="text" name="nomor_receipt"
                                   class="form-control form-control-sm @error('nomor_receipt') is-invalid @enderror"
                                   value="{{ old('nomor_receipt', $isEdit ? $receipt->nomor_receipt : $receiptNumber) }}" required>
                            @error('nomor_receipt')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-sm-4">
                            <label class="form-label fw-semibold" style="font-size:13px">Referensi Invoice <span class="text-danger">*</span></label>
                            <select name="nomor_invoice" id="nomor_invoice" class="form-select form-select-sm @error('nomor_invoice') is-invalid @enderror"
                                    data-url-template="{{ route('admin.receipts.invoice-data', ['invoice' => '__ID__']) }}" required>
                                <option value="">-- Pilih Invoice --</option>
                                @foreach($invoices as $inv)
                                    <option value="{{ $inv->nomor_invoice }}"
                                        {{ old('nomor_invoice', ($isEdit ? $receipt->nomor_invoice : '')) == $inv->nomor_invoice ? 'selected' : '' }}>
                                        {{ $inv->nomor_invoice }} — {{ $inv->salesOrder->client->nama_perusahaan ?? '-' }} (Rp {{ number_format($inv->grandtotal, 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                            @error('nomor_invoice')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                         <div class="col-12 col-sm-4">
                             <label class="form-label fw-semibold" style="font-size:13px">Nomor PO</label>
                             <input type="text" name="nomor_po" id="nomor_po" class="form-control form-control-sm"
                                    value="{{ old('nomor_po', $isEdit ? $receipt->nomor_po : '') }}"
                                    placeholder="Auto-load dari Invoice">
                         </div>
                         <div class="col-12 col-sm-6">
                             <label class="form-label fw-semibold" style="font-size:13px">Nama Project</label>
                             <input type="text" name="nama_project" id="nama_project" class="form-control form-control-sm"
                                    value="{{ old('nama_project', $isEdit ? $receipt->nama_project : '') }}"
                                    placeholder="Auto-load dari Invoice">
                         </div>
                         <div class="col-12 col-sm-6">
                             <label class="form-label fw-semibold" style="font-size:13px">Metode Pembayaran <span class="text-danger">*</span></label>
                            <select name="metode_bayar" class="form-select form-select-sm @error('metode_bayar') is-invalid @enderror" required>
                                @foreach(['cash'=>'Cash / Tunai','transfer'=>'Transfer Bank','cheque'=>'Cek / Giro','other'=>'Lainnya'] as $v=>$l)
                                    <option value="{{ $v }}" {{ old('metode_bayar', $isEdit ? $receipt->metode_bayar : 'transfer') === $v ? 'selected' : '' }}>{{ $l }}</option>
                                @endforeach
                            </select>
                            @error('metode_bayar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold" style="font-size:13px">Tanggal Bayar <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_bayar" class="form-control form-control-sm @error('tanggal_bayar') is-invalid @enderror"
                                   value="{{ old('tanggal_bayar', $isEdit ? $receipt->tanggal_bayar->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
                            @error('tanggal_bayar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px">Keterangan</label>
                        <textarea name="keterangan" class="form-control form-control-sm" rows="2"
                                  placeholder="Keterangan pembayaran...">{{ old('keterangan', $isEdit ? $receipt->keterangan : '') }}</textarea>
                    </div>
                </div>
            </div>

        </div>{{-- end left --}}

        {{-- ── RIGHT COLUMN ── --}}
        <div class="col-12 col-xl-4 d-flex flex-column gap-3">

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <span class="fw-semibold">Jumlah Pembayaran</span>
                </div>
                <div class="card-body text-center">
                    <div class="amount-display" id="display-amount">Rp 0</div>
                    <div class="mt-3">
                        <label class="form-label fw-semibold" style="font-size:13px">Jumlah Bayar <span class="text-danger">*</span></label>
                        <input type="number" name="jumlah_bayar" id="input-jumlah_bayar"
                               class="form-control form-control-lg text-center fw-bold @error('jumlah_bayar') is-invalid @enderror"
                               style="font-family:monospace;font-size:20px;color:#1B5DBC;"
                               value="{{ old('jumlah_bayar', $isEdit ? $receipt->jumlah_bayar : 0) }}" min="0" step="any" required
                               oninput="document.getElementById('display-amount').textContent = 'Rp ' + (parseFloat(this.value) || 0).toLocaleString('id-ID', {minimumFractionDigits:0, maximumFractionDigits:2})">
                        @error('jumlah_bayar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mt-3 text-start" style="font-size:12px;color:#94a3b8;" id="invoice-info">
                        @if($isEdit && $receipt->invoice)
                        Invoice: {{ $receipt->invoice->nomor_invoice }} — Total: Rp {{ number_format($receipt->invoice->grandtotal, 0, ',', '.') }}
                        @endif
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-check-lg"></i>
                    {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Tanda Terima' }}
                </button>
                <a href="{{ route('admin.receipts.index') }}" class="btn btn-outline-secondary text-center">Batal</a>
            </div>

        </div>{{-- end right --}}
    </div>
</form>

@endsection

@push('scripts')
<script>
/* ══ Auto-load from Invoice via AJAX ═══════════════════ */
document.addEventListener('DOMContentLoaded', function() {
    const invoiceSelect = document.getElementById('nomor_invoice');
    if (!invoiceSelect) return;
    
    invoiceSelect.addEventListener('change', async function() {
        const opt = this.options[this.selectedIndex];
        if (!opt.value) return;

        const url = this.dataset.urlTemplate.replace('__ID__', opt.value);

        try {
            const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) throw new Error('Failed to load invoice data');
            const data = await res.json();

            document.getElementById('nomor_po').value         = data.nomor_po || '';
            document.getElementById('nama_project').value     = data.nama_project || '';

            document.getElementById('invoice-info').innerHTML =
                'Invoice: ' + (data.nomor_invoice || '-') +
                ' — Total: Rp ' + (data.total || 0).toLocaleString('id-ID', {minimumFractionDigits:0, maximumFractionDigits:2}) +
                ' | Terbayar: Rp ' + (data.paid_amount || 0).toLocaleString('id-ID', {minimumFractionDigits:0, maximumFractionDigits:2});

            // Suggest remaining amount
            const remaining = (data.total || 0) - (data.paid_amount || 0);
            if (remaining > 0) {
                document.getElementById('input-jumlah_bayar').value = remaining;
                document.getElementById('display-amount').textContent = 'Rp ' + remaining.toLocaleString('id-ID', {minimumFractionDigits:0, maximumFractionDigits:2});
            }
        } catch (err) {
            console.error(err);
            alert('Gagal memuat data Invoice. Silakan coba lagi.');
        }
    });
});
</script>
@endpush
