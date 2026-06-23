@extends('layouts.app')

@php
    $isEdit        = isset($invoice);
    $action        = $isEdit ? route('admin.invoices.update', $invoice) : route('admin.invoices.store');
    $oldItems      = old('items',       $isEdit ? $invoice->items->load('materials')->toArray()      : []);
    $oldLabors     = old('labors',      $isEdit ? $invoice->labors->toArray()     : []);
    $oldOtherCosts = old('other_costs', $isEdit ? $invoice->otherCosts->toArray() : []);
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
    .subtotal-cell { font-family: monospace; font-size: 12.5px; color: #374151; text-align: right; white-space: nowrap; }
    .btn-remove-row {
        background: none; border: none; color: #cbd5e1; cursor: pointer;
        padding: 4px 6px; border-radius: 6px; transition: all .15s; font-size: 15px;
        display: flex; align-items: center;
    }
    .btn-remove-row:hover { color: #ef4444; background: #fee2e2; }
    .table-section-header th { background: #1e3a5f !important; color: #fff !important; font-size: 11px; text-transform: uppercase; letter-spacing: .05em; }
    .labor-header th { background: #1B5DBC !important; color: #fff !important; font-size: 11px; text-transform: uppercase; letter-spacing: .05em; }
    .product-card {
        background: #fff; border: 1px solid #e2e8f0; border-radius: 8px;
        margin-bottom: 12px; overflow: hidden;
    }
    .product-card-header {
        background: #f8fafc; padding: 10px 14px;
        border-bottom: 1px solid #e2e8f0;
        display: flex; align-items: center; gap: 10px;
    }
    .product-card-header .card-num {
        font-family: monospace; font-weight: 700; font-size: 13px; color: #1B5DBC;
        background: #dbeafe; border-radius: 4px; padding: 2px 8px; min-width: 32px; text-align: center;
    }
    .product-card-body { padding: 12px 14px; }
    .mat-input-sm {
        border: 1px solid #e2e8f0; border-radius: 4px;
        padding: 3px 6px; font-size: 12px; width: 100%;
        background: #fff; font-family: inherit; outline: none;
    }
    .mat-input-sm:focus { border-color: #1B5DBC; box-shadow: 0 0 0 2px rgba(27,93,188,.10); }
    .btn-add-mat {
        background: none; border: 1px dashed #cbd5e1; color: #64748b;
        cursor: pointer; padding: 3px 10px; border-radius: 5px;
        font-size: 11px; transition: all .15s;
    }
    .btn-add-mat:hover { border-color: #1B5DBC; color: #1B5DBC; background: #f0f6ff; }
    .btn-remove-mat {
        background: none; border: none; color: #cbd5e1; cursor: pointer;
        padding: 2px 4px; border-radius: 4px; font-size: 12px;
    }
    .btn-remove-mat:hover { color: #ef4444; background: #fee2e2; }
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
                             <label class="form-label fw-semibold" style="font-size:13px">Nomor PO</label>
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

            {{-- ── PRODUCT CARDS (Item Produksi) ── --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <span class="fw-semibold">Item Produksi</span>
                </div>
                <div class="card-body" id="items-container"></div>
            </div>

            {{-- ── LABOR ITEMS ── --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <span class="fw-semibold">Biaya Tenaga Kerja (Labor)</span>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0" style="font-size:13px;">
                        <thead>
                            <tr class="labor-header">
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
            </div>

            {{-- Biaya Lain-Lain --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <span class="fw-semibold">Biaya Lain-Lain</span>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0" style="font-size:13px;">
                        <thead>
                            <tr class="labor-header">
                                <th style="width:36px;">#</th>
                                <th style="min-width:180px;">Nama Biaya <span class="text-warning">*</span></th>
                                <th style="width:80px;text-align:center;">Qty</th>
                                <th style="width:140px;text-align:right;">Rate</th>
                                <th style="width:140px;text-align:right;">Sub Total</th>
                                <th style="width:36px;"></th>
                            </tr>
                        </thead>
                        <tbody id="other-costs-tbody"></tbody>
                    </table>
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
                    <div class="summary-row"><span>Subtotal Produksi</span><span class="summary-val" id="sum-mat">Rp 0</span></div>
                    <div class="summary-row"><span>Subtotal Labor</span><span class="summary-val" id="sum-lab">Rp 0</span></div>
                    <div class="summary-row"><span>Subtotal Biaya Lain-Lain</span><span class="summary-val" id="sum-oth">Rp 0</span></div>
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
                    <div class="summary-row align-items-start gap-2" style="flex-wrap:wrap;">
                        <div>
                            <div style="font-size:13px;margin-bottom:4px;">Diskon (Rp)</div>
                            <div class="input-group" style="max-width:160px;">
                                <div class="input-group-text" style="background:#f1f5f9;font-size:13px;">Rp</div>
                                <input type="text" id="discount-display"
                                       class="form-control form-control-sm" placeholder="0"
                                       style="text-align:right;"
                                       oninput="formatNumberInput(this, 'discount')">
                                <input type="hidden" name="discount" id="discount"
                                       value="{{ old('discount', $isEdit ? $invoice->discount : 0) }}">
                            </div>
                        </div>
                        <span class="summary-val mt-4" id="sum-discount">Rp 0</span>
                    </div>
                    <div class="summary-row total-row">
                        <span>TOTAL</span>
                        <span class="summary-val" id="sum-total">Rp 0</span>
                    </div>
                    <input type="hidden" name="discount"           id="h-discount">
                    <input type="hidden" name="subtotal"           id="h-mat">
                    <input type="hidden" name="subtotal_labor"     id="h-lab">
                    <input type="hidden" name="subtotal_other_cost" id="h-oth">
                    <input type="hidden" name="tax_amount"         id="h-tax">
                    <input type="hidden" name="total"              id="h-total">
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
const initItems      = @json($oldItems);
const initLabors     = @json($oldLabors);
const initOtherCosts = @json($oldOtherCosts);
let iIdx = 0, lIdx = 0, oIdx = 0;
let mIdx = {};

const fmt = n => 'Rp ' + Math.round(n).toLocaleString('id-ID');
const esc = s => String(s ?? '').replace(/"/g,'"').replace(/</g,'<');

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

        const discountEl = document.getElementById('discount');
        const discountDisplayEl = document.getElementById('discount-display');
        if (discountEl && discountDisplayEl) {
            discountEl.value = data.discount ?? 0;
            discountDisplayEl.value = parseFloat(data.discount || 0).toLocaleString('id-ID');
        }

        document.getElementById('client_name').value        = data.client_name || '';
        document.getElementById('client_company').value     = data.client_company || '';
        document.getElementById('client_attention').value   = data.client_attention || '';
        document.getElementById('client_cc').value          = data.client_cc || '';
        document.getElementById('client_email').value       = data.client_email || '';
        document.getElementById('client_address').value     = data.client_address || '';
        document.getElementById('description').value        = data.description || '';

        // Clear & load items with materials
        document.getElementById('items-container').innerHTML = '';
        iIdx = 0;
        mIdx = {};
        if (data.items && data.items.length) {
            data.items.forEach(it => addProductCard({
                item_name: it.item_name ?? '',
                description: it.description ?? '',
                unit: it.unit ?? 'Unit',
                qty: it.qty ?? 1,
                unit_price: it.unit_price ?? 0,
                materials: it.materials ?? [],
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

        // Load other costs
        document.getElementById('other-costs-tbody').innerHTML = '';
        oIdx = 0;
        if (data.other_costs && data.other_costs.length) {
            data.other_costs.forEach(oc => addOtherCostRow({
                cost_name: oc.cost_name ?? '',
                qty: oc.qty ?? 1,
                rate: oc.rate ?? 0,
            }));
        }

        // Show labor & other costs cards if hidden
        if (data.labors && data.labors.length) {
            document.getElementById('labors-tbody').closest('.card').classList.remove('d-none');
        }
        if (data.other_costs && data.other_costs.length) {
            document.getElementById('other-costs-tbody').closest('.card').classList.remove('d-none');
        }

        recalc();
    } catch (err) {
        console.error(err);
        alert('Gagal memuat data Sales Order. Silakan coba lagi.');
    }
});

/* ══ PRODUCT CARDS with MATERIALS ══════════════════════ */
function createProductCard(item = {}) {
    const pIdx = iIdx++;
    mIdx['p' + pIdx] = 0;
    const qty   = parseFloat(item.qty ?? 1) || 0;
    const price = parseFloat(item.unit_price ?? 0) || 0;
    const materials = item.materials || [];

    const div = document.createElement('div');
    div.className = 'product-card';
    div.dataset.pIdx = pIdx;

    div.innerHTML = `
        <div class="product-card-header">
            <span class="card-num">${pIdx + 1}</span>
            <div style="flex:1; display:flex; gap:8px; flex-wrap:wrap;">
                <input type="text" name="items[${pIdx}][item_name]" class="item-input" placeholder="Nama item *" value="${esc(item.item_name)}" style="flex:2;min-width:140px;" required>
                <input type="text" name="items[${pIdx}][description]" class="item-input" placeholder="Deskripsi" value="${esc(item.description)}" style="flex:2;min-width:140px;">
                <input type="text" name="items[${pIdx}][unit]" class="item-input" placeholder="Satuan" value="${esc(item.unit ?? 'Unit')}" style="flex:0 0 70px;text-align:center;" required>
                <input type="number" name="items[${pIdx}][qty]" class="item-input item-qty" min="0" step="any" value="${qty}" style="flex:0 0 70px;text-align:right;" required onchange="updateProductCardSub(this.closest('.product-card'))">
                <input type="number" name="items[${pIdx}][unit_price]" class="item-input item-price" min="0" step="any" value="${price}" style="flex:0 0 110px;text-align:right;" required onchange="updateProductCardSub(this.closest('.product-card'))">
                <span class="subtotal-cell" id="isub-${pIdx}">Rp ${(qty * price).toLocaleString('id-ID')}</span>
            </div>
            <button type="button" class="btn-remove-row" onclick="removeProduct(this)" title="Hapus item"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="product-card-body">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:6px;">
                <span style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase;">Material / Bahan Baku</span>
                <button type="button" class="btn-add-mat" onclick="addMaterialRow(this)" data-p="${pIdx}">
                    <i class="bi bi-plus"></i> Tambah Material
                </button>
            </div>
            <table class="table table-sm mb-0" style="font-size:12px;">
                <thead>
                    <tr style="background:#f1f5f9;">
                        <th style="width:28px;">#</th>
                        <th>Nama Material</th>
                        <th style="width:60px;">Satuan</th>
                        <th style="width:70px;text-align:right;">Qty</th>
                        <th style="width:100px;text-align:right;">Harga Satuan</th>
                        <th style="width:100px;text-align:right;">Subtotal</th>
                        <th style="width:28px;"></th>
                    </tr>
                </thead>
                <tbody class="mat-tbody"></tbody>
            </table>
        </div>
    `;

    // Seed materials if any
    if (materials.length) {
        const btnAddMat = div.querySelector('.btn-add-mat');
        materials.forEach(mat => addMaterialRow(btnAddMat, {
            asset_id: mat.asset_id ?? '',
            material_name: mat.material_name ?? '',
            qty_required: mat.qty_required ?? 0,
            satuan: mat.satuan ?? 'pcs',
            unit_price: mat.unit_price ?? 0,
        }));
    }

    return div;
}

function updateProductCardSub(card) {
    const pIdx  = card.dataset.pIdx;
    const qty   = parseFloat(card.querySelector('.item-qty')?.value) || 0;
    const price = parseFloat(card.querySelector('.item-price')?.value) || 0;
    const el = card.querySelector(`#isub-${pIdx}`);
    if (el) el.textContent = 'Rp ' + Math.round(qty * price).toLocaleString('id-ID');
    recalc();
}

function addProductCard(item = {}) {
    const container = document.getElementById('items-container');
    const card = createProductCard(item);
    container.appendChild(card);
    renumberProducts();
    recalc();
    card.querySelector('.item-input')?.focus();
}

function removeProduct(btn) {
    const card = btn.closest('.product-card');
    delete mIdx['p' + card.dataset.pIdx];
    card.remove();
    renumberProducts();
    recalc();
}

function renumberProducts() {
    document.querySelectorAll('#items-container .product-card').forEach((card, i) => {
        card.querySelector('.card-num').textContent = i + 1;
        card.dataset.pIdx = i;
        card.querySelectorAll('[name]').forEach(el => {
            const name = el.getAttribute('name');
            if (name) {
                el.setAttribute('name', name.replace(/items\[\d+\]/, 'items[' + i + ']'));
            }
        });
        const btn = card.querySelector('.btn-add-mat');
        if (btn) btn.dataset.p = i;
        const subEl = card.querySelector('[id^="isub-"]');
        if (subEl) subEl.id = 'isub-' + i;
    });
}

/* ══ Material rows inside product cards ═══════════════ */
function createMaterialRow(pIdx, mat = {}) {
    const mSeq = mIdx['p' + pIdx] = (mIdx['p' + pIdx] || 0) + 1;
    const qty   = parseFloat(mat.qty_required ?? 0) || 0;
    const price = parseFloat(mat.unit_price ?? 0) || 0;

    const tr = document.createElement('tr');
    tr.dataset.mi = mSeq;
    tr.innerHTML = `
        <td style="text-align:center;color:#94a3b8;">${mSeq}</td>
        <td>
            <input type="text" name="items[${pIdx}][materials][${mSeq}][material_name]" class="mat-input-sm" required value="${esc(mat.material_name || '')}" placeholder="Nama material">
            ${mat.asset_id ? `<input type="hidden" name="items[${pIdx}][materials][${mSeq}][asset_id]" value="${mat.asset_id}">` : ''}
        </td>
        <td><input type="text" name="items[${pIdx}][materials][${mSeq}][satuan]" class="mat-input-sm" value="${esc(mat.satuan || 'pcs')}" style="text-align:center;"></td>
        <td><input type="number" name="items[${pIdx}][materials][${mSeq}][qty_required]" class="mat-input-sm mat-qty" min="0" step="any" value="${qty}" style="text-align:right;" onchange="updateMatRow(this)"></td>
        <td><input type="number" name="items[${pIdx}][materials][${mSeq}][unit_price]" class="mat-input-sm mat-price" min="0" step="any" value="${price}" style="text-align:right;" onchange="updateMatRow(this)"></td>
        <td style="text-align:right;font-weight:600;color:#1B5DBC;">${fmt(qty * price)}</td>
        <td><button type="button" class="btn-remove-mat" onclick="removeMaterialRow(this)"><i class="bi bi-x"></i></button></td>
    `;
    return tr;
}

function addMaterialRow(btn, mat = {}) {
    const pIdx  = btn.dataset.p;
    const card  = btn.closest('.product-card');
    const tbody = card.querySelector('.mat-tbody');
    const tr = createMaterialRow(pIdx, mat);
    tbody.appendChild(tr);
    renumberMaterials(card);
    recalc();
}

function updateMatRow(el) {
    const tr = el.closest('tr');
    const qty = parseFloat(tr.querySelector('.mat-qty')?.value) || 0;
    const price = parseFloat(tr.querySelector('.mat-price')?.value) || 0;
    const tds = tr.querySelectorAll('td');
    tds[tds.length - 2].textContent = fmt(qty * price);
    recalc();
}

function removeMaterialRow(btn) {
    const card = btn.closest('.product-card');
    btn.closest('tr').remove();
    renumberMaterials(card);
    recalc();
}

function renumberMaterials(card) {
    card.querySelectorAll('.mat-tbody tr').forEach((tr, i) => {
        tr.querySelector('td').textContent = i + 1;
    });
}

/* ══ LABOR rows ═════════════════════════════════════════ */
function createLaborRow(labor = {}) {
    const idx  = lIdx++;
    const mp   = parseInt(labor.mp   ?? 1) || 0;
    const days = parseFloat(labor.days ?? 0) || 0;
    const rate = parseFloat(labor.rate ?? 0) || 0;
    const sub  = parseFloat(labor.subtotal ?? (mp * days * rate)) || 0;

    const tr = document.createElement('tr');
    tr.dataset.idx = idx;
    tr.innerHTML = `
        <td class="item-no" id="lno-${idx}"></td>
        <td><input type="text"   name="labors[${idx}][labor_name]" class="item-input" required value="${esc(labor.labor_name)}" placeholder="Nama pekerjaan"></td>
        <td><input type="number" name="labors[${idx}][mp]"   class="item-input labor-mp"   min="1" step="1" value="${mp}"   style="text-align:center;" required></td>
        <td><input type="number" name="labors[${idx}][days]" class="item-input labor-days" min="0" step="any" value="${days}" style="text-align:center;" required></td>
        <td><input type="number" name="labors[${idx}][rate]" class="item-input labor-rate" min="0" step="any" value="${rate}" style="text-align:right;" required></td>
        <td class="subtotal-cell" id="lsub-${idx}">${fmt(sub)}</td>
        <td><button type="button" class="btn-remove-row" onclick="removeLaborRow(this)"><i class="bi bi-x-lg"></i></button></td>
    `;
    tr.querySelector('.labor-mp')?.addEventListener('input',   () => updateLaborRow(tr));
    tr.querySelector('.labor-days')?.addEventListener('input', () => updateLaborRow(tr));
    tr.querySelector('.labor-rate')?.addEventListener('input', () => updateLaborRow(tr));
    return tr;
}

function updateLaborRow(tr) {
    const idx  = tr.dataset.idx;
    const mp   = parseInt(tr.querySelector('.labor-mp')?.value)   || 0;
    const days = parseFloat(tr.querySelector('.labor-days')?.value) || 0;
    const rate = parseFloat(tr.querySelector('.labor-rate')?.value) || 0;
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
    tr.querySelector('.item-input')?.focus();
}

/* ══ OTHER COSTS rows ══════════════════════════════════ */
function createOtherCostRow(cost = {}) {
    const idx  = oIdx++;
    const qty  = parseFloat(cost.qty  ?? 1) || 0;
    const rate = parseFloat(cost.rate ?? 0) || 0;
    const sub  = qty * rate;

    const tr = document.createElement('tr');
    tr.dataset.idx = idx;
    tr.innerHTML = `
        <td class="item-no" id="ono-${idx}"></td>
        <td><input type="text"   name="other_costs[${idx}][cost_name]" class="item-input" required value="${esc(cost.cost_name)}" placeholder="Nama biaya"></td>
        <td><input type="number" name="other_costs[${idx}][qty]"       class="item-input oc-qty"   min="0" step="any" value="${qty}"  style="text-align:center;" required></td>
        <td><input type="number" name="other_costs[${idx}][rate]"      class="item-input oc-rate"  min="0" step="any" value="${rate}" style="text-align:right;" required></td>
        <td class="subtotal-cell" id="osub-${idx}">${fmt(sub)}</td>
        <td><button type="button" class="btn-remove-row" onclick="removeOtherCostRow(this)"><i class="bi bi-x-lg"></i></button></td>
    `;
    tr.querySelector('.oc-qty')?.addEventListener('input',  () => updateOtherCostRow(tr));
    tr.querySelector('.oc-rate')?.addEventListener('input', () => updateOtherCostRow(tr));
    return tr;
}

function updateOtherCostRow(tr) {
    const idx  = tr.dataset.idx;
    const qty  = parseFloat(tr.querySelector('.oc-qty')?.value)  || 0;
    const rate = parseFloat(tr.querySelector('.oc-rate')?.value) || 0;
    tr.querySelector(`#osub-${idx}`).textContent = fmt(qty * rate);
    recalc();
}

function removeOtherCostRow(btn) {
    btn.closest('tr').remove();
    reorderNums('other-costs-tbody', 'ono-');
    recalc();
}

function addOtherCostRow(cost = {}) {
    const tbody = document.getElementById('other-costs-tbody');
    const tr = createOtherCostRow(cost);
    tbody.appendChild(tr);
    reorderNums('other-costs-tbody', 'ono-');
    recalc();
}

/* ══ Helpers ════════════════════════════════════════════ */
function reorderNums(tbodyId, prefix) {
    document.querySelectorAll(`#${tbodyId} tr`).forEach((tr, i) => {
        const el = tr.querySelector(`[id^="${prefix}"]`);
        if (el) el.textContent = i + 1;
    });
}

// Number formatting helper
function formatNumberInput(el, hiddenId) {
    let val = el.value.replace(/[^0-9]/g, '');
    if (val === '') val = '0';
    document.getElementById(hiddenId).value = parseFloat(val);
    el.value = parseFloat(val).toLocaleString('id-ID');
    recalc();
}

function recalc() {
    let mat = 0, lab = 0, oth = 0;

    // Product subtotal (qty * unit_price)
    document.querySelectorAll('#items-container .product-card').forEach(card => {
        mat += (parseFloat(card.querySelector('.item-qty')?.value)   || 0)
             * (parseFloat(card.querySelector('.item-price')?.value) || 0);
    });

    // Material subtotals (from sub-rows within product cards) — masuk ke total produksi
    document.querySelectorAll('#items-container .mat-qty').forEach(el => {
        const tr = el.closest('tr');
        const qty   = parseFloat(el.value) || 0;
        const price = parseFloat(tr.querySelector('.mat-price')?.value) || 0;
        mat += qty * price;
    });

    document.querySelectorAll('#labors-tbody tr').forEach(tr => {
        lab += (parseInt(tr.querySelector('.labor-mp')?.value)     || 0)
             * (parseFloat(tr.querySelector('.labor-days')?.value) || 0)
             * (parseFloat(tr.querySelector('.labor-rate')?.value) || 0);
    });

    document.querySelectorAll('#other-costs-tbody tr').forEach(tr => {
        oth += (parseFloat(tr.querySelector('.oc-qty')?.value)  || 0)
             * (parseFloat(tr.querySelector('.oc-rate')?.value) || 0);
    });

    const sub   = mat + lab + oth;
    const discount = parseFloat(document.getElementById('discount').value) || 0;
    
    // Perhitungan: Diskon dikurangi SEBELUM pajak
    const taxableBase = Math.max(sub - discount, 0);  // Dasar pengenaan pajak (tidak boleh negatif)
    const tax   = taxableBase * ((parseFloat(document.getElementById('tax_percentage').value) || 0) / 100);
    const total = taxableBase + tax;

    document.getElementById('sum-mat').textContent      = fmt(mat);
    document.getElementById('sum-lab').textContent      = fmt(lab);
    document.getElementById('sum-oth').textContent      = fmt(oth);
    document.getElementById('sum-sub').textContent      = fmt(sub);
    document.getElementById('sum-tax').textContent      = fmt(tax);
    document.getElementById('sum-discount').textContent = fmt(discount);
    document.getElementById('sum-total').textContent    = fmt(Math.max(total, 0));

    document.getElementById('h-mat').value      = mat.toFixed(2);
    document.getElementById('h-lab').value      = lab.toFixed(2);
    document.getElementById('h-oth').value      = oth.toFixed(2);
    document.getElementById('h-tax').value      = tax.toFixed(2);
    document.getElementById('h-discount').value = discount.toFixed(2);
    document.getElementById('h-total').value    = Math.max(total, 0).toFixed(2);
}

/* ══ Tax percentage & discount change ═══════════════════ */
    document.getElementById('tax_percentage')?.addEventListener('input', recalc);
    document.getElementById('discount-display')?.addEventListener('input', recalc);

    // Format discount initial value
    const discountEl = document.getElementById('discount');
    const discountDisplayEl = document.getElementById('discount-display');
    if (discountEl && discountDisplayEl) {
        discountDisplayEl.value = parseFloat(discountEl.value || 0).toLocaleString('id-ID');
    }

/* ══ Boot ═══════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
    (initItems.length  ? initItems  : [{}]).forEach(i => addProductCard(i));
    (initLabors.length ? initLabors : []).forEach(l => addLaborRow(l));
    (initOtherCosts.length ? initOtherCosts : []).forEach(c => addOtherCostRow(c));

    // Hide labor card if no data loaded
    if (!initLabors.length) {
        document.getElementById('labors-tbody').closest('.card').classList.add('d-none');
    }
    // Hide other costs card if no data loaded
    if (!initOtherCosts.length) {
        document.getElementById('other-costs-tbody').closest('.card').classList.add('d-none');
    }
});
</script>
@endpush
