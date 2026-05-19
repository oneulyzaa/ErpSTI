@extends('layouts.app')

@php
    $isEdit      = isset($invoice);
    $action      = $isEdit ? route('admin.invoices.update', $invoice) : route('admin.invoices.store');
    $oldItems    = old('items',  $isEdit ? $invoice->items->toArray()  : []);
    $oldLabors   = old('labors', $isEdit ? $invoice->labors->toArray() : []);
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
    .item-input {
        border: 1.5px solid #e2e8f0; border-radius: 6px;
        padding: 6px 10px; font-size: 13px; width: 100%;
        background: #fff; font-family: inherit; outline: none;
        transition: border-color .15s, box-shadow .15s;
    }
    .item-input:focus { border-color: #1B5DBC; box-shadow: 0 0 0 3px rgba(27,93,188,.12); }
    .item-no { font-family: monospace; font-size: 12px; color: #94a3b8; text-align: center; width: 36px; }
    .btn-remove-row {
        background: none; border: none; color: #cbd5e1; cursor: pointer;
        padding: 4px 6px; border-radius: 6px; transition: all .15s; font-size: 15px;
        display: flex; align-items: center;
    }
    .btn-remove-row:hover { color: #ef4444; background: #fee2e2; }
    .table-section-header th { background: #1e3a5f !important; color: #fff !important; font-size: 11px; text-transform: uppercase; letter-spacing: .05em; }
    .table-section-header2 th { background: #2d5a27 !important; color: #fff !important; font-size: 11px; text-transform: uppercase; letter-spacing: .05em; }
    .total-display { font-family: monospace; font-size: 15px; font-weight: 700; color: #1B5DBC; }
    .total-label { font-size: 13px; color: #475569; }
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
                        <label class="form-label fw-semibold" style="font-size:13px">Deskripsi</label>
                        <textarea name="description" id="description" class="form-control form-control-sm" rows="2"
                                  placeholder="Deskripsi pekerjaan...">{{ old('description', $isEdit ? $invoice->description : '') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- ── Produksi ITEMS ── --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <span class="fw-semibold">Item Produksi</span>
                    <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-1" id="btn-add-item">
                        <i class="bi bi-plus-lg"></i> Tambah Item
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0" style="font-size:13px;">
                        <thead>
                            <tr class="table-section-header">
                                <th style="width:36px;">#</th>
                                <th style="min-width:160px;">Nama Item <span class="text-warning">*</span></th>
                                <th style="min-width:120px;">Deskripsi</th>
                                <th style="width:70px;text-align:center;">Satuan</th>
                                <th style="width:70px;text-align:right;">Qty</th>
                                <th style="width:110px;text-align:right;">Harga</th>
                                <th style="width:110px;text-align:right;">Subtotal</th>
                                <th style="width:36px;"></th>
                            </tr>
                        </thead>
                        <tbody id="items-tbody"></tbody>
                    </table>
                </div>
                <div class="card-footer bg-white d-flex align-items-center py-2">
                    <button type="button" class="btn btn-outline-primary btn-sm" id="btn-add-item-2">
                        <i class="bi bi-plus-lg"></i> Tambah Item
                    </button>
                </div>
            </div>

            {{-- ── LABOR ITEMS ── --}}
            <div class="card border-0 shadow-sm d-none">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <span class="fw-semibold">Biaya Tenaga Kerja (Labor)</span>
                    <button type="button" class="btn btn-success btn-sm d-flex align-items-center gap-1" id="btn-add-labor">
                        <i class="bi bi-plus-lg"></i> Tambah Labor
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0" style="font-size:13px;">
                        <thead>
                            <tr class="table-section-header2">
                                <th style="width:36px;">#</th>
                                <th style="min-width:140px;">Nama Pekerjaan <span class="text-warning">*</span></th>
                                <th style="width:60px;text-align:center;">MP</th>
                                <th style="width:80px;text-align:center;">Hari</th>
                                <th style="width:110px;text-align:right;">Rate/Hari</th>
                                <th style="width:110px;text-align:right;">Subtotal</th>
                                <th style="width:36px;"></th>
                            </tr>
                        </thead>
                        <tbody id="labors-tbody"></tbody>
                    </table>
                </div>
                <div class="card-footer bg-white d-flex align-items-center py-2">
                    <button type="button" class="btn btn-outline-success btn-sm" id="btn-add-labor-2">
                        <i class="bi bi-plus-lg"></i> Tambah Labor
                    </button>
                </div>
            </div>

        </div>{{-- end left --}}

        {{-- ── RIGHT COLUMN ── --}}
        <div class="col-12 col-xl-4 d-flex flex-column gap-3">

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <span class="fw-semibold">Ringkasan Keuangan</span>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom:1px solid #f1f5f9;">
                        <span class="total-label">Subtotal Produksi</span>
                        <span class="total-display" id="display-subtotal">Rp 0</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom:1px solid #f1f5f9;">
                        <span class="total-label">Subtotal Labor</span>
                        <span class="total-display" id="display-labor">Rp 0</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom:1px solid #f1f5f9;">
                        <span class="total-label">
                            PPN (
                            <input type="number" name="tax_percentage" id="tax_percentage"
                                   class="form-control form-control-sm d-inline-block"
                                   style="width:55px;text-align:center;font-size:12px;"
                                   value="{{ old('tax_percentage', $isEdit ? $invoice->tax_percentage : 11) }}" min="0" max="100">
                            % )
                        </span>
                        <span class="total-display" id="display-tax">Rp 0</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-3">
                        <span class="fw-bold" style="font-size:15px;">Total</span>
                        <span class="total-display" style="font-size:18px;" id="display-total">Rp 0</span>
                    </div>

                    {{-- Hidden fields --}}
                    <input type="hidden" name="subtotal" id="input-subtotal" value="0">
                    <input type="hidden" name="subtotal_labor" id="input-labor" value="0">
                    <input type="hidden" name="tax_amount" id="input-tax" value="0">
                    <input type="hidden" name="total" id="input-total" value="0">
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
const initItems  = @json($oldItems);
const initLabors = @json($oldLabors);
let iIdx = 0;
let lIdx = 0;

const esc = s => String(s ?? '').replace(/"/g,'"').replace(/</g,'<');
const fmt = (n) => 'Rp ' + (n || 0).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 2 });

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
        document.getElementById('client_name').value        = data.client_name || '';
        document.getElementById('client_company').value     = data.client_company || '';
        document.getElementById('client_attention').value   = data.client_attention || '';
        document.getElementById('client_cc').value          = data.client_cc || '';
        document.getElementById('client_email').value       = data.client_email || '';
        document.getElementById('description').value        = data.description || '';

        // Load material items
        document.getElementById('items-tbody').innerHTML = '';
        iIdx = 0;
        if (data.items && data.items.length) {
            data.items.forEach(it => addItemRow({
                item_name: it.item_name ?? '',
                description: it.description ?? '',
                unit: it.unit ?? 'Unit',
                qty: it.qty ?? 1,
                unit_price: it.unit_price ?? 0,
                subtotal: it.subtotal ?? 0,
            }));
        }

        // Load labor items
        document.getElementById('labors-tbody').innerHTML = '';
        lIdx = 0;
        if (data.labors && data.labors.length) {
            data.labors.forEach(lb => addLaborRow({
                labor_name: lb.labor_name ?? '',
                mp: lb.mp ?? 1,
                days: lb.days ?? 0,
                rate: lb.rate ?? 0,
                subtotal: lb.subtotal ?? 0,
            }));
        }

        recalc();
    } catch (err) {
        console.error(err);
        alert('Gagal memuat data Sales Order. Silakan coba lagi.');
    }
});

/* ══ Material Item rows ════════════════════════════════════ */
function createItemRow(item = {}) {
    const idx = iIdx++;
    const qty = parseFloat(item.qty ?? 1) || 0;
    const price = parseFloat(item.unit_price ?? 0) || 0;
    const sub = parseFloat(item.subtotal ?? (qty * price)) || 0;

    const tr = document.createElement('tr');
    tr.dataset.idx = idx;
    tr.innerHTML = `
        <td class="item-no" id="ino-${idx}"></td>
        <td><input type="text"   name="items[${idx}][item_name]" class="item-input" required value="${esc(item.item_name)}" placeholder="Nama item"></td>
        <td><input type="text"   name="items[${idx}][description]" class="item-input" value="${esc(item.description)}" placeholder="Keterangan"></td>
        <td><input type="text"   name="items[${idx}][unit]"       class="item-input" value="${esc(item.unit ?? 'Unit')}" style="text-align:center;" required></td>
        <td><input type="number" name="items[${idx}][qty]"        class="item-input item-qty" min="0" step="any" value="${qty}" style="text-align:right;" required></td>
        <td><input type="number" name="items[${idx}][unit_price]" class="item-input item-price" min="0" step="any" value="${price}" style="text-align:right;" required></td>
        <td><input type="number" name="items[${idx}][subtotal]"   class="item-input item-subtotal" min="0" step="any" value="${sub}" style="text-align:right;font-weight:600;color:#1B5DBC;" readonly></td>
        <td><button type="button" class="btn-remove-row" onclick="removeItemRow(this)"><i class="bi bi-x-lg"></i></button></td>
    `;
    tr.querySelector('.item-qty')?.addEventListener('input', function() { calcRowSubtotal(this); recalc(); });
    tr.querySelector('.item-price')?.addEventListener('input', function() { calcRowSubtotal(this); recalc(); });
    return tr;
}

function calcRowSubtotal(el) {
    const tr = el.closest('tr');
    const qty = parseFloat(tr.querySelector('.item-qty')?.value) || 0;
    const price = parseFloat(tr.querySelector('.item-price')?.value) || 0;
    const sub = qty * price;
    tr.querySelector('.item-subtotal').value = sub.toFixed(2);
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

/* ══ Labor rows ═══════════════════════════════════════════ */
function createLaborRow(labor = {}) {
    const idx = lIdx++;
    const mp = parseInt(labor.mp ?? 1) || 1;
    const days = parseFloat(labor.days ?? 0) || 0;
    const rate = parseFloat(labor.rate ?? 0) || 0;
    const sub = parseFloat(labor.subtotal ?? (mp * days * rate)) || 0;

    const tr = document.createElement('tr');
    tr.dataset.idx = idx;
    tr.innerHTML = `
        <td class="item-no" id="lno-${idx}"></td>
        <td><input type="text"   name="labors[${idx}][labor_name]" class="item-input" required value="${esc(labor.labor_name)}" placeholder="Nama pekerjaan"></td>
        <td><input type="number" name="labors[${idx}][mp]"   class="item-input labor-mp" min="1" step="1" value="${mp}" style="text-align:center;" required></td>
        <td><input type="number" name="labors[${idx}][days]" class="item-input labor-days" min="0" step="any" value="${days}" style="text-align:center;" required></td>
        <td><input type="number" name="labors[${idx}][rate]" class="item-input labor-rate" min="0" step="any" value="${rate}" style="text-align:right;" required></td>
        <td><input type="number" name="labors[${idx}][subtotal]" class="item-input labor-subtotal" min="0" step="any" value="${sub}" style="text-align:right;font-weight:600;color:#1B5DBC;" readonly></td>
        <td><button type="button" class="btn-remove-row" onclick="removeLaborRow(this)"><i class="bi bi-x-lg"></i></button></td>
    `;
    tr.querySelector('.labor-mp')?.addEventListener('input', function() { calcLaborSubtotal(this); recalc(); });
    tr.querySelector('.labor-days')?.addEventListener('input', function() { calcLaborSubtotal(this); recalc(); });
    tr.querySelector('.labor-rate')?.addEventListener('input', function() { calcLaborSubtotal(this); recalc(); });
    return tr;
}

function calcLaborSubtotal(el) {
    const tr = el.closest('tr');
    const mp = parseInt(tr.querySelector('.labor-mp')?.value) || 0;
    const days = parseFloat(tr.querySelector('.labor-days')?.value) || 0;
    const rate = parseFloat(tr.querySelector('.labor-rate')?.value) || 0;
    const sub = mp * days * rate;
    tr.querySelector('.labor-subtotal').value = sub.toFixed(2);
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
    tr.querySelector('.item-input').focus();
}

/* ══ Helpers ════════════════════════════════════════════ */
function reorderNums(tbodyId, prefix) {
    document.querySelectorAll(`#${tbodyId} tr`).forEach((tr, i) => {
        const el = tr.querySelector(`[id^="${prefix}"]`);
        if (el) el.textContent = i + 1;
    });
}

function recalc() {
    let subtotal = 0;
    document.querySelectorAll('#items-tbody tr').forEach(tr => {
        subtotal += parseFloat(tr.querySelector('.item-subtotal')?.value) || 0;
    });

    let laborTotal = 0;
    document.querySelectorAll('#labors-tbody tr').forEach(tr => {
        laborTotal += parseFloat(tr.querySelector('.labor-subtotal')?.value) || 0;
    });

    const grandSubtotal = subtotal + laborTotal;
    const taxPct = parseFloat(document.getElementById('tax_percentage')?.value) || 0;
    const taxAmt = grandSubtotal * (taxPct / 100);
    const total  = grandSubtotal + taxAmt;

    document.getElementById('display-subtotal').textContent = fmt(subtotal);
    document.getElementById('display-labor').textContent    = fmt(laborTotal);
    document.getElementById('display-tax').textContent      = fmt(taxAmt);
    document.getElementById('display-total').textContent     = fmt(total);

    document.getElementById('input-subtotal').value = subtotal.toFixed(2);
    document.getElementById('input-labor').value    = laborTotal.toFixed(2);
    document.getElementById('input-tax').value      = taxAmt.toFixed(2);
    document.getElementById('input-total').value     = total.toFixed(2);
}

/* ══ Tax percentage change ══════════════════════════════ */
document.getElementById('tax_percentage')?.addEventListener('input', recalc);

/* ══ Boot ═══════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
    (initItems.length ? initItems : [{}]).forEach(i => addItemRow(i));
    (initLabors.length ? initLabors : []).forEach(l => addLaborRow(l));

    document.getElementById('btn-add-item').addEventListener('click',   () => addItemRow());
    document.getElementById('btn-add-item-2').addEventListener('click', () => addItemRow());
    document.getElementById('btn-add-labor').addEventListener('click',   () => addLaborRow());
    document.getElementById('btn-add-labor-2').addEventListener('click', () => addLaborRow());
});
</script>
@endpush