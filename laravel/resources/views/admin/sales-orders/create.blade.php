@extends('layouts.app')

@php
    $isEdit        = isset($salesOrder);
    $action        = $isEdit ? route('admin.sales-orders.update', $salesOrder) : route('admin.sales-orders.store');
    
    // Map DB field names to JS-expected field names for edit mode
    $mapItemForJs = function(array $item) {
        return [
            'material_name' => $item['nama_item'] ?? '',
            'description'   => $item['deskripsi_item'] ?? '',
            'unit'          => $item['satuan'] ?? 'Unit',
            'qty'           => (float)($item['jumlah_item'] ?? 0),
            'unit_price'    => (float)($item['harga_item'] ?? 0),
            'materials'     => collect($item['materials'] ?? [])->map(function($m) {
                return [
                    'material_name' => $m['nama_material'] ?? '',
                    'satuan'        => $m['satuan_material'] ?? 'pcs',
                    'qty_required'  => (float)($m['jumlah_material'] ?? 0),
                    'unit_price'    => (float)($m['harga_material'] ?? 0),
                    'asset_id'      => $m['id_material'] ?? null,
                ];
            })->toArray(),
        ];
    };
    $mapLaborForJs = function(array $labor) {
        return [
            'labor_name' => $labor['nama_labor'] ?? '',
            'mp'         => (int)($labor['jumlah_sdm'] ?? 1),
            'days'       => (float)($labor['jumlah_hari'] ?? 1),
            'rate'       => (float)($labor['rate_hari'] ?? 0),
        ];
    };
    $mapOtherCostForJs = function(array $cost) {
        return [
            'cost_name' => $cost['nama_biaya'] ?? '',
            'qty'       => 1,
            'rate'      => (float)($cost['jumlah_biaya'] ?? 0),
        ];
    };

    // Data untuk edit mode sudah di-pass dari controller sebagai $oldItems, $oldLabors, $oldOtherCosts
    // Prioritas: old() (jika ada validasi error) > data dari controller (edit mode) > copyQuote > kosong
    $copyQuote    = isset($quotation);
    
    // Siapkan data copyQuote jika ada
    if ($copyQuote) {
        $quotation->load('items.materials', 'labors', 'otherCosts');
        $copyItems = $quotation->items->map(function($item) {
            return [
                'material_name' => $item->nama_item ?? '',
                'description'   => $item->deskripsi_item ?? '',
                'unit'          => $item->satuan ?? 'Unit',
                'qty'           => (float)($item->jumlah_item ?? 0),
                'unit_price'    => (float)($item->harga_item ?? 0),
                'materials'     => $item->materials->map(function($mat) {
                    return [
                        'material_name' => $mat->nama_material ?? '',
                        'satuan'        => $mat->satuan_material ?? 'pcs',
                        'qty_required'  => (float)($mat->jumlah_material ?? 0),
                        'unit_price'    => (float)($mat->harga_material ?? 0),
                        'asset_id'      => $mat->id_material ?? null,
                    ];
                })->toArray(),
            ];
        })->toArray();
        $copyLabors = $quotation->labors->map(function($labor) {
            return [
                'labor_name' => $labor->nama_labor ?? '',
                'mp'         => (int)($labor->jumlah_sdm ?? 1),
                'days'       => (float)($labor->jumlah_hari ?? 1),
                'rate'       => (float)($labor->rate_hari ?? 0),
            ];
        })->toArray();
        $copyOtherCosts = $quotation->otherCosts->map(function($cost) {
            return [
                'cost_name' => $cost->nama_biaya ?? '',
                'qty'       => 1,
                'rate'      => (float)($cost->jumlah_biaya ?? 0),
            ];
        })->toArray();
    }
    
    // Gunakan old() jika ada (form pernah di-submit dengan error validasi)
    // Jika tidak ada old(), gunakan data dari controller untuk edit mode
    // Jika bukan edit mode, gunakan data dari copyQuote
    if (old('_token') !== null) {
        // Form pernah di-submit, gunakan old() data
        $seedItems = old('items', []);
        $seedLabors = old('labors', []);
        $seedOtherCosts = old('other_costs', []);
    } elseif ($isEdit && isset($oldItems)) {
        // Edit mode: gunakan data dari controller yang sudah di-map
        $seedItems = $oldItems;
        $seedLabors = $oldLabors;
        $seedOtherCosts = $oldOtherCosts;
    } elseif ($copyQuote) {
        // Copy from quotation: gunakan data dari quotation
        $seedItems = $copyItems;
        $seedLabors = $copyLabors;
        $seedOtherCosts = $copyOtherCosts;
    } else {
        // Create mode kosong
        $seedItems = [];
        $seedLabors = [];
        $seedOtherCosts = [];
    }
