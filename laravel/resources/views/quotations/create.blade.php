@extends('layouts.app')

@php
    $isEdit      = isset($quotation);
    $action      = $isEdit ? route('admin.quotations.update', $quotation) : route('admin.quotations.store');
    $oldItems    = old('items', $isEdit ? $quotation->items->toArray() : []);
@endphp

@section('title', $isEdit ? 'Edit Quotation' : 'Buat Quotation Baru')
@section('breadcrumb', $isEdit ? 'Edit Quotation' : 'Buat Quotation')

@push('styles')
<style>
    .section-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #94a3b8;
        margin-bottom: 12px;
        padding-bottom: 8px;
        border-bottom: 1px solid #f1f5f9;
    }
    /* Items table */
    #items-tbody td { padding: 5px 6px; vertical-align: middle; }
    .item-input {
        border: 1.5px solid #e2e8f0;
        border-radius: 6px;
        padding: 6px 10px;
        font-size: 13px;
        width: 100%;
        background: #fff;
        transition: border-color .15s, box-shadow .15s;
        outline: none;
        font-family: inherit;
    }
    .item-input:focus {
        border-color: #1B5DBC;
        box-shadow: 0 0 0 3px rgba(27,93,188,.12);
    }
    .item-no {
        font-family: monospace;
        font-size: 12px;
        color: #94a3b8;
        text-align: center;
        width: 36px;
    }
    .subtotal-cell {
        font-family: monospace;
        font-size: 13px;
        color: #374151;
        text-align: right;
        white-space: nowrap;
        padding-right: 12px !important;
    }
    .btn-remove-item {
        background: none;
        border: none;
        color: #cbd5e1;
        cursor: pointer;
        padding: 4px 6px;
        border-radius: 6px;
        transition: all .15s;
        font-size: 15px;
        display: flex; align-items: center;
    }
    .btn-remove-item:hover { color: #ef4444; background: #fee2e2; }

    /* Summary */
    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 7px 0;
        font-size: 14px;
        color: #475569;
        border-bottom: 1px solid #f1f5f9;
    }
    .summary-row:last-child { border-bottom: none; }
    .summary-row.total-row {
        font-size: 17px;
        font-weight: 700;
        color: #1e293b;
        padding-top: 12px;
        border-top: 2px solid #e2e8f0;
        border-bottom: none;
        margin-top: 4px;
    }
    .summary-val {
        font-family: monospace;
        font-size: 13px;
        color: #1e293b;
    }
    .summary-row.total-row .summary-val { font-size: 17px; color: #1B5DBC; }
</style>
@endpush

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">{{ $isEdit ? 'Edit Quotation' : 'Buat Quotation Baru' }}</h4>
        <p class="text-muted mb-0" style="font-size:13px">PT. Sistem Teknologi Integrator</p>
    </div>
    <a href="{{ route('admin.quotations.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<form method="POST" action="{{ $action }}" id="quotation-form">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="row g-3 align-items-start">

        {{-- ── LEFT COLUMN ─────────────────────────────── --}}
        <div class="col-12 col-xl-8 d-flex flex-column gap-3">

            {{-- Info Quotation --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <span class="fw-semibold">Informasi Quotation</span>
                </div>
                <div class="card-body">

                    <div class="section-label">Nomor & Tanggal</div>
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-sm-5">
                            <label class="form-label fw-semibold" style="font-size:13px">
                                No. Quotation <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="quote_number"
                                   class="form-control form-control-sm @error('quote_number') is-invalid @enderror"
                                   value="{{ old('quote_number', $isEdit ? $quotation->quote_number : $quoteNumber) }}"
                                   placeholder="QUO-202601-0001" required>
                            @error('quote_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-sm-4">
                            <label class="form-label fw-semibold" style="font-size:13px">Customer ID</label>
                            <input type="text" name="customer_id" class="form-control form-control-sm"
                                   value="{{ old('customer_id', $isEdit ? $quotation->customer_id : '') }}"
                                   placeholder="CUST-001">
                        </div>
                        <div class="col-12 col-sm-3">
                            <label class="form-label fw-semibold" style="font-size:13px">
                                Status <span class="text-danger">*</span>
                            </label>
                            <select name="status" class="form-select form-select-sm" required>
                                @foreach(['draft'=>'Draft','sent'=>'Terkirim','approved'=>'Disetujui','rejected'=>'Ditolak','expired'=>'Kadaluarsa'] as $v => $l)
                                    <option value="{{ $v }}" {{ old('status', $isEdit ? $quotation->status : 'draft') === $v ? 'selected' : '' }}>
                                        {{ $l }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold" style="font-size:13px">
                                Tanggal <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="date"
                                   class="form-control form-control-sm @error('date') is-invalid @enderror"
                                   value="{{ old('date', $isEdit ? $quotation->date->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
                            @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold" style="font-size:13px">
                                Berlaku Sampai <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="valid_until"
                                   class="form-control form-control-sm @error('valid_until') is-invalid @enderror"
                                   value="{{ old('valid_until', $isEdit ? $quotation->valid_until->format('Y-m-d') : now()->addDays(30)->format('Y-m-d')) }}" required>
                            @error('valid_until')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="section-label">Data Klien</div>
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold" style="font-size:13px">
                                Nama Kontak <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="client_name"
                                   class="form-control form-control-sm @error('client_name') is-invalid @enderror"
                                   value="{{ old('client_name', $isEdit ? $quotation->client_name : '') }}"
                                   placeholder="Bpk. Budi Santoso" required>
                            @error('client_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold" style="font-size:13px">
                                Perusahaan <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="client_company"
                                   class="form-control form-control-sm @error('client_company') is-invalid @enderror"
                                   value="{{ old('client_company', $isEdit ? $quotation->client_company : '') }}"
                                   placeholder="PT. Maju Bersama" required>
                            @error('client_company')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-sm-4">
                            <label class="form-label fw-semibold" style="font-size:13px">Attn (Perhatian)</label>
                            <input type="text" name="client_attention" class="form-control form-control-sm"
                                   value="{{ old('client_attention', $isEdit ? $quotation->client_attention : '') }}"
                                   placeholder="Bpk. Direktur">
                        </div>
                        <div class="col-12 col-sm-4">
                            <label class="form-label fw-semibold" style="font-size:13px">CC</label>
                            <input type="text" name="client_cc" class="form-control form-control-sm"
                                   value="{{ old('client_cc', $isEdit ? $quotation->client_cc : '') }}"
                                   placeholder="Nama penerima CC">
                        </div>
                        <div class="col-12 col-sm-4">
                            <label class="form-label fw-semibold" style="font-size:13px">Email Klien</label>
                            <input type="email" name="client_email" class="form-control form-control-sm"
                                   value="{{ old('client_email', $isEdit ? $quotation->client_email : '') }}"
                                   placeholder="klien@perusahaan.com">
                        </div>
                    </div>

                    <div>
                        <label class="form-label fw-semibold" style="font-size:13px">Deskripsi Pekerjaan</label>
                        <textarea name="description_of_work" class="form-control form-control-sm" rows="3"
                                  placeholder="Jelaskan pekerjaan atau lingkup proyek...">{{ old('description_of_work', $isEdit ? $quotation->description_of_work : '') }}</textarea>
                    </div>

                </div>
            </div>

            {{-- Items --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <span class="fw-semibold">Item / Material</span>
                    <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-1" id="btn-add-item">
                        <i class="bi bi-plus-lg"></i> Tambah Item
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0" style="font-size:13px;">
                        <thead class="table-light">
                            <tr>
                                <th style="width:36px;">#</th>
                                <th style="min-width:190px;">Nama Material / Jasa <span class="text-danger">*</span></th>
                                <th style="min-width:160px;">Deskripsi</th>
                                <th style="width:90px;text-align:center;">Satuan <span class="text-danger">*</span></th>
                                <th style="width:90px;text-align:right;">Qty <span class="text-danger">*</span></th>
                                <th style="width:140px;text-align:right;">Harga Satuan <span class="text-danger">*</span></th>
                                <th style="width:140px;text-align:right;">Subtotal</th>
                                <th style="width:40px;"></th>
                            </tr>
                        </thead>
                        <tbody id="items-tbody"></tbody>
                    </table>
                </div>
                <div class="card-footer bg-white border-top py-2">
                    <button type="button" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1" id="btn-add-item-2">
                        <i class="bi bi-plus-lg"></i> Tambah Item
                    </button>
                </div>
            </div>

        </div>{{-- end left --}}

        {{-- ── RIGHT COLUMN ──────────────────────────── --}}
        <div class="col-12 col-xl-4 d-flex flex-column gap-3">

            {{-- Summary --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <span class="fw-semibold">Ringkasan</span>
                </div>
                <div class="card-body">
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span class="summary-val" id="disp-subtotal">Rp 0</span>
                    </div>
                    <div class="summary-row align-items-start" style="gap:8px;">
                        <div>
                            <div style="font-size:13px;margin-bottom:4px;">PPN (%)</div>
                            <input type="number" name="tax_percentage" id="tax_percentage"
                                   class="form-control form-control-sm" min="0" max="100" step="0.01"
                                   value="{{ old('tax_percentage', $isEdit ? $quotation->tax_percentage : 11) }}"
                                   style="width:80px;">
                        </div>
                        <span class="summary-val mt-4" id="disp-tax">Rp 0</span>
                    </div>
                    <div class="summary-row total-row">
                        <span>Total</span>
                        <span class="summary-val" id="disp-total">Rp 0</span>
                    </div>
                    <input type="hidden" name="_subtotal"   id="hidden-subtotal">
                    <input type="hidden" name="_tax_amount" id="hidden-tax">
                    <input type="hidden" name="_total"      id="hidden-total">
                </div>
            </div>

            {{-- Catatan --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <span class="fw-semibold">Catatan / Syarat & Ketentuan</span>
                </div>
                <div class="card-body">
                    <textarea name="notes" class="form-control form-control-sm" rows="5"
                              placeholder="Syarat pembayaran, garansi, catatan tambahan...">{{ old('notes', $isEdit ? $quotation->notes : '') }}</textarea>
                </div>
            </div>

            {{-- Tombol --}}
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-check-lg"></i>
                    {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Quotation' }}
                </button>
                <a href="{{ route('admin.quotations.index') }}" class="btn btn-outline-secondary text-center">
                    Batal
                </a>
            </div>

        </div>{{-- end right --}}

    </div>
</form>

@endsection

@push('scripts')
<script>
const initialItems = @json($oldItems);
let itemIndex = 0;

function fmt(n) {
    return 'Rp ' + Math.round(n).toLocaleString('id-ID');
}

function esc(str) {
    return String(str ?? '').replace(/"/g,'&quot;').replace(/</g,'&lt;');
}

function createRow(item = {}) {
    const idx   = itemIndex++;
    const qty   = parseFloat(item.qty   ?? 1) || 1;
    const price = parseFloat(item.unit_price ?? 0) || 0;
    const sub   = qty * price;

    const tr = document.createElement('tr');
    tr.dataset.idx = idx;
    tr.innerHTML = `
        <td class="item-no" id="no-${idx}"></td>
        <td><input type="text"   name="items[${idx}][material_name]" class="item-input" required placeholder="Nama material / jasa" value="${esc(item.material_name)}"></td>
        <td><input type="text"   name="items[${idx}][description]"   class="item-input" placeholder="Keterangan (opsional)"        value="${esc(item.description)}"></td>
        <td><input type="text"   name="items[${idx}][unit]"          class="item-input" required placeholder="Unit"                value="${esc(item.unit ?? 'Unit')}" style="text-align:center;"></td>
        <td><input type="number" name="items[${idx}][qty]"           class="item-input item-qty"   required min="0" step="any" value="${qty}"  style="text-align:right;"></td>
        <td><input type="number" name="items[${idx}][unit_price]"    class="item-input item-price" required min="0" step="any" value="${price}" style="text-align:right;"></td>
        <td class="subtotal-cell" id="sub-${idx}">${fmt(sub)}</td>
        <td>
            <button type="button" class="btn-remove-item" onclick="removeRow(this)" title="Hapus baris">
                <i class="bi bi-x-lg"></i>
            </button>
        </td>
    `;

    tr.querySelector('.item-qty').addEventListener('input',   () => updateRow(tr));
    tr.querySelector('.item-price').addEventListener('input', () => updateRow(tr));
    return tr;
}

function updateRow(tr) {
    const idx   = tr.dataset.idx;
    const qty   = parseFloat(tr.querySelector('.item-qty').value)   || 0;
    const price = parseFloat(tr.querySelector('.item-price').value) || 0;
    tr.querySelector(`#sub-${idx}`).textContent = fmt(qty * price);
    recalcTotals();
}

function removeRow(btn) {
    const tbody = document.getElementById('items-tbody');
    if (tbody.rows.length <= 1) { alert('Minimal 1 item harus ada.'); return; }
    btn.closest('tr').remove();
    reorderNumbers();
    recalcTotals();
}

function reorderNumbers() {
    document.querySelectorAll('#items-tbody tr').forEach((tr, i) => {
        const no = tr.querySelector('[id^="no-"]');
        if (no) no.textContent = i + 1;
    });
}

function addRow(item = {}) {
    const tbody = document.getElementById('items-tbody');
    const tr = createRow(item);
    tbody.appendChild(tr);
    reorderNumbers();
    recalcTotals();
    tr.querySelector('.item-input').focus();
}

function recalcTotals() {
    let subtotal = 0;
    document.querySelectorAll('#items-tbody tr').forEach(tr => {
        const qty   = parseFloat(tr.querySelector('.item-qty')?.value)   || 0;
        const price = parseFloat(tr.querySelector('.item-price')?.value) || 0;
        subtotal += qty * price;
    });

    const taxPct    = parseFloat(document.getElementById('tax_percentage').value) || 0;
    const taxAmount = subtotal * (taxPct / 100);
    const total     = subtotal + taxAmount;

    document.getElementById('disp-subtotal').textContent = fmt(subtotal);
    document.getElementById('disp-tax').textContent      = fmt(taxAmount);
    document.getElementById('disp-total').textContent    = fmt(total);

    document.getElementById('hidden-subtotal').value = subtotal.toFixed(2);
    document.getElementById('hidden-tax').value      = taxAmount.toFixed(2);
    document.getElementById('hidden-total').value    = total.toFixed(2);
}

document.addEventListener('DOMContentLoaded', () => {
    const items = initialItems.length ? initialItems : [{}];
    items.forEach(item => addRow(item));

    document.getElementById('btn-add-item').addEventListener('click',  () => addRow());
    document.getElementById('btn-add-item-2').addEventListener('click', () => addRow());
    document.getElementById('tax_percentage').addEventListener('input', recalcTotals);
});
</script>
@endpush
