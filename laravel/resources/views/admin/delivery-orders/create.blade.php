@extends('layouts.app')

@php
    $isEdit      = isset($deliveryOrder);
    $action      = $isEdit ? route('admin.delivery-orders.update', $deliveryOrder) : route('admin.delivery-orders.store');
    $oldItems    = old('items',  $isEdit ? $deliveryOrder->items->toArray()  : []);
@endphp

@section('title', $isEdit ? 'Edit Delivery Order' : 'Buat Delivery Order Baru')
@section('breadcrumb', $isEdit ? 'Edit Delivery Order' : 'Buat Delivery Order')

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
    .material-row { background: #f9fafb; font-size: 12px; }
    .material-row td { padding: 4px 8px !important; }
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
</style>
@endpush

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">{{ $isEdit ? 'Edit Delivery Order' : 'Buat Delivery Order Baru' }}</h4>
        <p class="text-muted mb-0" style="font-size:13px">PT. Sistem Teknologi Integrator</p>
    </div>
    <a href="{{ route('admin.delivery-orders.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<form method="POST" action="{{ $action }}" id="do-form">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="row g-3 align-items-start">
        {{-- ── LEFT COLUMN ── --}}
        <div class="col-12 col-xl-8 d-flex flex-column gap-3">

            {{-- Info Delivery Order --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <span class="fw-semibold">Informasi Delivery Order</span>
                </div>
                <div class="card-body">
                    <div class="section-label">Nomor & Referensi</div>
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-sm-4">
                            <label class="form-label fw-semibold" style="font-size:13px">No. DO <span class="text-danger">*</span></label>
                            <input type="text" name="do_number"
                                   class="form-control form-control-sm @error('do_number') is-invalid @enderror"
                                   value="{{ old('do_number', $isEdit ? $deliveryOrder->do_number : $doNumber) }}" required>
                            @error('do_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-sm-4">
                            <label class="form-label fw-semibold" style="font-size:13px">Referensi Sales Order</label>
                            <select name="sales_order_id" id="sales_order_id" class="form-select form-select-sm"
                                    data-url-template="{{ route('admin.delivery-orders.so-data', ['salesOrder' => '__ID__']) }}">
                                <option value="">-- Pilih SO (opsional) --</option>
                                @foreach($salesOrders as $so)
                                    <option value="{{ $so->id }}"
                                        {{ old('sales_order_id', ($isEdit ? $deliveryOrder->sales_order_id : ($copySo?->id ?? ''))) == $so->id ? 'selected' : '' }}>
                                        {{ $so->so_number }} — {{ $so->client_company }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="so_number" id="so_number"
                                   value="{{ old('so_number', $isEdit ? $deliveryOrder->so_number : '') }}">
                        </div>
                        <div class="col-12 col-sm-4">
                            <label class="form-label fw-semibold" style="font-size:13px">Nomor PO</label>
                            <input type="text" name="nomor_po" id="nomor_po" class="form-control form-control-sm"
                                   value="{{ old('nomor_po', $isEdit ? $deliveryOrder->nomor_po : '') }}"
                                   placeholder="Auto-load dari SO">
                        </div>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-sm-4">
                            <label class="form-label fw-semibold" style="font-size:13px">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select form-select-sm" required>
                                @foreach(['draft'=>'Draft','confirmed'=>'Confirmed','shipped'=>'Shipped','delivered'=>'Delivered','cancelled'=>'Cancelled'] as $v=>$l)
                                    <option value="{{ $v }}" {{ old('status', $isEdit ? $deliveryOrder->status : 'draft') === $v ? 'selected' : '' }}>{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold" style="font-size:13px">Tanggal DO <span class="text-danger">*</span></label>
                            <input type="date" name="date" class="form-control form-control-sm @error('date') is-invalid @enderror"
                                   value="{{ old('date', $isEdit ? $deliveryOrder->date->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
                            @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold" style="font-size:13px">Tanggal Pengiriman</label>
                            <input type="date" name="delivery_date" class="form-control form-control-sm"
                                   value="{{ old('delivery_date', $isEdit && $deliveryOrder->delivery_date ? $deliveryOrder->delivery_date->format('Y-m-d') : '') }}">
                        </div>
                    </div>

                    <div class="section-label">Pilih Perusahaan (dari Master Client)</div>
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <select name="client_id" id="client-select" class="form-select form-select-sm"
                                    data-url-template="{{ route('admin.delivery-orders.client-data', ['client' => '__ID__']) }}">
                                <option value="">-- Pilih Perusahaan (opsional) --</option>
                                @isset($clients)
                                    @foreach($clients as $c)
                                        <option value="{{ $c->id }}"
                                            data-nama="{{ $c->nama_perusahaan }}"
                                            data-kontak="{{ $c->nama_kontak_perusahaan }}"
                                            data-email="{{ $c->email_perusahaan }}"
                                            data-alamat="{{ $c->alamat_pengiriman_perusahaan }}"
                                            {{ old('client_id', $isEdit ? ($deliveryOrder->client_id ?? '') : '') == $c->id ? 'selected' : '' }}>
                                            {{ $c->id_perusahaan }} — {{ $c->nama_perusahaan }}
                                        </option>
                                    @endforeach
                                @endisset
                            </select>
                        </div>
                    </div>

                    <div class="section-label">Info Klien</div>
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold" style="font-size:13px">Perusahaan</label>
                            <input type="text" name="client_company" id="client_company" class="form-control form-control-sm"
                                   value="{{ old('client_company', $isEdit ? $deliveryOrder->client_company : '') }}"
                                   placeholder="Auto-load dari SO">
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold" style="font-size:13px">Nama Kontak</label>
                            <input type="text" name="client_name" id="client_name" class="form-control form-control-sm"
                                   value="{{ old('client_name', $isEdit ? $deliveryOrder->client_name : '') }}"
                                   placeholder="Auto-load dari SO">
                        </div>
                        <div class="col-12 col-sm-4">
                            <label class="form-label fw-semibold" style="font-size:13px">Attn</label>
                            <input type="text" name="client_attention" id="client_attention" class="form-control form-control-sm"
                                   value="{{ old('client_attention', $isEdit ? $deliveryOrder->client_attention : '') }}">
                        </div>
                        <div class="col-12 col-sm-4">
                            <label class="form-label fw-semibold" style="font-size:13px">CC</label>
                            <input type="text" name="client_cc" id="client_cc" class="form-control form-control-sm"
                                   value="{{ old('client_cc', $isEdit ? $deliveryOrder->client_cc : '') }}">
                        </div>
                        <div class="col-12 col-sm-4">
                            <label class="form-label fw-semibold" style="font-size:13px">Email</label>
                            <input type="email" name="client_email" id="client_email" class="form-control form-control-sm"
                                   value="{{ old('client_email', $isEdit ? $deliveryOrder->client_email : '') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px">Alamat Tujuan</label>
                        <textarea name="destination_address" id="destination_address" class="form-control form-control-sm" rows="2"
                                  placeholder="Alamat pengiriman...">{{ old('destination_address', $isEdit ? $deliveryOrder->destination_address : '') }}</textarea>
                    </div>
                    <div>
                        <label class="form-label fw-semibold" style="font-size:13px">Deskripsi</label>
                        <textarea name="description" id="description" class="form-control form-control-sm" rows="2"
                                  placeholder="Deskripsi pengiriman...">{{ old('description', $isEdit ? $deliveryOrder->description : '') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- ── PRODUCT CARDS ── --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <span class="fw-semibold">Item Pengiriman</span>
                    <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-1" id="btn-add-item">
                        <i class="bi bi-plus-lg"></i> Tambah Item
                    </button>
                </div>
                <div class="card-body" id="items-container"></div>
                <div class="card-footer bg-white d-flex align-items-center py-2">
                    <button type="button" class="btn btn-outline-primary btn-sm" id="btn-add-item-2">
                        <i class="bi bi-plus-lg"></i> Tambah Item
                    </button>
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
                    <div class="d-flex justify-content-between align-items-center py-2" style="font-size:14px;color:#475569;border-bottom:1px solid #f1f5f9;">
                        <span>Total Item</span>
                        <span class="fw-bold" id="sum-items" style="font-family:monospace;color:#1B5DBC;">0</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2" style="font-size:14px;color:#475569;border-bottom:1px solid #f1f5f9;">
                        <span>Total Qty</span>
                        <span class="fw-bold" id="sum-qty" style="font-family:monospace;color:#1B5DBC;">0</span>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <span class="fw-semibold">Catatan</span>
                </div>
                <div class="card-body">
                    <textarea name="notes" class="form-control form-control-sm" rows="6"
                              placeholder="Catatan pengiriman...">{{ old('notes', $isEdit ? $deliveryOrder->notes : '') }}</textarea>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-check-lg"></i>
                    {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Delivery Order' }}
                </button>
                <a href="{{ route('admin.delivery-orders.index') }}" class="btn btn-outline-secondary text-center">Batal</a>
            </div>

        </div>{{-- end right --}}
    </div>
</form>

@endsection

@push('scripts')
<script>
const initItems = @json($oldItems);
let iIdx = 0, mIdx = {};

const esc = s => String(s ?? '').replace(/"/g,'"').replace(/</g,'<');

/* ══ Auto-load from Master Client via AJAX ═══════════════ */
document.getElementById('client-select')?.addEventListener('change', async function() {
    const opt = this.options[this.selectedIndex];
    const val = opt.value;
    if (!val) return;

    const url = this.dataset.urlTemplate.replace('__ID__', val);

    try {
        const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
        if (!res.ok) throw new Error('Failed to load client data');
        const data = await res.json();

        document.getElementById('client_company').value   = data.nama_perusahaan || '';
        document.getElementById('client_name').value      = data.nama_kontak || '';
        document.getElementById('client_email').value     = data.email || '';
        document.getElementById('destination_address').value = data.alamat_pengiriman || '';
    } catch (err) {
        console.error(err);
    }
});

/* ══ Auto-load from Sales Order via AJAX ═══════════════════ */
document.getElementById('sales_order_id')?.addEventListener('change', async function() {
    const opt = this.options[this.selectedIndex];
    if (!opt.value) return;

    const url = this.dataset.urlTemplate.replace('__ID__', opt.value);

    try {
        const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
        if (!res.ok) throw new Error('Failed to load SO data');
        const data = await res.json();

        // Fill info fields
        document.getElementById('so_number').value          = data.so_number || '';
        document.getElementById('nomor_po').value           = data.nomor_po || '';
        document.getElementById('client_name').value        = data.client_name || '';
        document.getElementById('client_company').value     = data.client_company || '';
        document.getElementById('client_attention').value   = data.client_attention || '';
        document.getElementById('client_cc').value          = data.client_cc || '';
        document.getElementById('client_email').value       = data.client_email || '';
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
                materials: it.materials ?? [],
            }));
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
    const qty = parseFloat(item.qty ?? 1) || 0;

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
                <input type="number" name="items[${pIdx}][qty]" class="item-input item-qty" min="0" step="any" value="${qty}" style="flex:0 0 80px;text-align:right;" required>
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

    div.querySelector('.item-qty')?.addEventListener('input', () => recalc());
    return div;
}

function addProductCard(item = {}) {
    const container = document.getElementById('items-container');
    const card = createProductCard(item);
    container.appendChild(card);
    renumberProducts();

    // Seed materials if any
    if (item.materials && item.materials.length) {
        const pIdx = card.dataset.pIdx;
        item.materials.forEach(mat => addMaterialRow(card.querySelector('.btn-add-mat'), {
            asset_id: mat.asset_id ?? '',
            material_name: mat.material_name ?? '',
            qty_required: mat.qty_required ?? 0,
            satuan: mat.satuan ?? 'pcs',
            unit_price: mat.unit_price ?? 0,
        }));
    }
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
        // Update name prefixes
        card.querySelectorAll('[name]').forEach(el => {
            const name = el.getAttribute('name');
            if (name) {
                el.setAttribute('name', name.replace(/items\[\d+\]/, 'items[' + i + ']'));
            }
        });
        // Update button data-p
        const btn = card.querySelector('.btn-add-mat');
        if (btn) btn.dataset.p = i;
    });
}

/* ══ MATERIAL ROWS ═══════════════════════════════════ */
function createMaterialRow(pIdx, mat = {}) {
    const mSeq = mIdx['p' + pIdx]++;

    const tr = document.createElement('tr');
    tr.className = 'material-row';
    tr.innerHTML = `
        <td style="text-align:center;font-family:monospace;color:#94a3b8;">${mSeq + 1}</td>
        <td>
            <input type="text" name="items[${pIdx}][materials][${mSeq}][material_name]" class="mat-input-sm" value="${esc(mat.material_name)}" placeholder="Nama material" required>
            <input type="hidden" name="items[${pIdx}][materials][${mSeq}][asset_id]" value="${esc(mat.asset_id ?? '')}">
        </td>
        <td><input type="text" name="items[${pIdx}][materials][${mSeq}][satuan]" class="mat-input-sm" value="${esc(mat.satuan ?? 'pcs')}" style="text-align:center;"></td>
        <td><input type="number" name="items[${pIdx}][materials][${mSeq}][qty_required]" class="mat-input-sm mat-qty" min="0" step="any" value="${parseFloat(mat.qty_required ?? 0) || 0}" style="text-align:right;" required></td>
        <td><input type="number" name="items[${pIdx}][materials][${mSeq}][unit_price]" class="mat-input-sm mat-price" min="0" step="any" value="${parseFloat(mat.unit_price ?? 0) || 0}" style="text-align:right;"></td>
        <td style="text-align:right;font-weight:600;color:#1B5DBC;font-family:monospace;">${((parseFloat(mat.qty_required) || 0) * (parseFloat(mat.unit_price) || 0)).toLocaleString('id-ID')}</td>
        <td><button type="button" class="btn-remove-mat" onclick="removeMaterialRow(this)"><i class="bi bi-x"></i></button></td>
    `;
    tr.querySelector('.mat-qty')?.addEventListener('input', function() { updateMatRow(this); recalc(); });
    tr.querySelector('.mat-price')?.addEventListener('input', function() { updateMatRow(this); recalc(); });
    return tr;
}

function addMaterialRow(btn, mat = {}) {
    const pIdx = btn.dataset.p;
    const card = btn.closest('.product-card');
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
    tds[tds.length - 2].textContent = (qty * price).toLocaleString('id-ID');
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

/* ══ Helpers ════════════════════════════════════════════ */
function recalc() {
    let totalQty = 0;
    let totalItems = 0;
    document.querySelectorAll('#items-container .product-card').forEach(card => {
        totalQty += parseFloat(card.querySelector('.item-qty')?.value) || 0;
        totalItems++;
    });

    document.getElementById('sum-items').textContent = totalItems;
    document.getElementById('sum-qty').textContent   = totalQty.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
}

/* ══ Boot ═══════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
    (initItems.length ? initItems : [{}]).forEach(i => addProductCard(i));

    document.getElementById('btn-add-item').addEventListener('click',   () => addProductCard());
    document.getElementById('btn-add-item-2').addEventListener('click', () => addProductCard());
});
</script>
@endpush