@endphp

@section('title', $isEdit ? 'Edit Sales Order' : 'Buat Sales Order Baru')
@section('breadcrumb', $isEdit ? 'Edit Sales Order' : 'Buat Sales Order')

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
    .info-readonly {
        background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 6px;
        padding: 10px 14px; font-size: 13px; min-height: 36px;
    }
</style>
@endpush

@section('content')

{{-- Alert untuk error dari session flash --}}
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>
    <strong>Gagal!</strong> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

{{-- Alert untuk error validasi Laravel --}}
@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>
    <strong>Gagal menambahkan Sales Order!</strong> Terdapat {{ $errors->count() }} error:
    <ul class="mb-0 mt-2">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

{{-- Alert untuk sukses dari session flash --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i>
    <strong>Berhasil!</strong> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">{{ $isEdit ? 'Edit Sales Order' : 'Buat Sales Order Baru' }}</h4>
        <p class="text-muted mb-0" style="font-size:13px">PT. Sistem Teknologi Integrator</p>
    </div>
    <a href="{{ route('admin.sales-orders.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<form method="POST" action="{{ $action }}" id="so-form">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="row g-3 align-items-start">
        {{-- ── LEFT COLUMN ── --}}
        <div class="col-12 col-xl-8 d-flex flex-column gap-3">

            {{-- Info Sales Order --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <span class="fw-semibold">Informasi Sales Order</span>
                </div>
                <div class="card-body">
                    <div class="section-label">Nomor & Referensi</div>
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-sm-4">
                            <label class="form-label fw-semibold" style="font-size:13px">No. SO <span class="text-danger">*</span></label>
                            <input type="text" name="nomor_salesorder"
                                   class="form-control form-control-sm @error('nomor_salesorder') is-invalid @enderror"
                                   value="{{ old('nomor_salesorder', $isEdit ? $salesOrder->nomor_salesorder  : $soNumber) }}" required>
                            @error('nomor_salesorder')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-sm-4">
                            <label class="form-label fw-semibold" style="font-size:13px">Nomor PO</label>
                            <input type="text" name="nomor_po"
                                   class="form-control form-control-sm @error('nomor_po') is-invalid @enderror"
                                   value="{{ old('nomor_po', $isEdit ? $salesOrder->nomor_po : '') }}">
                            @error('nomor_po')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-sm-4">
                            <label class="form-label fw-semibold" style="font-size:13px">Referensi Quotation</label>
                            @php
                                $selectedQuotation = old('nomor_quotation', $isEdit ? ($salesOrder->nomor_quotation ?? '') : ($copyQuote ? $quotation->nomor_quotation : ''));
                            @endphp
                            <select name="nomor_quotation" id="nomor_quotation" class="form-select form-select-sm"
                                    data-url-template="{{ route('admin.sales-orders.quotation-data', ['quotation' => '__ID__']) }}">
                                <option value="">-- Pilih Quotation (opsional) --</option>
                                @foreach($quotations as $q)
                                    <option value="{{ $q->nomor_quotation }}"
                                        {{ $selectedQuotation == $q->nomor_quotation ? 'selected' : '' }}>
                                        {{ $q->nomor_quotation }} — {{ $q->nama_project ?: ($q->client->nama_perusahaan ?? '') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold" style="font-size:13px">Tanggal SO <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_pembuatan" class="form-control form-control-sm @error('tanggal_pembuatan') is-invalid @enderror"
                                   value="{{ old('tanggal_pembuatan', $isEdit ? $salesOrder->tanggal_pembuatan->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
                            @error('tanggal_pembuatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold" style="font-size:13px">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select form-select-sm" required>
                                @foreach(['draft'=>'Draft','confirmed'=>'Confirmed','in_progress'=>'In Progress','completed'=>'Completed','cancelled'=>'Cancelled'] as $v=>$l)
                                    <option value="{{ $v }}" {{ old('status', $isEdit ? $salesOrder->status : 'draft') === $v ? 'selected' : '' }}>{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    

                    <div class="section-label">Info Perusahaan & Project</div>
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold" style="font-size:13px">Nama Project</label>
                            <input type="text" name="nama_project" id="nama_project" class="form-control form-control-sm"
                                   value="{{ old('nama_project', $isEdit ? $salesOrder->nama_project : ($copyQuote ? $quotation->nama_project : '')) }}"
                                   placeholder="Auto-load dari Quotation">
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold" style="font-size:13px">Client</label>
                            <select name="id_client" id="id_client" class="form-select form-select-sm"
                                    data-url-template="{{ route('admin.sales-orders.client-data', ['client' => '__ID__']) }}">
                                <option value="">-- Pilih Client --</option>
                                @foreach($clients as $c)
                                    <option value="{{ $c->id }}"
                                        {{ old('id_client', ($isEdit ? $salesOrder->id_client : '')) == $c->id ? 'selected' : '' }}>
                                        {{ $c->nama_perusahaan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold" style="font-size:13px">Nama Kontak</label>
                            <input type="text" name="nama_kontak_display" id="nama_kontak_display" class="form-control form-control-sm" readonly
                                   value="{{ old('nama_kontak_display', ($isEdit && $salesOrder->client) ? $salesOrder->client->nama_kontak : ($copyQuote ? ($quotation->client->nama_kontak ?? '') : '')) }}">
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold" style="font-size:13px">Email</label>
                            <input type="email" name="email_display" id="email_display" class="form-control form-control-sm" readonly
                                   value="{{ old('email_display', ($isEdit && $salesOrder->client) ? $salesOrder->client->email_perusahaan : ($copyQuote ? ($quotation->client->email_perusahaan ?? '') : '')) }}">
                        </div>
                    </div>
                    <div>
                        <label class="form-label fw-semibold" style="font-size:13px">Alamat</label>
                        <textarea name="alamat_display" id="alamat_display" class="form-control form-control-sm" rows="2" readonly
                                  placeholder="Alamat klien...">{{ old('alamat_display', ($isEdit && $salesOrder->client) ? $salesOrder->client->alamat_perusahaan : ($copyQuote ? ($quotation->client->alamat_perusahaan ?? '') : '')) }}</textarea>
                    </div>
                    <div>
                        <label class="form-label fw-semibold" style="font-size:13px">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" class="form-control form-control-sm" rows="2"
                                  placeholder="Keterangan...">{{ old('keterangan', $isEdit ? $salesOrder->keterangan : ($copyQuote ? $quotation->keterangan : '')) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- ── PRODUKSI ITEMS ── --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <span class="fw-semibold">Produksi</span>
                    <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-1" id="btn-add-item">
                        <i class="bi bi-plus-lg"></i> Tambah Produk
                    </button>
                </div>
                <div class="card-body" id="items-container"></div>
                <div class="card-footer bg-white d-flex align-items-center justify-content-between py-2">
                    <div class="fw-semibold" style="font-size:13px;">
                        Total Produksi + Material: <span class="text-primary ms-2" id="disp-mat" style="font-family:monospace;">Rp 0</span>
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
                    <div class="fw-semibold" style="font-size:13px;">
                        Total Labor: <span class="ms-2" id="disp-lab" style="font-family:monospace;color:#1B5DBC;">Rp 0</span>
                    </div>
                </div>
            </div>

            {{-- Biaya Lain-Lain --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <span class="fw-semibold">Biaya Lain-Lain</span>
                    <button type="button" class="btn btn-sm d-flex align-items-center gap-1"
                            style="background:#1B5DBC;color:#fff;" id="btn-add-other-cost">
                        <i class="bi bi-plus-lg"></i> Tambah Biaya
                    </button>
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
                <div class="card-footer bg-white d-flex align-items-center justify-content-between py-2">
                    <div class="fw-semibold" style="font-size:13px;">
                        Total Biaya Lain-Lain: <span class="ms-2" id="disp-oth" style="font-family:monospace;color:#1B5DBC;">Rp 0</span>
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
                    <div class="summary-row"><span>Total Produksi + Material</span><span class="summary-val" id="sum-mat">Rp 0</span></div>
                    <div class="summary-row"><span>Total Labor</span><span class="summary-val" id="sum-lab">Rp 0</span></div>
                    <div class="summary-row"><span>Total Biaya Lain-Lain</span><span class="summary-val" id="sum-oth">Rp 0</span></div>
                    <div class="summary-row"><span>Subtotal</span><span class="summary-val" id="sum-sub">Rp 0</span></div>
                    <div class="summary-row align-items-start gap-2" style="flex-wrap:wrap;">
                        <div>
                            <div style="font-size:13px;margin-bottom:4px;">Pajak (%)</div>
                            <input type="number" name="pajak" id="pajak"
                                   class="form-control form-control-sm" min="0" max="100" step="0.01"
                                   value="{{ old('pajak', $isEdit ? $salesOrder->pajak : 0) }}"
                                   style="width:80px;">
                        </div>
                        <span class="summary-val mt-4" id="sum-tax">Rp 0</span>
                    </div>
                    <div class="summary-row">
                        <span>Diskon</span>
                        <div class="input-group" style="max-width:160px;">
                            <div class="input-group-text" style="background:#f1f5f9;font-size:13px;">Rp</div>
                            <input type="text" id="discount-display"
                                   class="form-control form-control-sm" placeholder="0"
                                   style="text-align:right;"
                                   oninput="formatNumberInput(this, 'diskon')">
                            <input type="hidden" name="diskon" id="diskon"
                                   value="{{ old('diskon', $isEdit ? $salesOrder->diskon : 0) }}">
                        </div>
                    </div>
                    <div class="summary-row total-row">
                        <span>TOTAL</span>
                        <span class="summary-val" id="sum-total">Rp 0</span>
                    </div>
                    <input type="hidden" name="_subtotal_material"   id="h-mat">
                    <input type="hidden" name="_subtotal_labor"      id="h-lab">
                    <input type="hidden" name="_subtotal_other_cost" id="h-oth">
                    <input type="hidden" name="_subtotal"            id="h-sub">
                    <input type="hidden" name="_tax_amount"          id="h-tax">
                    <input type="hidden" name="_total"               id="h-total">
                </div>
            </div>


            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-check-lg"></i>
                    {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Sales Order' }}
                </button>
                <a href="{{ route('admin.sales-orders.index') }}" class="btn btn-outline-secondary text-center">Batal</a>
            </div>

        </div>{{-- end right --}}
    </div>
</form>

@endsection

@push('scripts')
<script>
/* ── seed data ── */
/* Variabel $seedItems, $seedLabors, $seedOtherCosts sudah di-set di bagian atas (PHP @php block) */
const initItems      = @json($seedItems);
const initLabors     = @json($seedLabors);
const initOtherCosts = @json($seedOtherCosts);

/* ══ Index counters ══════════════════════════════════════ */
let iIdx = 0; // product card index
let lIdx = 0; // labor row index
let oIdx = 0; // other cost row index

/* ══ Helper functions ════════════════════════════════════ */
function esc(str) {
    const div = document.createElement('div');
    div.textContent = String(str ?? '');
    return div.innerHTML;
}
function fmt(n) {
    return 'Rp ' + (parseFloat(n) || 0).toLocaleString('id-ID', {minimumFractionDigits:0, maximumFractionDigits:0});
}

/* ══ Auto-load from Master Client via AJAX ═══════════════ */
const clientSelectEl = document.querySelector('select[name="id_client"]');
if (clientSelectEl) {
    clientSelectEl.addEventListener('change', async function() {
        const val = this.value;
        if (!val) {
            // Clear display fields when no client selected
            document.getElementById('nama_kontak_display').value = '';
            document.getElementById('email_display').value = '';
            document.getElementById('alamat_display').value = '';
            return;
        }

        const url = this.dataset.urlTemplate?.replace('__ID__', val);
        if (!url) return;

        try {
            const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) throw new Error('Failed to load client data');
            const data = await res.json();

            document.getElementById('nama_kontak_display').value = data.nama_kontak || '';
            document.getElementById('email_display').value = data.email_perusahaan || '';
            document.getElementById('alamat_display').value = data.alamat_perusahaan || '';
        } catch (err) {
            console.error(err);
        }
    });
}

/* ══ Auto-load from Quotation via AJAX ═══════════════════ */
document.getElementById('nomor_quotation')?.addEventListener('change', async function() {
    const opt = this.options[this.selectedIndex];
    if (!opt.value) {
        // Clear all fields when no quotation selected
        document.getElementById('nama_project').value = '';
        document.getElementById('keterangan').value = '';
        document.getElementById('nama_kontak_display').value = '';
        document.getElementById('email_display').value = '';
        document.getElementById('alamat_display').value = '';
        document.querySelector('select[name="id_client"]').value = '';
        
        // Clear items, labors, other costs
        document.getElementById('items-container').innerHTML = '';
        document.getElementById('labors-tbody').innerHTML = '';
        document.getElementById('other-costs-tbody').innerHTML = '';
        iIdx = 0; lIdx = 0; oIdx = 0;
        
        // Reset discount
        document.getElementById('diskon').value = 0;
        document.getElementById('discount-display').value = '0';
        
        recalc();
        return;
    }

    const url = this.dataset.urlTemplate.replace('__ID__', opt.value);

    try {
        const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
        if (!res.ok) throw new Error('Failed to load quotation data');
        const data = await res.json();

        console.log('Quotation data loaded:', data);

        // Fill info fields
        document.getElementById('nama_project').value        = data.nama_project || '';
        document.getElementById('keterangan').value          = data.keterangan || '';
        
        // Set client dropdown and auto-fill client fields
        const clientId = data.id_client || data.id_customer;
        if (clientId) {
            const clientSelect = document.querySelector('select[name="id_client"]');
            if (clientSelect) {
                clientSelect.value = clientId;
                
                // Directly fill client display fields from quotation data
                document.getElementById('nama_kontak_display').value = data.nama_kontak || '';
                document.getElementById('email_display').value = data.email_perusahaan || '';
                document.getElementById('alamat_display').value = data.alamat_perusahaan || '';
            }
        }

        // Fill discount
        const discountEl        = document.getElementById('diskon');
        const discountDisplayEl = document.getElementById('discount-display');
        if (discountEl && discountDisplayEl) {
            discountEl.value = data.diskon ?? 0;
            discountDisplayEl.value = parseFloat(data.diskon || 0).toLocaleString('id-ID');
        }

        // Clear & load items with materials
        document.getElementById('items-container').innerHTML = '';
        iIdx = 0;
        mIdx = {}; // Reset material index
        if (data.items && data.items.length) {
            data.items.forEach(it => {
                // Map quotation item structure to sales order item structure
                const itemData = {
                    material_name: it.material_name || it.product_name || '',
                    description: it.description || '',
                    unit: it.unit || it.satuan || 'Unit',
                    qty: it.qty || 1,
                    unit_price: it.unit_price || 0,
                    materials: it.materials || []
                };
                addProductCard(itemData);
            });
        }

        // Clear & load labors
        document.getElementById('labors-tbody').innerHTML = '';
        lIdx = 0;
        if (data.labors && data.labors.length) {
            data.labors.forEach(lb => addLaborRow({
                labor_name: lb.labor_name ?? '',
                mp: lb.mp ?? 1,
                days: lb.days ?? 1,
                rate: lb.rate ?? 0,
            }));
        }

        // Clear & load other costs
        document.getElementById('other-costs-tbody').innerHTML = '';
        oIdx = 0;
        if (data.other_costs && data.other_costs.length) {
            data.other_costs.forEach(oc => addOtherCostRow({
                cost_name: oc.cost_name ?? '',
                qty: oc.qty ?? 1,
                rate: oc.rate ?? 0,
            }));
        }

        // Recalculate all totals
        recalc();
    } catch (err) {
        console.error('Error loading quotation:', err);
        alert('Gagal memuat data quotation. Silakan coba lagi.');
    }
});

/* ══ PRODUCT CARDS with MATERIALS ══════════════════════ */
let mIdx = {}; // keyed by 'p' + product index
function createProductCard(item = {}) {
    const idx = iIdx++;
    mIdx['p' + idx] = 0;
    const qty   = parseFloat(item.qty ?? 1) || 0;
    const price = parseFloat(item.unit_price ?? 0) || 0;
    const materials = item.materials || [];

    const card = document.createElement('div');
    card.className = 'product-card';
    card.dataset.idx = idx;
    card.style.cssText = 'border:1px solid #e2e8f0;border-radius:8px;margin-bottom:10px;background:#fff;';
    card.innerHTML = `
        <div style="padding:10px 14px;background:#f8fafc;border-bottom:1px solid #e2e8f0;border-radius:7px 7px 0 0;">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="fw-semibold" style="font-size:14px;min-width:30px;" id="pnum-${idx}">#1</span>
                <input type="text" name="items[${idx}][material_name]" class="item-input" required style="width:160px;" value="${esc(item.material_name || '')}" placeholder="Nama Produk">
                <input type="text" name="items[${idx}][description]" class="item-input" style="width:140px;" value="${esc(item.description || '')}" placeholder="Deskripsi">
                <input type="text" name="items[${idx}][unit]" class="item-input" required style="width:75px;text-align:center;" value="${esc(item.unit || 'Unit')}" placeholder="Sat">
                <input type="number" name="items[${idx}][qty]" class="item-input item-qty" required style="width:70px;text-align:right;" min="0" step="any" value="${qty}" onchange="updateProductCardSub(this.closest('.product-card'))">
                <input type="number" name="items[${idx}][unit_price]" class="item-input item-price" required style="width:110px;text-align:right;" min="0" step="any" value="${price}" onchange="updateProductCardSub(this.closest('.product-card'))">
                <span class="subtotal-cell" id="isub-${idx}">${fmt(qty * price)}</span>
                <button type="button" class="btn-remove-row" onclick="removeProduct(this)"><i class="bi bi-x-lg"></i></button>
            </div>
        </div>
        <div class="p-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span style="font-size:12px;font-weight:600;color:#1B5DBC;">MATERIAL</span>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addMaterialRow(this)">
                    <i class="bi bi-plus-lg"></i> Tambah Material
                </button>
            </div>
            <table class="table table-sm mb-0" style="font-size:12px;">
                <thead>
                    <tr style="background:#e8f0fe;font-size:10px;text-transform:uppercase;letter-spacing:.04em;">
                        <th style="width:28px;">#</th>
                        <th style="min-width:140px;">Nama Material</th>
                        <th style="width:70px;text-align:center;">Satuan</th>
                        <th style="width:80px;text-align:right;">Qty</th>
                        <th style="width:110px;text-align:right;">Harga</th>
                        <th style="width:100px;text-align:right;">Subtotal</th>
                        <th style="width:28px;"></th>
                    </tr>
                </thead>
                <tbody class="mat-tbody" id="mat-tbody-${idx}"></tbody>
            </table>
        </div>
    `;

    // Seed materials
    if (materials.length) {
        materials.forEach(mat => addMaterialRow(card.querySelector('.btn-outline-primary'), mat));
    }

    return card;
}
function updateProductCardSub(card) {
    const idx  = card.dataset.idx;
    const qty  = parseFloat(card.querySelector('.item-qty')?.value) || 0;
    const price = parseFloat(card.querySelector('.item-price')?.value) || 0;
    const el = card.querySelector(`#isub-${idx}`);
    if (el) el.textContent = fmt(qty * price);
    recalc();
}
function addProductCard(item = {}) {
    const container = document.getElementById('items-container');
    const card = createProductCard(item);
    container.appendChild(card);
    renumberProducts();
    recalc();
    //card.querySelector('.item-input')?.focus();
}
function removeProduct(btn) {
    btn.closest('.product-card').remove();
    renumberProducts();
    recalc();
}
function renumberProducts() {
    document.querySelectorAll('#items-container .product-card').forEach((card, i) => {
        const el = card.querySelector('[id^="pnum-"]');
        if (el) el.textContent = '#' + (i + 1);
    });
}
/* ══ Material rows inside product cards ═══════════════ */
function addMaterialRow(btnTrigger, mat = {}) {
    const card  = btnTrigger.closest('.product-card');
    const pIdx  = card.dataset.idx;
    const mKey  = 'p' + pIdx;
    const mSeq  = mIdx[mKey] = (mIdx[mKey] || 0) + 1;
    const qty   = parseFloat(mat.qty_required ?? 0) || 0;
    const price = parseFloat(mat.unit_price ?? 0) || 0;

    const tbody = card.querySelector('.mat-tbody');
    const tr = document.createElement('tr');
    tr.dataset.mi = mSeq;
    tr.innerHTML = `
        <td style="text-align:center;color:#94a3b8;">${mSeq}</td>
        <td><input type="text" name="items[${pIdx}][materials][${mSeq}][material_name]" class="item-input" required value="${esc(mat.material_name || '')}" placeholder="Nama material"></td>
        <td><input type="text" name="items[${pIdx}][materials][${mSeq}][satuan]" class="item-input" value="${esc(mat.satuan || 'pcs')}" style="text-align:center;"></td>
        <td><input type="number" name="items[${pIdx}][materials][${mSeq}][qty_required]" class="item-input mat-qty" min="0" step="any" value="${qty}" style="text-align:right;" onchange="updateMatRow(this)"></td>
        <td><input type="number" name="items[${pIdx}][materials][${mSeq}][unit_price]" class="item-input mat-price" min="0" step="any" value="${price}" style="text-align:right;" onchange="updateMatRow(this)"></td>
        <td style="text-align:right;font-weight:600;color:#1B5DBC;">${fmt(qty * price)}</td>
        <td><button type="button" class="btn-remove-row" onclick="removeMaterialRow(this)"><i class="bi bi-x-lg"></i></button></td>
    `;
    if (mat.asset_id) {
        tr.querySelector(`input[name="items[${pIdx}][materials][${mSeq}][material_name]"]`).insertAdjacentHTML('afterend',
            `<input type="hidden" name="items[${pIdx}][materials][${mSeq}][asset_id]" value="${mat.asset_id}">`);
    }
    tbody.appendChild(tr);
    renumberMaterials(card);
}
function updateMatRow(el) {
    const tr = el.closest('tr');
    const qty = parseFloat(tr.querySelector('.mat-qty')?.value) || 0;
    const price = parseFloat(tr.querySelector('.mat-price')?.value) || 0;
    const lastTd = tr.querySelectorAll('td');
    lastTd[lastTd.length - 2].textContent = fmt(qty * price);
    recalc();
}
function removeMaterialRow(btn) {
    const card = btn.closest('.product-card');
    btn.closest('tr').remove();
    renumberMaterials(card);
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
    tr.querySelector('.oc-qty').addEventListener('input',  () => updateOtherCostRow(tr));
    tr.querySelector('.oc-rate').addEventListener('input', () => updateOtherCostRow(tr));
    return tr;
}
function updateOtherCostRow(tr) {
    const idx  = tr.dataset.idx;
    const qty  = parseFloat(tr.querySelector('.oc-qty').value)  || 0;
    const rate = parseFloat(tr.querySelector('.oc-rate').value) || 0;
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

function recalc() {
    let prod = 0, mat = 0, lab = 0, oth = 0;

    // Product subtotal (qty * unit_price) - this is the PRODUCTION cost
    document.querySelectorAll('#items-container .product-card').forEach(card => {
        prod += (parseFloat(card.querySelector('.item-qty')?.value)   || 0)
             * (parseFloat(card.querySelector('.item-price')?.value) || 0);
    });

    // Material subtotals - materials are ADDITIONAL costs inside products
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
    const sub   = prod + mat + lab + oth;
    const discount = parseFloat(document.getElementById('diskon')?.value) || 0;
    
    // Perhitungan: Diskon dikurangi SEBELUM pajak
    const taxableBase = Math.max(sub - discount, 0);  // Dasar pengenaan pajak (tidak boleh negatif)
    const tax   = taxableBase * ((parseFloat(document.getElementById('pajak').value) || 0) / 100);
    const total = taxableBase + tax;

    const prodAndMat = prod + mat;
    document.getElementById('disp-mat').textContent = fmt(prodAndMat);
    document.getElementById('disp-lab').textContent = fmt(lab);
    document.getElementById('disp-oth').textContent = fmt(oth);
    document.getElementById('sum-mat').textContent  = fmt(prodAndMat);
    document.getElementById('sum-lab').textContent  = fmt(lab);
    document.getElementById('sum-oth').textContent  = fmt(oth);
    document.getElementById('sum-sub').textContent  = fmt(sub);
    document.getElementById('sum-tax').textContent  = fmt(tax);
    document.getElementById('sum-total').textContent= fmt(total);

    document.getElementById('h-mat').value   = prodAndMat.toFixed(2);
    document.getElementById('h-lab').value   = lab.toFixed(2);
    document.getElementById('h-oth').value   = oth.toFixed(2);
    document.getElementById('h-sub').value   = sub.toFixed(2);
    document.getElementById('h-tax').value   = tax.toFixed(2);
    document.getElementById('h-total').value = total.toFixed(2);
}

/* ══ Edit mode flag ═══════════════════════════════════ */
const isEditMode = @json($isEdit ?? false);

/* ══ Boot ═══════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
    // Items: always seed from data; if empty in create mode, add one blank card
    if (initItems.length) {
        initItems.forEach(i => addProductCard(i));
    } else if (!isEditMode) {
        addProductCard();
    }

    // Labors: seed from data; if empty in create mode, add one blank row
    if (initLabors.length) {
        initLabors.forEach(l => addLaborRow(l));
    } else if (!isEditMode) {
        addLaborRow();
    }

    // Other costs: seed from data only
    if (initOtherCosts.length) {
        initOtherCosts.forEach(c => addOtherCostRow(c));
    }

    document.getElementById('btn-add-item').addEventListener('click',   () => addProductCard());
    document.getElementById('btn-add-labor').addEventListener('click',  () => addLaborRow());
    document.getElementById('btn-add-other-cost').addEventListener('click',  () => addOtherCostRow());
    document.getElementById('pajak').addEventListener('input', recalc);
    
    // Bind discount display input to recalc
    const discountDisplayInput = document.getElementById('discount-display');
    if (discountDisplayInput) {
        discountDisplayInput.addEventListener('input', function() {
            formatNumberInput(this, 'diskon');
        });
    }

    // Format discount initial value
    const discountEl = document.getElementById('diskon');
    const discountDisplayEl = document.getElementById('discount-display');
    if (discountEl && discountDisplayEl) {
        discountDisplayEl.value = parseFloat(discountEl.value || 0).toLocaleString('id-ID');
    }
    
    // Add form submit confirmation
    const form = document.getElementById('so-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const soNumber = document.querySelector('input[name="nomor_salesorder"]')?.value || '';
        });
    }

    // ══ Hitung ulang total saat halaman dibuka ══
    // Panggil recalc() setelah semua data seed selesai di-generate
    recalc();
});

// Number formatting helper
function formatNumberInput(el, hiddenId) {
    let val = el.value.replace(/[^0-9]/g, '');
    if (val === '') val = '0';
    const hiddenEl = document.getElementById(hiddenId);
    if (hiddenEl) {
        hiddenEl.value = parseFloat(val);
    }
    el.value = parseFloat(val).toLocaleString('id-ID');
    recalc();
}
</script>
@endpush
