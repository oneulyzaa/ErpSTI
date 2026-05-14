@extends('layouts.app')

@php
    $isEdit = isset($production);
    $action = $isEdit ? route('admin.productions.update', $production) : route('admin.productions.store');
    $oldItems = old('items', $isEdit ? $production->items->toArray() : []);
@endphp

@section('title', $isEdit ? 'Edit Rencana Produksi' : 'Buat Rencana Produksi Baru')
@section('breadcrumb', $isEdit ? 'Edit Produksi' : 'Buat Produksi')

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
    .mat-section-header th { background: #2c4f8a !important; color: #fff !important; font-size: 10px; text-transform: uppercase; letter-spacing: .05em; }
    .product-card {
        border: 1px solid #e2e8f0; border-radius: 8px;
        margin-bottom: 12px; overflow: hidden;
    }
    .product-card-header {
        background: #f8fafc; padding: 10px 14px;
        border-bottom: 1px solid #e2e8f0;
        display: flex; align-items: center; justify-content: space-between;
    }
</style>
@endpush

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">{{ $isEdit ? 'Edit Rencana Produksi' : 'Buat Rencana Produksi Baru' }}</h4>
        <p class="text-muted mb-0" style="font-size:13px">Tentukan bahan baku untuk setiap produk dari Sales Order</p>
    </div>
    <a href="{{ route('admin.productions.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<form method="POST" action="{{ $action }}" id="prd-form">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="row g-3 align-items-start">
        <div class="col-12 col-xl-8 d-flex flex-column gap-3">

            {{-- Info --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <span class="fw-semibold">Informasi Produksi</span>
                </div>
                <div class="card-body">
                    <div class="section-label">Nomor & Referensi</div>
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-sm-4">
                            <label class="form-label fw-semibold" style="font-size:13px">No. Produksi <span class="text-danger">*</span></label>
                            <input type="text" name="production_number"
                                   class="form-control form-control-sm @error('production_number') is-invalid @enderror"
                                   value="{{ old('production_number', $isEdit ? $production->production_number : $productionNumber) }}" required>
                            @error('production_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-sm-4">
                            <label class="form-label fw-semibold" style="font-size:13px">Ref. Sales Order <span class="text-danger">*</span></label>
                            <select name="sales_order_id" id="sales_order_id" class="form-select form-select-sm" required
                                    data-url="{{ route('admin.productions.so-items', ['salesOrder' => '__ID__']) }}">
                                <option value="">-- Pilih Sales Order --</option>
                                @foreach($salesOrders as $so)
                                    <option value="{{ $so->id }}"
                                        {{ old('sales_order_id', ($isEdit ? $production->sales_order_id : ($selected?->id ?? ''))) == $so->id ? 'selected' : '' }}>
                                        {{ $so->so_number }} — {{ $so->project_name ?: $so->client_company }}
                                    </option>
                                @endforeach
                            </select>
                            @error('sales_order_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <input type="hidden" name="so_number" id="so_number"
                                   value="{{ old('so_number', $isEdit ? $production->so_number : '') }}">
                        </div>
                        <div class="col-12 col-sm-4">
                            <label class="form-label fw-semibold" style="font-size:13px">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select form-select-sm" required>
                                @foreach(['planned'=>'Planned','in_progress'=>'In Progress','completed'=>'Completed','cancelled'=>'Cancelled'] as $v=>$l)
                                    <option value="{{ $v }}" {{ old('status', $isEdit ? $production->status : 'planned') === $v ? 'selected' : '' }}>{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold" style="font-size:13px">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="date" class="form-control form-control-sm @error('date') is-invalid @enderror"
                                   value="{{ old('date', $isEdit ? $production->date->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
                            @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold" style="font-size:13px">Target Selesai</label>
                            <input type="date" name="target_date" class="form-control form-control-sm"
                                   value="{{ old('target_date', $isEdit && $production->target_date ? $production->target_date->format('Y-m-d') : '') }}">
                        </div>
                    </div>

                    <div class="section-label">Info Project</div>
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold" style="font-size:13px">Nama Project</label>
                            <input type="text" name="project_name" id="project_name" class="form-control form-control-sm"
                                   value="{{ old('project_name', $isEdit ? $production->project_name : ($selected->project_name ?? '')) }}" readonly>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold" style="font-size:13px">Perusahaan</label>
                            <input type="text" name="client_company" id="client_company" class="form-control form-control-sm"
                                   value="{{ old('client_company', $isEdit ? $production->client_company : ($selected->client_company ?? '')) }}" readonly>
                        </div>
                    </div>

                    <label class="form-label fw-semibold" style="font-size:13px">Catatan</label>
                    <textarea name="notes" class="form-control form-control-sm" rows="2"
                              placeholder="Catatan produksi...">{{ old('notes', $isEdit ? $production->notes : '') }}</textarea>
                </div>
            </div>

            {{-- ── PRODUK LIST ── --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <span class="fw-semibold">Daftar Produk & Bahan Baku</span>
                    <div>
                        <small class="text-muted me-2">Pilih SO terlebih dahulu</small>
                    </div>
                </div>
                <div class="card-body" id="products-container">
                    @if($isEdit)
                        @foreach($oldItems as $pIdx => $pItem)
                            @include('admin.productions._product_card', [
                                'pIdx' => $pIdx,
                                'pItem' => $pItem,
                                'assets' => $assets ?? [],
                                'isEdit' => true,
                            ])
                        @endforeach
                    @elseif($selected)
                        @foreach($selected->items as $pIdx => $soItem)
                            @include('admin.productions._product_card', [
                                'pIdx' => $pIdx,
                                'pItem' => [
                                    'product_name' => $soItem->material_name,
                                    'product_qty' => $soItem->qty,
                                    'unit' => $soItem->unit,
                                    'sales_order_item_id' => $soItem->id,
                                    'materials' => [],
                                ],
                                'assets' => $assets ?? [],
                                'isEdit' => false,
                            ])
                        @endforeach
                    @else
                    <div class="text-center py-4 text-muted" id="placeholder-products">
                        <i class="bi bi-inbox" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                        Pilih Sales Order untuk memuat daftar produk
                    </div>
                    @endif
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
                    <div class="d-flex justify-content-between mb-2" style="font-size:13px;">
                        <span>Jumlah Produk</span>
                        <span class="fw-semibold" id="summary-products">0</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2" style="font-size:13px;">
                        <span>Total Jenis Bahan Baku</span>
                        <span class="fw-semibold" id="summary-materials">0</span>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-check-lg"></i>
                    {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Rencana Produksi' }}
                </button>
                <a href="{{ route('admin.productions.index') }}" class="btn btn-outline-secondary text-center">Batal</a>
            </div>
        </div>{{-- end right --}}
    </div>
</form>
@endsection

@push('scripts')
<script>
const assets = @json($assets ?? []);
let pIdx = {{ count($oldItems) }};
let mIdx = {};

/* ── Load products from selected SO ── */
document.getElementById('sales_order_id')?.addEventListener('change', async function() {
    const opt = this.options[this.selectedIndex];
    if (!opt.value) return;

    const url = this.dataset.url.replace('__ID__', opt.value);

    try {
        const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
        if (!res.ok) throw new Error('Failed');
        const data = await res.json();

        document.getElementById('project_name').value = data.project_name || '';
        document.getElementById('client_company').value = data.client_company || '';
        document.getElementById('so_number').value = opt.text.split(' — ')[0] || '';

        // Clear existing products
        document.getElementById('products-container').innerHTML = '';

        if (data.items && data.items.length) {
            data.items.forEach((item, i) => {
                addProductCard({
                    product_name: item.material_name,
                    product_qty: item.qty,
                    unit: item.unit,
                    sales_order_item_id: item.id,
                    materials: [],
                });
            });
        }

        updateSummary();
    } catch (err) {
        console.error(err);
        alert('Gagal memuat data produk dari Sales Order.');
    }
});

/* ══ Product Card ═══════════════════════════════════════ */
function addProductCard(item = {}) {
    const container = document.getElementById('products-container');
    const idx = pIdx++;
    mIdx['p' + idx] = 0;

    const card = document.createElement('div');
    card.className = 'product-card';
    card.dataset.idx = idx;
    card.innerHTML = `
        <div class="product-card-header">
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <span class="fw-semibold" style="font-size:14px;min-width:60px;" id="pnum-${idx}">#${container.children.length + 1}</span>
                <input type="hidden" name="items[${idx}][sales_order_item_id]" value="${item.sales_order_item_id || ''}">
                <input type="text" name="items[${idx}][product_name]" class="form-control form-control-sm" required
                       style="width:200px;" value="${esc(item.product_name || '')}" placeholder="Nama Produk">
                <input type="number" name="items[${idx}][product_qty]" class="form-control form-control-sm" required
                       style="width:80px;" min="0" step="any" value="${item.product_qty || 1}" placeholder="Qty">
                <input type="text" name="items[${idx}][unit]" class="form-control form-control-sm" required
                       style="width:80px;" value="${item.unit || 'Unit'}" placeholder="Satuan">
                <select name="items[${idx}][status]" class="form-select form-select-sm" style="width:120px;">
                    <option value="pending">Pending</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                </select>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeProduct(this)"><i class="bi bi-trash"></i></button>
            </div>
        </div>
        <div class="p-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span style="font-size:12px;font-weight:600;color:#1B5DBC;">BAHAN BAKU</span>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addMaterialRow(this, ${idx})">
                    <i class="bi bi-plus-lg"></i> Tambah Bahan Baku
                </button>
            </div>
            <table class="table table-sm mb-0" style="font-size:12px;">
                <thead>
                    <tr class="mat-section-header">
                        <th style="width:36px;">#</th>
                        <th style="min-width:160px;">Nama Bahan Baku</th>
                        <th style="width:80px;text-align:center;">Satuan</th>
                        <th style="width:100px;text-align:right;">Qty Dibutuhkan</th>
                        <th style="width:36px;"></th>
                    </tr>
                </thead>
                <tbody id="mattbody-${idx}"></tbody>
            </table>
        </div>
    `;
    container.appendChild(card);
    updateProductNumbers();
    updateSummary();
    return card;
}

function removeProduct(btn) {
    btn.closest('.product-card').remove();
    updateProductNumbers();
    updateSummary();
}

function updateProductNumbers() {
    document.querySelectorAll('#products-container .product-card').forEach((card, i) => {
        const num = card.querySelector('[id^="pnum-"]');
        if (num) num.textContent = '#' + (i + 1);
    });
}

/* ══ Material Rows ═══════════════════════════════════════ */
function addMaterialRow(btn, pIdx) {
    const tbody = btn.closest('.product-card').querySelector('tbody[id^="mattbody-"]');
    if (!tbody) return;
    const idx = mIdx['p' + pIdx] || 0;
    mIdx['p' + pIdx] = idx + 1;

    const tr = document.createElement('tr');
    tr.dataset.idx = idx;
    tr.innerHTML = `
        <td class="item-no" id="mno-${pIdx}-${idx}"></td>
        <td>
            <select name="items[${pIdx}][materials][${idx}][asset_id]" class="form-select form-select-sm material-select"
                    onchange="onMatSelect(this, ${pIdx}, ${idx})"
                    style="font-size:12px;">
                <option value="">-- Pilih Bahan Baku --</option>
                ${assets.map(a => `<option value="${a.id}" data-nama="${esc(a.nama_aset)}" data-satuan="${esc(a.satuan)}">${esc(a.nama_aset)}</option>`).join('')}
            </select>
            <input type="hidden" name="items[${pIdx}][materials][${idx}][nama_bahan_baku]" class="mat-name-hidden">
        </td>
        <td><input type="text" name="items[${pIdx}][materials][${idx}][satuan]" class="form-control form-control-sm mat-satuan" style="font-size:12px;text-align:center;" value="pcs"></td>
        <td><input type="number" name="items[${pIdx}][materials][${idx}][qty_required]" class="form-control form-control-sm" style="font-size:12px;text-align:right;" min="0" step="any" value="1"></td>
        <td><button type="button" class="btn-remove-row" onclick="removeMatRow(this)"><i class="bi bi-x-lg"></i></button></td>
    `;
    tbody.appendChild(tr);
    reorderMatNums(tbody);
    updateSummary();
}

function onMatSelect(select, pIdx, idx) {
    const opt = select.options[select.selectedIndex];
    const hidden = select.closest('td').querySelector('.mat-name-hidden');
    const satuanInput = select.closest('tr').querySelector('.mat-satuan');
    if (!opt.value) {
        if (hidden) hidden.value = '';
        if (satuanInput) satuanInput.value = 'pcs';
        return;
    }
    if (hidden) hidden.value = opt.dataset.nama || '';
    if (satuanInput && opt.dataset.satuan) satuanInput.value = opt.dataset.satuan;
}

function removeMatRow(btn) {
    const tbody = btn.closest('tbody');
    btn.closest('tr').remove();
    reorderMatNums(tbody);
    updateSummary();
}

function reorderMatNums(tbody) {
    tbody.querySelectorAll('tr').forEach((tr, i) => {
        const el = tr.querySelector('[id^="mno-"]');
        if (el) el.textContent = i + 1;
    });
}

/* ══ Summary ════════════════════════════════════════════ */
function updateSummary() {
    const products = document.querySelectorAll('#products-container .product-card').length;
    let matTotal = 0;
    document.querySelectorAll('#products-container tbody[id^="mattbody-"]').forEach(tbody => {
        matTotal += tbody.querySelectorAll('tr').length;
    });
    document.getElementById('summary-products').textContent = products;
    document.getElementById('summary-materials').textContent = matTotal;
}

/* ══ Helpers ════════════════════════════════════════════ */
const esc = s => String(s ?? '').replace(/"/g,'"').replace(/</g,'<');

/* ══ Boot ═══════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
    updateSummary();
});
</script>
@endpush
