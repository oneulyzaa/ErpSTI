@extends('layouts.app')

@php
    $isEdit      = isset($deliveryOrder);
    $action      = $isEdit ? route('admin.delivery-orders.update', $deliveryOrder) : route('admin.delivery-orders.store');
    $oldItems    = old('items', $isEdit ? $deliveryOrder->items->map(function($it) {
        return [
            'nama_item'       => $it->nama_item,
            'deskripsi_item'  => $it->deskripsi_item,
            'satuan'          => $it->satuan,
            'jumlah_item'     => $it->jumlah_item,
            'harga_item'      => $it->harga_item,
            'materials'       => $it->materials->map(function($m) {
                return [
                    'id_material'      => $m->id_material,
                    'nama_material'    => $m->nama_material,
                    'satuan_material'  => $m->satuan_material,
                    'jumlah_material'  => $m->jumlah_material,
                    'harga_material'   => $m->harga_material,
                ];
            })->toArray(),
        ];
    })->toArray() : []);
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
                            <input type="text" name="nomor_deliveryorder"
                                   class="form-control form-control-sm @error('nomor_deliveryorder') is-invalid @enderror"
                                   value="{{ old('nomor_deliveryorder', $isEdit ? $deliveryOrder->nomor_deliveryorder : $doNumber) }}" required>
                            @error('nomor_deliveryorder')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-sm-4">
                            <label class="form-label fw-semibold" style="font-size:13px">Referensi Sales Order</label>
                            <select name="nomor_salesorder" id="nomor_salesorder" class="form-select form-select-sm"
                                    data-url-template="{{ route('admin.delivery-orders.so-data', ['salesOrder' => '__ID__']) }}">
                                <option value="">-- Pilih SO (opsional) --</option>
                                @foreach($salesOrders as $so)
                                    <option value="{{ $so->nomor_salesorder }}"
                                        {{ old('nomor_salesorder', ($isEdit ? $deliveryOrder->nomor_salesorder : '')) == $so->nomor_salesorder ? 'selected' : '' }}>
                                        {{ $so->nomor_salesorder }} — {{ $so->client->nama_perusahaan ?? '-' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                         <div class="col-12 col-sm-4">
                             <label class="form-label fw-semibold" style="font-size:13px">Nomor PO</label>
                             <input type="text" name="nomor_po" id="nomor_po" class="form-control form-control-sm"
                                    value="{{ old('nomor_po', $isEdit ? $deliveryOrder->nomor_po : '') }}"
                                    placeholder="Auto-load dari SO">
                         </div>
                     </div>
                     <div class="row g-3 mb-3">
                         <div class="col-12 col-sm-6">
                             <label class="form-label fw-semibold" style="font-size:13px">Nama Project</label>
                             <input type="text" name="nama_project" id="nama_project" class="form-control form-control-sm"
                                    value="{{ old('nama_project', $isEdit ? $deliveryOrder->nama_project : '') }}"
                                    placeholder="Auto-load dari SO">
                         </div>
                         <div class="col-12 col-sm-6">
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
                            <input type="date" name="tanggal_pembuatan" class="form-control form-control-sm @error('tanggal_pembuatan') is-invalid @enderror"
                                   value="{{ old('tanggal_pembuatan', $isEdit ? $deliveryOrder->tanggal_pembuatan->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
                            @error('tanggal_pembuatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold" style="font-size:13px">Tanggal Pengiriman</label>
                            <input type="date" name="tanggal_pengiriman" class="form-control form-control-sm"
                                   value="{{ old('tanggal_pengiriman', $isEdit && $deliveryOrder->tanggal_pengiriman ? $deliveryOrder->tanggal_pengiriman->format('Y-m-d') : '') }}">
                        </div>
                    </div>

                    <div class="section-label">Pilih Perusahaan (dari Master Client)</div>
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <select name="id_client" id="client-select" class="form-select form-select-sm"
                                    data-url-template="{{ route('admin.delivery-orders.client-data', ['client' => '__ID__']) }}">
                                <option value="">-- Pilih Perusahaan (opsional) --</option>
                                @isset($clients)
                                    @foreach($clients as $c)
                                        <option value="{{ $c->id }}"
                                            {{ old('id_client', $isEdit ? ($deliveryOrder->id_client ?? '') : '') == $c->id ? 'selected' : '' }}>
                                            {{ $c->id_perusahaan ?? $c->id }} — {{ $c->nama_perusahaan }}
                                        </option>
                                    @endforeach
                                @endisset
                            </select>
                        </div>
                    </div>

                    <div class="section-label">Info Klien <span class="text-muted" style="font-size:10px;text-transform:none;font-weight:400;">(auto-fill dari master client)</span></div>
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold" style="font-size:13px">Perusahaan</label>
                            <input type="text" id="display_client_company" class="form-control form-control-sm" readonly placeholder="Auto-load dari client">
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold" style="font-size:13px">Nama Kontak</label>
                            <input type="text" id="display_client_name" class="form-control form-control-sm" readonly placeholder="Auto-load dari client">
                        </div>
                        <div class="col-12 col-sm-4">
                            <label class="form-label fw-semibold" style="font-size:13px">Attn</label>
                            <input type="text" id="display_client_attn" class="form-control form-control-sm" readonly>
                        </div>
                        <div class="col-12 col-sm-4">
                            <label class="form-label fw-semibold" style="font-size:13px">CC</label>
                            <input type="text" id="display_client_cc" class="form-control form-control-sm" readonly>
                        </div>
                        <div class="col-12 col-sm-4">
                            <label class="form-label fw-semibold" style="font-size:13px">Email</label>
                            <input type="email" id="display_client_email" class="form-control form-control-sm" readonly>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px">Alamat Tujuan</label>
                        <textarea id="display_client_address" class="form-control form-control-sm" rows="2" readonly
                                  placeholder="Alamat pengiriman dari master client..."></textarea>
                    </div>
                    <div>
                        <label class="form-label fw-semibold" style="font-size:13px">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" class="form-control form-control-sm" rows="2"
                                  placeholder="Keterangan pengiriman...">{{ old('keterangan', $isEdit ? $deliveryOrder->keterangan : '') }}</textarea>
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
const fmtRp = v => 'Rp ' + (parseFloat(v) || 0).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 2 });

/* ══ Auto-load from Master Client via AJAX ═══════════════ */
document.getElementById('client-select')?.addEventListener('change', async function() {
    const opt = this.options[this.selectedIndex];
    const val = opt.value;
    if (!val) {
        // Clear display fields
        document.getElementById('display_client_company').value = '';
        document.getElementById('display_client_name').value    = '';
        document.getElementById('display_client_email').value   = '';
        document.getElementById('display_client_address').value = '';
        document.getElementById('display_client_attn').value    = '';
        document.getElementById('display_client_cc').value      = '';
        return;
    }

    const url = this.dataset.urlTemplate.replace('__ID__', val);

    try {
        const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
        if (!res.ok) throw new Error('Failed to load client data');
        const data = await res.json();

        document.getElementById('display_client_company').value = data.nama_perusahaan || '';
        document.getElementById('display_client_name').value    = data.nama_kontak || '';
        document.getElementById('display_client_email').value   = data.email || '';
        document.getElementById('display_client_address').value = data.alamat || '';
        document.getElementById('display_client_attn').value    = data.attn || '';
        document.getElementById('display_client_cc').value      = data.cc || '';
    } catch (err) {
        console.error(err);
    }
});

/* ══ Auto-load from Sales Order via AJAX ═══════════════════ */
document.getElementById('nomor_salesorder')?.addEventListener('change', async function() {
    const opt = this.options[this.selectedIndex];
    if (!opt.value) return;

    // Use the SO's id for the AJAX URL (we need to find the id from nomor_salesorder)
    // The route expects a SalesOrder model binding, so we pass the nomor_salesorder
    const url = this.dataset.urlTemplate.replace('__ID__', opt.value);

    try {
        const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
        if (!res.ok) throw new Error('Failed to load SO data');
        const data = await res.json();

        // Fill info fields
        document.getElementById('nomor_po').value     = data.nomor_po || '';
        document.getElementById('nama_project').value  = data.nama_project || '';

        // Fill client display fields
        if (data.id_client) {
            // Load client data
            const clientUrl = document.getElementById('client-select').dataset.urlTemplate.replace('__ID__', data.id_client);
            try {
                const cRes = await fetch(clientUrl, { headers: { 'Accept': 'application/json' } });
                if (cRes.ok) {
                    const cData = await cRes.json();
                    document.getElementById('display_client_company').value = cData.nama_perusahaan || '';
                    document.getElementById('display_client_name').value    = cData.nama_kontak || '';
                    document.getElementById('display_client_email').value   = cData.email || '';
                    document.getElementById('display_client_address').value = cData.alamat || '';
                    document.getElementById('display_client_attn').value    = cData.attn || '';
                    document.getElementById('display_client_cc').value      = cData.cc || '';
                    // Also set the client select
                    document.getElementById('client-select').value = data.id_client;
                }
            } catch(e) { console.error(e); }
        }

        // Clear & load items with materials
        document.getElementById('items-container').innerHTML = '';
        iIdx = 0;
        mIdx = {};
        if (data.items && data.items.length) {
            data.items.forEach(it => addProductCard({
                nama_item:       it.nama_item ?? '',
                deskripsi_item:  it.deskripsi_item ?? '',
                satuan:          it.satuan ?? 'Unit',
                jumlah_item:     it.jumlah_item ?? 1,
                harga_item:      it.harga_item ?? 0,
                materials:       it.materials ?? [],
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
    const qty   = parseFloat(item.jumlah_item ?? 1) || 0;
    const price = parseFloat(item.harga_item ?? 0) || 0;

    const div = document.createElement('div');
    div.className = 'product-card';
    div.dataset.pIdx = pIdx;

    div.innerHTML = `
        <div class="product-card-header">
            <span class="card-num">${pIdx + 1}</span>
            <div style="flex:1; display:flex; gap:8px; flex-wrap:wrap;">
                <input type="text" name="items[${pIdx}][nama_item]" class="item-input" placeholder="Nama item *" value="${esc(item.nama_item)}" style="flex:2;min-width:140px;" required>
                <input type="text" name="items[${pIdx}][deskripsi_item]" class="item-input" placeholder="Deskripsi" value="${esc(item.deskripsi_item)}" style="flex:2;min-width:140px;">
                <input type="text" name="items[${pIdx}][satuan]" class="item-input" placeholder="Satuan" value="${esc(item.satuan ?? 'Unit')}" style="flex:0 0 70px;text-align:center;" required>
                <input type="number" name="items[${pIdx}][jumlah_item]" class="item-input item-qty" min="0" step="any" value="${qty}" style="flex:0 0 80px;text-align:right;" required>
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
            id_material:      mat.id_material ?? '',
            nama_material:    mat.nama_material ?? '',
            satuan_material:  mat.satuan_material ?? 'pcs',
            jumlah_material:  mat.jumlah_material ?? 0,
            harga_material:   mat.harga_material ?? 0,
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
            <input type="text" name="items[${pIdx}][materials][${mSeq}][nama_material]" class="mat-input-sm" value="${esc(mat.nama_material)}" placeholder="Nama material" required>
            <input type="hidden" name="items[${pIdx}][materials][${mSeq}][id_material]" value="${esc(mat.id_material ?? '')}">
        </td>
        <td><input type="text" name="items[${pIdx}][materials][${mSeq}][satuan_material]" class="mat-input-sm" value="${esc(mat.satuan_material ?? 'pcs')}" style="text-align:center;"></td>
        <td><input type="number" name="items[${pIdx}][materials][${mSeq}][jumlah_material]" class="mat-input-sm mat-qty" min="0" step="any" value="${parseFloat(mat.jumlah_material ?? 0) || 0}" style="text-align:right;" required></td>
        <td><button type="button" class="btn-remove-mat" onclick="removeMaterialRow(this)"><i class="bi bi-x"></i></button></td>
    `;
    tr.querySelector('.mat-qty')?.addEventListener('input', function() { recalc(); });
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
        const qty   = parseFloat(card.querySelector('.item-qty')?.value) || 0;
        totalQty += qty;
        totalItems++;
    });

    document.getElementById('sum-items').textContent      = totalItems;
    document.getElementById('sum-qty').textContent         = totalQty.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
}

/* ══ Boot ═══════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
    (initItems.length ? initItems : [{}]).forEach(i => addProductCard(i));

    document.getElementById('btn-add-item').addEventListener('click',   () => addProductCard());
    document.getElementById('btn-add-item-2').addEventListener('click', () => addProductCard());
});
</script>
@endpush