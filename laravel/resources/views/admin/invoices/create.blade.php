@extends('layouts.app')

@php
    $isEdit        = isset($invoice);
    $action        = $isEdit ? route('admin.invoices.update', $invoice) : route('admin.invoices.store');
@endphp

@section('title', $isEdit ? 'Edit Invoice' : 'Buat Invoice Baru')
@section('breadcrumb', $isEdit ? 'Edit Invoice' : 'Buat Invoice')

@push('styles')
<style>
    .section-label {
        font-size: 11px; font-weight: 700; text-transform: uppercase;
        letter-spacing: .06em; color: #94a3b8;
        margin-bottom: 12px; padding-bottom: 8px;
        border-bottom: 1px solid #f1f5f9;
    }
    .summary-row { display: flex; justify-content: space-between; align-items: center; padding: 7px 0; font-size: 14px; color: #475569; border-bottom: 1px solid #f1f5f9; }
    .summary-row:last-child { border-bottom: none; }
    .summary-row.total-row { font-size: 17px; font-weight: 700; color: #1e293b; border-top: 2px solid #e2e8f0; border-bottom: none; margin-top: 4px; padding-top: 12px; }
    .summary-val { font-family: monospace; font-size: 13px; color: #1e293b; }
    .summary-row.total-row .summary-val { font-size: 17px; color: #1B5DBC; }
</style>
@endpush

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">{{ $isEdit ? 'Edit Invoice' : 'Buat Invoice Baru' }}</h4>
        <p class="text-muted mb-0" style="font-size:13px">PT. Sistem Teknologi Integrator</p>
    </div>
    <a href="{{ route('admin.invoices.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<form method="POST" action="{{ $action }}" id="invoice-form">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="row g-3 align-items-start">
        {{-- ── LEFT COLUMN ── --}}
        <div class="col-12 col-xl-8 d-flex flex-column gap-3">

            {{-- Info Invoice --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <span class="fw-semibold">Informasi Invoice</span>
                </div>
                <div class="card-body">
                    <div class="section-label">Nomor & Referensi</div>
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-sm-4">
                            <label class="form-label fw-semibold" style="font-size:13px">No. Invoice <span class="text-danger">*</span></label>
                            <input type="text" name="invoice_number"
                                   class="form-control form-control-sm @error('invoice_number') is-invalid @enderror"
                                   value="{{ old('invoice_number', $isEdit ? $invoice->invoice_number : $invoiceNumber) }}" required>
                            @error('invoice_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-sm-4">
                            <label class="form-label fw-semibold" style="font-size:13px">Referensi Sales Order</label>
                            <select name="sales_order_id" id="sales_order_id" class="form-select form-select-sm"
                                    data-url-template="{{ route('admin.invoices.so-data', ['salesOrder' => '__ID__']) }}">
                                <option value="">-- Pilih SO (opsional) --</option>
                                @foreach($salesOrders as $so)
                                    <option value="{{ $so->id }}"
                                        {{ old('sales_order_id', ($isEdit ? $invoice->sales_order_id : '')) == $so->id ? 'selected' : '' }}>
                                        {{ $so->so_number }} — {{ $so->client_company }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="so_number" id="so_number"
                                   value="{{ old('so_number', $isEdit ? $invoice->so_number : '') }}">
                        </div>
                         <div class="col-12 col-sm-4">
                             <label class="form-label fw-semibold" style="font-size:13px">Nomor PO Customer</label>
                             <input type="text" name="nomor_po" id="nomor_po" class="form-control form-control-sm"
                                    value="{{ old('nomor_po', $isEdit ? $invoice->nomor_po : '') }}"
                                    placeholder="Auto-load dari SO">
                         </div>
                     </div>
                     <div class="row g-3 mb-3">
                         <div class="col-12 col-sm-6">
                             <label class="form-label fw-semibold" style="font-size:13px">Nama Project</label>
                             <input type="text" name="project_name" id="project_name" class="form-control form-control-sm"
                                    value="{{ old('project_name', $isEdit ? $invoice->project_name : '') }}"
                                    placeholder="Auto-load dari SO">
                         </div>
                     </div>
                     <div class="row g-3 mb-4">
                        <div class="col-12 col-sm-4">
                            <label class="form-label fw-semibold" style="font-size:13px">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select form-select-sm" required>
                                @foreach(['draft'=>'Draft','sent'=>'Sent','paid'=>'Paid','overdue'=>'Overdue','cancelled'=>'Cancelled'] as $v=>$l)
                                    <option value="{{ $v }}" {{ old('status', $isEdit ? $invoice->status : 'draft') === $v ? 'selected' : '' }}>{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold" style="font-size:13px">Tanggal Invoice <span class="text-danger">*</span></label>
                            <input type="date" name="date" class="form-control form-control-sm @error('date') is-invalid @enderror"
                                   value="{{ old('date', $isEdit ? $invoice->date->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
                            @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold" style="font-size:13px">Jatuh Tempo</label>
                            <input type="date" name="due_date" class="form-control form-control-sm"
                                   value="{{ old('due_date', $isEdit && $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '') }}">
                        </div>
                    </div>

                    <div class="section-label">Info Klien</div>
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold" style="font-size:13px">Perusahaan</label>
                            <input type="text" name="client_company" id="client_company" class="form-control form-control-sm"
                                   value="{{ old('client_company', $isEdit ? $invoice->client_company : '') }}"
                                   placeholder="Auto-load dari SO">
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold" style="font-size:13px">Nama Kontak</label>
                            <input type="text" name="client_name" id="client_name" class="form-control form-control-sm"
                                   value="{{ old('client_name', $isEdit ? $invoice->client_name : '') }}"
                                   placeholder="Auto-load dari SO">
                        </div>
                        <div class="col-12 col-sm-4">
                            <label class="form-label fw-semibold" style="font-size:13px">Attn</label>
                            <input type="text" name="client_attention" id="client_attention" class="form-control form-control-sm"
                                   value="{{ old('client_attention', $isEdit ? $invoice->client_attention : '') }}">
                        </div>
                        <div class="col-12 col-sm-4">
                            <label class="form-label fw-semibold" style="font-size:13px">CC</label>
                            <input type="text" name="client_cc" id="client_cc" class="form-control form-control-sm"
                                   value="{{ old('client_cc', $isEdit ? $invoice->client_cc : '') }}">
                        </div>
                        <div class="col-12 col-sm-4">
                            <label class="form-label fw-semibold" style="font-size:13px">Email</label>
                            <input type="email" name="client_email" id="client_email" class="form-control form-control-sm"
                                   value="{{ old('client_email', $isEdit ? $invoice->client_email : '') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px">Alamat</label>
                        <textarea name="client_address" id="client_address" class="form-control form-control-sm" rows="2"
                                placeholder="Alamat pelanggan...">{{ old('client_address', $isEdit ? $invoice->client_address : '') }}</textarea>
                    </div>
                     <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px">Deskripsi</label>
                        <textarea name="description" id="description" class="form-control form-control-sm" rows="2"
                                  placeholder="Deskripsi pekerjaan...">{{ old('description', $isEdit ? $invoice->description : '') }}</textarea>
                    </div>
                </div>
            </div>

        </div>{{-- end left --}}

        {{-- ── RIGHT COLUMN ── --}}
        <div class="col-12 col-xl-4 d-flex flex-column gap-3">

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <span class="fw-semibold">Nilai Project</span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px">Nilai Project <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-text" style="background:#f1f5f9;font-size:13px;">Rp</div>
                            <input type="text" id="project_value_display"
                                   class="form-control form-control-sm" placeholder="0"
                                   style="text-align:right;"
                                   oninput="formatNumberInput(this, 'project_value')">
                             <input type="hidden" name="project_value" id="project_value"
                                    value="{{ old('project_value', $isEdit ? ($invoice->project_value ?? ($invoice->subtotal + $invoice->discount)) : 0) }}">
                        </div>
                    </div>
                    
                    <div class="summary-row"><span>Nilai Project</span><span class="summary-val" id="sum-project">Rp 0</span></div>
                    
                    <div class="summary-row align-items-start gap-2" style="flex-wrap:wrap;">
                        <div>
                            <div style="font-size:13px;margin-bottom:4px;">Diskon (Rp)</div>
                            <div class="input-group" style="max-width:160px;">
                                <div class="input-group-text" style="background:#f1f5f9;font-size:13px;">Rp</div>
                                <input type="text" id="discount_display"
                                       class="form-control form-control-sm" placeholder="0"
                                       style="text-align:right;"
                                       oninput="formatNumberInput(this, 'discount')">
                                <input type="hidden" name="discount" id="discount"
                                       value="{{ old('discount', $isEdit ? $invoice->discount : 0) }}">
                            </div>
                        </div>
                        <span class="summary-val mt-4" id="sum-discount">Rp 0</span>
                    </div>

                    <div class="summary-row"><span>Subtotal</span><span class="summary-val" id="sum-sub">Rp 0</span></div>

                    <div class="summary-row align-items-start gap-2" style="flex-wrap:wrap;">
                        <div>
                            <div style="font-size:13px;margin-bottom:4px;">PPN (%)</div>
                            <input type="number" name="tax_percentage" id="tax_percentage"
                                   class="form-control form-control-sm" min="0" max="100" step="0.01"
                                   value="{{ old('tax_percentage', $isEdit ? $invoice->tax_percentage : 11) }}"
                                   style="width:80px;">
                        </div>
                        <span class="summary-val mt-4" id="sum-tax">Rp 0</span>
                    </div>

                    <div class="summary-row total-row">
                        <span>TOTAL</span>
                        <span class="summary-val" id="sum-total">Rp 0</span>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <span class="fw-semibold">Syarat & Ketentuan</span>
                </div>
                <div class="card-body">
                    <textarea name="term_and_condition" class="form-control form-control-sm" rows="4"
                              placeholder="Syarat dan ketentuan pembayaran...">{{ old('term_and_condition', $isEdit ? $invoice->term_and_condition : '') }}</textarea>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <span class="fw-semibold">Catatan</span>
                </div>
                <div class="card-body">
                    <textarea name="notes" class="form-control form-control-sm" rows="4"
                              placeholder="Catatan internal...">{{ old('notes', $isEdit ? $invoice->notes : '') }}</textarea>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-check-lg"></i>
                    {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Invoice' }}
                </button>
                <a href="{{ route('admin.invoices.index') }}" class="btn btn-outline-secondary text-center">Batal</a>
            </div>

        </div>{{-- end right --}}
    </div>
</form>

@endsection

@push('scripts')
<script>
const fmt = n => 'Rp ' + Math.round(n).toLocaleString('id-ID');

/* ══ Auto-load from Sales Order via AJAX ═══════════════════ */
document.getElementById('sales_order_id')?.addEventListener('change', async function() {
    const opt = this.options[this.selectedIndex];
    if (!opt.value) return;

    const url = this.dataset.urlTemplate.replace('__ID__', opt.value);

    try {
        const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
        if (!res.ok) throw new Error('Failed to load SO data');
        const data = await res.json();

        document.getElementById('so_number').value          = data.so_number || '';
        document.getElementById('nomor_po').value           = data.nomor_po || '';
        document.getElementById('project_name').value       = data.project_name || '';

        // Load total project value from SO
        const projectValueEl = document.getElementById('project_value');
        const projectValueDisplayEl = document.getElementById('project_value_display');
        if (projectValueEl && projectValueDisplayEl && data.total) {
            projectValueEl.value = data.total;
            projectValueDisplayEl.value = parseFloat(data.total).toLocaleString('id-ID');
        }

        document.getElementById('client_name').value        = data.client_name || '';
        document.getElementById('client_company').value     = data.client_company || '';
        document.getElementById('client_attention').value   = data.client_attention || '';
        document.getElementById('client_cc').value          = data.client_cc || '';
        document.getElementById('client_email').value       = data.client_email || '';
        document.getElementById('client_address').value     = data.client_address || '';
        document.getElementById('description').value        = data.description || '';

        recalc();
    } catch (err) {
        console.error(err);
        alert('Gagal memuat data Sales Order. Silakan coba lagi.');
    }
});

// Number formatting helper
function formatNumberInput(el, hiddenId) {
    let val = el.value.replace(/[^0-9]/g, '');
    if (val === '') val = '0';
    document.getElementById(hiddenId).value = parseFloat(val);
    el.value = parseFloat(val).toLocaleString('id-ID');
    recalc();
}

function recalc() {
    const projectValue = parseFloat(document.getElementById('project_value').value) || 0;
    const discount = parseFloat(document.getElementById('discount').value) || 0;
    const taxPercentage = parseFloat(document.getElementById('tax_percentage').value) || 0;
    
    const subtotal = projectValue - discount;
    const tax = subtotal * (taxPercentage / 100);
    const total = subtotal + tax;

    document.getElementById('sum-project').textContent   = fmt(projectValue);
    document.getElementById('sum-discount').textContent  = fmt(discount);
    document.getElementById('sum-sub').textContent       = fmt(subtotal);
    document.getElementById('sum-tax').textContent       = fmt(tax);
    document.getElementById('sum-total').textContent     = fmt(Math.max(total, 0));
}

/* ══ Tax percentage change ═══════════════════ */
document.getElementById('tax_percentage')?.addEventListener('input', recalc);

// Format initial values
document.addEventListener('DOMContentLoaded', () => {
    const discountEl = document.getElementById('discount');
    const discountDisplayEl = document.getElementById('discount_display');
    if (discountEl && discountDisplayEl) {
        const discountVal = parseFloat(discountEl.value || 0);
        discountDisplayEl.value = discountVal.toLocaleString('id-ID');
    }

    const projectValueEl = document.getElementById('project_value');
    const projectValueDisplayEl = document.getElementById('project_value_display');
    if (projectValueEl && projectValueDisplayEl) {
        const projectVal = parseFloat(projectValueEl.value || 0);
        if (projectVal > 0) {
            projectValueDisplayEl.value = projectVal.toLocaleString('id-ID');
        }
    }

    recalc();
});
</script>
@endpush
