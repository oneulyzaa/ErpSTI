@extends('layouts.app')

@php
    $isEdit      = isset($quotation);
    $action      = $isEdit ? route('admin.quotations.update', $quotation) : route('admin.quotations.store');
    $oldItems    = old('items',  $isEdit ? $quotation->items->toArray()  : []);
    $oldLabors   = old('labors', $isEdit ? $quotation->labors->toArray() : $defaultLabors);
@endphp

@section('title', $isEdit ? 'Edit Quotation' : 'Buat Quotation Baru')
@section('breadcrumb', $isEdit ? 'Edit Quotation' : 'Buat Quotation')

@push('styles')
<style>
    .section-label {
        font-size: 11px; font-weight: 700; text-transform: uppercase;
        letter-spacing: .06em; color: #94a3b8;
        margin-bottom: 12px; padding-bottom: 8px;
        border-bottom: 1px solid #f1f5f9;
    }
    .item-input {
        border: 1.5px solid #e2e8f0; border-radius: 6px;
        padding: 6px 10px; font-size: 13px; width: 100%;
        background: #fff; font-family: inherit; outline: none;
        transition: border-color .15s, box-shadow .15s;
    }
    .item-input:focus { border-color: #1B5DBC; box-shadow: 0 0 0 3px rgba(27,93,188,.12); }
    .item-no { font-family: monospace; font-size: 12px; color: #94a3b8; text-align: center; width: 36px; }
    .subtotal-cell { font-family: monospace; font-size: 12.5px; color: #374151; text-align: right; white-space: nowrap; }
    .btn-remove-row {
        background: none; border: none; color: #cbd5e1; cursor: pointer;
        padding: 4px 6px; border-radius: 6px; transition: all .15s; font-size: 15px;
        display: flex; align-items: center;
    }
    .btn-remove-row:hover { color: #ef4444; background: #fee2e2; }
    .summary-row { display: flex; justify-content: space-between; align-items: center; padding: 7px 0; font-size: 14px; color: #475569; border-bottom: 1px solid #f1f5f9; }
    .summary-row:last-child { border-bottom: none; }
    .summary-row.total-row { font-size: 17px; font-weight: 700; color: #1e293b; border-top: 2px solid #e2e8f0; border-bottom: none; margin-top: 4px; padding-top: 12px; }
    .summary-val { font-family: monospace; font-size: 13px; color: #1e293b; }
    .summary-row.total-row .summary-val { font-size: 17px; color: #1B5DBC; }
    .table-section-header th { background: #1e3a5f !important; color: #fff !important; font-size: 11px; text-transform: uppercase; letter-spacing: .05em; }
    .labor-header th { background: #1B5DBC !important; color: #fff !important; font-size: 11px; text-transform: uppercase; letter-spacing: .05em; }
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

        {{-- ── LEFT COLUMN ── --}}
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
                            <label class="form-label fw-semibold" style="font-size:13px">No. Quotation <span class="text-danger">*</span></label>
                            <input type="text" name="quote_number"
                                   class="form-control form-control-sm @error('quote_number') is-invalid @enderror"
                                   value="{{ old('quote_number', $isEdit ? $quotation->quote_number : $quoteNumber) }}" required>
                            @error('quote_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-sm-4">
                            <label class="form-label fw-semibold" style="font-size:13px">Customer ID</label>
                            <input type="text" name="customer_id" class="form-control form-control-sm"
                                   value="{{ old('customer_id', $isEdit ? $quotation->customer_id : '') }}" placeholder="CUST-001">
                        </div>
                        <div class="col-12 col-sm-3">
                            <label class="form-label fw-semibold" style="font-size:13px">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select form-select-sm" required>
                                @foreach(['draft'=>'Draft','sent'=>'Terkirim','approved'=>'Disetujui','rejected'=>'Ditolak','expired'=>'Kadaluarsa'] as $v=>$l)
                                    <option value="{{ $v }}" {{ old('status', $isEdit ? $quotation->status : 'draft') === $v ? 'selected' : '' }}>{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold" style="font-size:13px">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="date" class="form-control form-control-sm @error('date') is-invalid @enderror"
                                   value="{{ old('date', $isEdit ? $quotation->date->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
                            @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold" style="font-size:13px">Berlaku Sampai <span class="text-danger">*</span></label>
                            <input type="date" name="valid_until" class="form-control form-control-sm @error('valid_until') is-invalid @enderror"
                                   value="{{ old('valid_until', $isEdit ? $quotation->valid_until->format('Y-m-d') : now()->addDays(30)->format('Y-m-d')) }}" required>
                            @error('valid_until')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="section-label">Data Klien</div>
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold" style="font-size:13px">Nama Kontak <span class="text-danger">*</span></label>
                            <input type="text" name="client_name" class="form-control form-control-sm @error('client_name') is-invalid @enderror"
                                   value="{{ old('client_name', $isEdit ? $quotation->client_name : '') }}" placeholder="Bpk. Budi Santoso" required>
                            @error('client_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold" style="font-size:13px">Perusahaan <span class="text-danger">*</span></label>
                            <input type="text" name="client_company" class="form-control form-control-sm @error('client_company') is-invalid @enderror"
                                   value="{{ old('client_company', $isEdit ? $quotation->client_company : '') }}" placeholder="PT. Maju Bersama" required>
                            @error('client_company')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-sm-4">
                            <label class="form-label fw-semibold" style="font-size:13px">Attn</label>
                            <input type="text" name="client_attention" class="form-control form-control-sm"
                                   value="{{ old('client_attention', $isEdit ? $quotation->client_attention : '') }}" placeholder="Bpk. Direktur">
                        </div>
                        <div class="col-12 col-sm-4">
                            <label class="form-label fw-semibold" style="font-size:13px">CC</label>
                            <input type="text" name="client_cc" class="form-control form-control-sm"
                                   value="{{ old('client_cc', $isEdit ? $quotation->client_cc : '') }}">
                        </div>
                        <div class="col-12 col-sm-4">
                            <label class="form-label fw-semibold" style="font-size:13px">Email</label>
                            <input type="email" name="client_email" class="form-control form-control-sm"
                                   value="{{ old('client_email', $isEdit ? $quotation->client_email : '') }}" placeholder="email@perusahaan.com">
                        </div>
                    </div>

                    <div>
                        <label class="form-label fw-semibold" style="font-size:13px">Description of Work</label>
                        <textarea name="description_of_work" class="form-control form-control-sm" rows="2"
                                  placeholder="Jelaskan lingkup pekerjaan...">{{ old('description_of_work', $isEdit ? $quotation->description_of_work : '') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- ── MATERIAL ITEMS ── --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <span class="fw-semibold">Material</span>
                    <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-1" id="btn-add-item">
                        <i class="bi bi-plus-lg"></i> Tambah Material
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0" style="font-size:13px;">
                        <thead>
                            <tr class="table-section-header">
                                <th style="width:36px;">#</th>
                                <th style="min-width:180px;">Nama Material / Jasa <span class="text-warning">*</span></th>
                                <th style="min-width:140px;">Deskripsi</th>
                                <th style="width:80px;text-align:center;">Satuan</th>
                                <th style="width:80px;text-align:right;">Qty</th>
                                <th style="width:130px;text-align:right;">Harga Satuan</th>
                                <th style="width:130px;text-align:right;">Sub Total</th>
                                <th style="width:36px;"></th>
                            </tr>
                        </thead>
                        <tbody id="items-tbody"></tbody>
                    </table>
                </div>
                <div class="card-footer bg-white d-flex align-items-center justify-content-between py-2">
                    <button type="button" class="btn btn-outline-primary btn-sm" id="btn-add-item-2">
                        <i class="bi bi-plus-lg"></i> Tambah Material
                    </button>
                    <div class="fw-semibold" style="font-size:13px;">
                        Total Material: <span class="text-primary ms-2" id="disp-mat" style="font-family:monospace;">Rp 0</span>
                    </div>
                </div>
            </div>

            {{-- ── LABOR ── --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <span class="fw-semibold">Labor</span>
                    <button type="button" class="btn btn-sm d-flex align-items-center gap-1"
                            style="background:#1B5DBC;color:#fff;" id="btn-add-labor">
                        <i class="bi bi-plus-lg"></i> Tambah Labor
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0" style="font-size:13px;">
                        <thead>
                            <tr class="labor-header">
                                <th style="width:36px;">#</th>
                                <th style="min-width:180px;">Labor / Pekerjaan <span class="text-warning">*</span></th>
                                <th style="width:70px;text-align:center;">MP</th>
                                <th style="width:70px;text-align:center;">Days</th>
                                <th style="width:140px;text-align:right;">Rate / Hari</th>
                                <th style="width:140px;text-align:right;">Sub Total</th>
                                <th style="width:36px;"></th>
                            </tr>
                        </thead>
                        <tbody id="labors-tbody"></tbody>
                    </table>
                </div>
                <div class="card-footer bg-white d-flex align-items-center justify-content-between py-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-add-labor-2">
                        <i class="bi bi-plus-lg"></i> Tambah Labor
                    </button>
                    <div class="fw-semibold" style="font-size:13px;">
                        Total Labor: <span class="ms-2" id="disp-lab" style="font-family:monospace;color:#1B5DBC;">Rp 0</span>
                    </div>
                </div>
            </div>

        </div>{{-- end left --}}

        {{-- ── RIGHT COLUMN ── --}}
        <div class="col-12 col-xl-4 d-flex flex-column gap-3">

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <span class="fw-semibold">Ringkasan</span>
                </div>
                <div class="card-body">
                    <div class="summary-row"><span>Total Material</span><span class="summary-val" id="sum-mat">Rp 0</span></div>
                    <div class="summary-row"><span>Total Labor</span><span class="summary-val" id="sum-lab">Rp 0</span></div>
                    <div class="summary-row"><span>Subtotal</span><span class="summary-val" id="sum-sub">Rp 0</span></div>
                    <div class="summary-row align-items-start gap-2" style="flex-wrap:wrap;">
                        <div>
                            <div style="font-size:13px;margin-bottom:4px;">PPN (%)</div>
                            <input type="number" name="tax_percentage" id="tax_percentage"
                                   class="form-control form-control-sm" min="0" max="100" step="0.01"
                                   value="{{ old('tax_percentage', $isEdit ? $quotation->tax_percentage : 12) }}"
                                   style="width:80px;">
                        </div>
                        <span class="summary-val mt-4" id="sum-tax">Rp 0</span>
                    </div>
                    <div class="summary-row total-row">
                        <span>TOTAL</span>
                        <span class="summary-val" id="sum-total">Rp 0</span>
                    </div>
                    <input type="hidden" name="_subtotal_material" id="h-mat">
                    <input type="hidden" name="_subtotal_labor"    id="h-lab">
                    <input type="hidden" name="_subtotal"          id="h-sub">
                    <input type="hidden" name="_tax_amount"        id="h-tax">
                    <input type="hidden" name="_total"             id="h-total">
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <span class="fw-semibold">Terms & Conditions</span>
                </div>
                <div class="card-body">
                    <textarea name="notes" class="form-control form-control-sm" rows="6"
                              placeholder="1. This quotation is only valid through date above.&#10;2. Term of payment: ...&#10;3. Price exclude Tax 12%">{{ old('notes', $isEdit ? $quotation->notes : "1. This quotation is only valid through date above.\n2. To accept the quote, sign and return quoted sheet to the address above.\n3. Term of payment :\n   - 30%  After PO + TT 14 calendar days\n   - 40%  After delivery\n   - 30%  After 8 AST\n4. Price exclude Tax 12%\n5. Warranty : 12 months") }}</textarea>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-check-lg"></i>
                    {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Quotation' }}
                </button>
                <a href="{{ route('admin.quotations.index') }}" class="btn btn-outline-secondary text-center">Batal</a>
            </div>

        </div>{{-- end right --}}
    </div>
</form>

@endsection

@push('scripts')
<script>
/* ── seed data ── */
const initItems  = @json($oldItems);
const initLabors = @json($oldLabors);
let iIdx = 0, lIdx = 0;

const fmt = n => 'Rp ' + Math.round(n).toLocaleString('id-ID');
const esc = s => String(s ?? '').replace(/"/g,'&quot;').replace(/</g,'&lt;');

/* ══ MATERIAL rows ══════════════════════════════════════ */
function createItemRow(item = {}) {
    const idx   = iIdx++;
    const qty   = parseFloat(item.qty ?? 1) || 0;
    const price = parseFloat(item.unit_price ?? 0) || 0;
    const sub   = qty * price;

    const tr = document.createElement('tr');
    tr.dataset.idx = idx;
    tr.innerHTML = `
        <td class="item-no" id="ino-${idx}"></td>
        <td><input type="text"   name="items[${idx}][material_name]" class="item-input" required value="${esc(item.material_name)}" placeholder="Nama material / jasa"></td>
        <td><input type="text"   name="items[${idx}][description]"   class="item-input" value="${esc(item.description)}" placeholder="Keterangan"></td>
        <td><input type="text"   name="items[${idx}][unit]"          class="item-input" value="${esc(item.unit ?? 'Unit')}" style="text-align:center;" required></td>
        <td><input type="number" name="items[${idx}][qty]"           class="item-input item-qty"   min="0" step="any" value="${qty}"  style="text-align:right;" required></td>
        <td><input type="number" name="items[${idx}][unit_price]"    class="item-input item-price" min="0" step="any" value="${price}" style="text-align:right;" required></td>
        <td class="subtotal-cell" id="isub-${idx}">${fmt(sub)}</td>
        <td><button type="button" class="btn-remove-row" onclick="removeItemRow(this)"><i class="bi bi-x-lg"></i></button></td>
    `;
    tr.querySelector('.item-qty').addEventListener('input',   () => updateItemRow(tr));
    tr.querySelector('.item-price').addEventListener('input', () => updateItemRow(tr));
    return tr;
}
function updateItemRow(tr) {
    const idx   = tr.dataset.idx;
    const qty   = parseFloat(tr.querySelector('.item-qty').value)   || 0;
    const price = parseFloat(tr.querySelector('.item-price').value) || 0;
    tr.querySelector(`#isub-${idx}`).textContent = fmt(qty * price);
    recalc();
}
function removeItemRow(btn) {
    btn.closest('tr').remove();
    reorderNums('items-tbody', 'ino-');
    recalc();
}
function addItemRow(item = {}) {
    const tbody = document.getElementById('items-tbody');
    const tr = createItemRow(item);
    tbody.appendChild(tr);
    reorderNums('items-tbody', 'ino-');
    recalc();
    tr.querySelector('.item-input').focus();
}

/* ══ LABOR rows ═════════════════════════════════════════ */
function createLaborRow(labor = {}) {
    const idx  = lIdx++;
    const mp   = parseInt(labor.mp   ?? 1) || 0;
    const days = parseFloat(labor.days ?? 1) || 0;
    const rate = parseFloat(labor.rate ?? 0) || 0;
    const sub  = mp * days * rate;

    const tr = document.createElement('tr');
    tr.dataset.idx = idx;
    tr.innerHTML = `
        <td class="item-no" id="lno-${idx}"></td>
        <td><input type="text"   name="labors[${idx}][labor_name]" class="item-input" required value="${esc(labor.labor_name)}" placeholder="Nama pekerjaan"></td>
        <td><input type="number" name="labors[${idx}][mp]"         class="item-input labor-mp"   min="0" value="${mp}"   style="text-align:center;" required></td>
        <td><input type="number" name="labors[${idx}][days]"       class="item-input labor-days" min="0" step="any" value="${days}" style="text-align:center;" required></td>
        <td><input type="number" name="labors[${idx}][rate]"       class="item-input labor-rate" min="0" step="any" value="${rate}" style="text-align:right;" required></td>
        <td class="subtotal-cell" id="lsub-${idx}">${fmt(sub)}</td>
        <td><button type="button" class="btn-remove-row" onclick="removeLaborRow(this)"><i class="bi bi-x-lg"></i></button></td>
    `;
    tr.querySelector('.labor-mp').addEventListener('input',   () => updateLaborRow(tr));
    tr.querySelector('.labor-days').addEventListener('input', () => updateLaborRow(tr));
    tr.querySelector('.labor-rate').addEventListener('input', () => updateLaborRow(tr));
    return tr;
}
function updateLaborRow(tr) {
    const idx  = tr.dataset.idx;
    const mp   = parseInt(tr.querySelector('.labor-mp').value)    || 0;
    const days = parseFloat(tr.querySelector('.labor-days').value) || 0;
    const rate = parseFloat(tr.querySelector('.labor-rate').value) || 0;
    tr.querySelector(`#lsub-${idx}`).textContent = fmt(mp * days * rate);
    recalc();
}
function removeLaborRow(btn) {
    btn.closest('tr').remove();
    reorderNums('labors-tbody', 'lno-');
    recalc();
}
function addLaborRow(labor = {}) {
    const tbody = document.getElementById('labors-tbody');
    const tr = createLaborRow(labor);
    tbody.appendChild(tr);
    reorderNums('labors-tbody', 'lno-');
    recalc();
}

/* ══ Helpers ════════════════════════════════════════════ */
function reorderNums(tbodyId, prefix) {
    document.querySelectorAll(`#${tbodyId} tr`).forEach((tr, i) => {
        const el = tr.querySelector(`[id^="${prefix}"]`);
        if (el) el.textContent = i + 1;
    });
}

function recalc() {
    let mat = 0, lab = 0;
    document.querySelectorAll('#items-tbody tr').forEach(tr => {
        mat += (parseFloat(tr.querySelector('.item-qty')?.value)   || 0)
             * (parseFloat(tr.querySelector('.item-price')?.value) || 0);
    });
    document.querySelectorAll('#labors-tbody tr').forEach(tr => {
        lab += (parseInt(tr.querySelector('.labor-mp')?.value)     || 0)
             * (parseFloat(tr.querySelector('.labor-days')?.value) || 0)
             * (parseFloat(tr.querySelector('.labor-rate')?.value) || 0);
    });
    const sub   = mat + lab;
    const tax   = sub * ((parseFloat(document.getElementById('tax_percentage').value) || 0) / 100);
    const total = sub + tax;

    document.getElementById('disp-mat').textContent = fmt(mat);
    document.getElementById('disp-lab').textContent = fmt(lab);
    document.getElementById('sum-mat').textContent  = fmt(mat);
    document.getElementById('sum-lab').textContent  = fmt(lab);
    document.getElementById('sum-sub').textContent  = fmt(sub);
    document.getElementById('sum-tax').textContent  = fmt(tax);
    document.getElementById('sum-total').textContent= fmt(total);

    document.getElementById('h-mat').value   = mat.toFixed(2);
    document.getElementById('h-lab').value   = lab.toFixed(2);
    document.getElementById('h-sub').value   = sub.toFixed(2);
    document.getElementById('h-tax').value   = tax.toFixed(2);
    document.getElementById('h-total').value = total.toFixed(2);
}

/* ══ Boot ═══════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
    (initItems.length  ? initItems  : [{}]).forEach(i => addItemRow(i));
    (initLabors.length ? initLabors : [{}]).forEach(l => addLaborRow(l));

    document.getElementById('btn-add-item').addEventListener('click',   () => addItemRow());
    document.getElementById('btn-add-item-2').addEventListener('click', () => addItemRow());
    document.getElementById('btn-add-labor').addEventListener('click',  () => addLaborRow());
    document.getElementById('btn-add-labor-2').addEventListener('click',() => addLaborRow());
    document.getElementById('tax_percentage').addEventListener('input', recalc);
});
</script>
@endpush
