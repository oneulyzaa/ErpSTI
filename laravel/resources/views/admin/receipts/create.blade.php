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
                            <input type="text" name="receipt_number"
                                   class="form-control form-control-sm @error('receipt_number') is-invalid @enderror"
                                   value="{{ old('receipt_number', $isEdit ? $receipt->receipt_number : $receiptNumber) }}" required>
                            @error('receipt_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-sm-4">
                            <label class="form-label fw-semibold" style="font-size:13px">Referensi Invoice</label>
                            <select name="invoice_id" id="invoice_id" class="form-select form-select-sm"
                                    data-url-template="{{ route('admin.receipts.invoice-data', ['invoice' => '__ID__']) }}">
                                <option value="">-- Pilih Invoice (opsional) --</option>
                                @foreach($invoices as $inv)
                                    <option value="{{ $inv->id }}"
                                        {{ old('invoice_id', ($isEdit ? $receipt->invoice_id : '')) == $inv->id ? 'selected' : '' }}>
                                        {{ $inv->invoice_number }} — {{ $inv->client_company }} (Rp {{ number_format($inv->total, 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="invoice_number" id="invoice_number"
                                   value="{{ old('invoice_number', $isEdit ? $receipt->invoice_number : '') }}">
                        </div>
                        <div class="col-12 col-sm-4">
                            <label class="form-label fw-semibold" style="font-size:13px">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select form-select-sm" required>
                                @foreach(['draft'=>'Draft','confirmed'=>'Confirmed','cancelled'=>'Cancelled'] as $v=>$l)
                                    <option value="{{ $v }}" {{ old('status', $isEdit ? $receipt->status : 'confirmed') === $v ? 'selected' : '' }}>{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold" style="font-size:13px">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="date" class="form-control form-control-sm @error('date') is-invalid @enderror"
                                   value="{{ old('date', $isEdit ? $receipt->date->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
                            @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold" style="font-size:13px">Metode Pembayaran <span class="text-danger">*</span></label>
                            <select name="payment_method" class="form-select form-select-sm" required>
                                @foreach(['cash'=>'Cash / Tunai','transfer'=>'Transfer Bank','cheque'=>'Cek / Giro','other'=>'Lainnya'] as $v=>$l)
                                    <option value="{{ $v }}" {{ old('payment_method', $isEdit ? $receipt->payment_method : 'transfer') === $v ? 'selected' : '' }}>{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold" style="font-size:13px">No. Referensi Pembayaran</label>
                            <input type="text" name="payment_reference" class="form-control form-control-sm"
                                   value="{{ old('payment_reference', $isEdit ? $receipt->payment_reference : '') }}"
                                   placeholder="No. referensi dari bank (opsional)">
                        </div>
                    </div>

                    <div class="section-label">Info Klien</div>
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold" style="font-size:13px">Perusahaan</label>
                            <input type="text" name="client_company" id="client_company" class="form-control form-control-sm"
                                   value="{{ old('client_company', $isEdit ? $receipt->client_company : '') }}">
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold" style="font-size:13px">Nama Kontak</label>
                            <input type="text" name="client_name" id="client_name" class="form-control form-control-sm"
                                   value="{{ old('client_name', $isEdit ? $receipt->client_name : '') }}">
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold" style="font-size:13px">Attn</label>
                            <input type="text" name="client_attention" id="client_attention" class="form-control form-control-sm"
                                   value="{{ old('client_attention', $isEdit ? $receipt->client_attention : '') }}">
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold" style="font-size:13px">Email</label>
                            <input type="email" name="client_email" id="client_email" class="form-control form-control-sm"
                                   value="{{ old('client_email', $isEdit ? $receipt->client_email : '') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px">Deskripsi</label>
                        <textarea name="description" class="form-control form-control-sm" rows="2"
                                  placeholder="Keterangan pembayaran...">{{ old('description', $isEdit ? $receipt->description : '') }}</textarea>
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
                        <label class="form-label fw-semibold" style="font-size:13px">Jumlah <span class="text-danger">*</span></label>
                        <input type="number" name="amount" id="input-amount"
                               class="form-control form-control-lg text-center fw-bold"
                               style="font-family:monospace;font-size:20px;color:#1B5DBC;"
                               value="{{ old('amount', $isEdit ? $receipt->amount : 0) }}" min="0" step="any" required
                               oninput="document.getElementById('display-amount').textContent = 'Rp ' + (parseFloat(this.value) || 0).toLocaleString('id-ID', {minimumFractionDigits:0, maximumFractionDigits:2})">
                    </div>
                    <div class="mt-3 text-start" style="font-size:12px;color:#94a3b8;" id="invoice-info">
                        @if($isEdit && $receipt->invoice)
                        Invoice: {{ $receipt->invoice->invoice_number }} — Total: Rp {{ number_format($receipt->invoice->total, 0, ',', '.') }}
                        @endif
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <span class="fw-semibold">Catatan</span>
                </div>
                <div class="card-body">
                    <textarea name="notes" class="form-control form-control-sm" rows="4"
                              placeholder="Catatan internal...">{{ old('notes', $isEdit ? $receipt->notes : '') }}</textarea>
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
document.getElementById('invoice_id')?.addEventListener('change', async function() {
    const opt = this.options[this.selectedIndex];
    if (!opt.value) return;

    const url = this.dataset.urlTemplate.replace('__ID__', opt.value);

    try {
        const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
        if (!res.ok) throw new Error('Failed to load invoice data');
        const data = await res.json();

        document.getElementById('invoice_number').value   = data.invoice_number || '';
        document.getElementById('client_name').value      = data.client_name || '';
        document.getElementById('client_company').value   = data.client_company || '';
        document.getElementById('client_attention').value = data.client_attention || '';
        document.getElementById('client_email').value     = data.client_email || '';

        document.getElementById('invoice-info').innerHTML =
            'Invoice: ' + (data.invoice_number || '-') +
            ' — Total: Rp ' + (data.total || 0).toLocaleString('id-ID', {minimumFractionDigits:0, maximumFractionDigits:2}) +
            ' | Terbayar: Rp ' + (data.paid_amount || 0).toLocaleString('id-ID', {minimumFractionDigits:0, maximumFractionDigits:2});

        // Suggest remaining amount
        const remaining = (data.total || 0) - (data.paid_amount || 0);
        if (remaining > 0) {
            document.getElementById('input-amount').value = remaining;
            document.getElementById('display-amount').textContent = 'Rp ' + remaining.toLocaleString('id-ID', {minimumFractionDigits:0, maximumFractionDigits:2});
        }
    } catch (err) {
        console.error(err);
    }
});
</script>
@endpush
