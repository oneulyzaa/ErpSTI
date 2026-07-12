@php
    $isEdit = $isEdit ?? false;
    $action = $isEdit ? route('admin.quotations.update', $quotation) : route('admin.quotations.store');
    $oldItems = old('items', $isEdit && isset($quotation) ? $quotation->items->map(function($item) {
        return array_merge($item->toArray(), [
            'materials' => $item->materials->toArray()
        ]);
    })->toArray() : []);
    $oldLabors = old('labors', $isEdit && isset($quotation) ? $quotation->labors->toArray() : []);
    $oldOtherCosts = old('other_costs', $isEdit && isset($quotation) ? $quotation->otherCosts->toArray() : []);
@endphp

@extends('layouts.app')
@section('title', $isEdit ? 'Edit Quotation' : 'Buat Quotation Baru')
@section('breadcrumb', $isEdit ? 'Edit Quotation' : 'Buat Quotation')

@push('styles')
<!-- Select2 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
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
    .btn-remove-item, .btn-remove-labor, .btn-remove-cost, .btn-remove-mat {
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
    .btn-remove-item:hover, .btn-remove-labor:hover, .btn-remove-cost:hover, .btn-remove-mat:hover {
        color: #ef4444; background: #fee2e2;
    }
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

    /* Client info box */
    .client-info-box {
        background: #f8faff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px 16px;
        font-size: 13px;
        line-height: 1.7;
        color: #475569;
        margin-top: 8px;
        display: none;
    }
    .client-info-box.show { display: block; }
    .client-info-box strong { color: #1e293b; }

    /* Material sub-table inside item */
    .mat-section {
        background: #f8faff;
        border: 1px solid #eef1f6;
        border-radius: 8px;
        padding: 10px;
        margin-top: 8px;
    }
    .mat-section table { font-size: 12px; margin-bottom: 0; }
    .mat-section th {
        background: #e8edf5;
        color: #475569;
        font-size: 10px;
        text-transform: uppercase;
        font-weight: 700;
        padding: 6px 8px;
        border: none;
    }
    .mat-section td {
        padding: 5px 8px;
        border: none;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .mat-input {
        border: 1px solid #e2e8f0;
        border-radius: 4px;
        padding: 4px 8px;
        font-size: 12px;
        width: 100%;
        background: #fff;
    }
    .mat-input:focus {
        border-color: #1B5DBC;
        outline: none;
        box-shadow: 0 0 0 2px rgba(27,93,188,.1);
    }

    /* Labor & Other cost table */
    .labor-table th, .cost-table th {
        background: #f8faff;
        color: #64748b;
        font-size: 10.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .04em;
        padding: 8px 10px;
        border: none;
        border-bottom: 1.5px solid #e2e8f0;
    }
    .labor-table td, .cost-table td {
        padding: 7px 10px;
        border: none;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    /* Select2 overrides */
    .select2-container--bootstrap-5 .select2-selection {
        border: 1.5px solid #e2e8f0;
        border-radius: 6px;
        min-height: 31px;
        font-size: 13px;
    }
    .select2-container--bootstrap-5.select2-container--focus .select2-selection {
        border-color: #1B5DBC;
        box-shadow: 0 0 0 3px rgba(27,93,188,.12);
    }

    /* Material Select2 inside table */
    .mat-section .select2-container { width: 100% !important; }
    .mat-section .select2-container--bootstrap-5 .select2-selection {
        min-height: 28px;
        font-size: 12px;
        padding: 2px 6px;
    }
    .mat-section .select2-dropdown {
        z-index: 1060;
    }
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

    {{-- Validation Errors Alert --}}
    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <h6 class="alert-heading fw-bold mb-2"><i class="bi bi-exclamation-triangle-fill me-1"></i> Validasi Gagal</h6>
        <ul class="mb-0" style="font-size:13px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row g-3 align-items-start">

        {{-- LEFT COLUMN --}}
        <div class="col-12 col-xl-8 d-flex flex-column gap-3">

            {{-- Informasi Quotation --}}
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
                            <input type="text" name="nomor_quotation"
                                   class="form-control form-control-sm @error('nomor_quotation') is-invalid @enderror"
                                   value="{{ old('nomor_quotation', $isEdit ? $quotation->nomor_quotation : $quoteNumber) }}"
                                   placeholder="QUO-202601-0001" required>
                            @error('nomor_quotation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-sm-4">
                            <label class="form-label fw-semibold" style="font-size:13px">Client</label>
                            <input type="hidden" name="id_client" id="hidden-id-client" value="{{ old('id_client', $isEdit ? $quotation->id_client : '') }}">
                            <div class="d-flex gap-1">
                                <select id="select-client" class="form-select form-select-sm" style="flex:1;">
                                    <option value="">-- Pilih Client --</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}"
                                            data-nama="{{ $client->nama_perusahaan }}"
                                            data-kontak="{{ $client->nama_kontak }}"
                                            data-email="{{ $client->email_perusahaan }}"
                                            data-alamat="{{ $client->alamat_perusahaan }}"
                                            data-telp="{{ $client->telepon_faktur }}"
                                            data-npwp="{{ $client->npwp_perusahaan }}"
                                            {{ old('id_client', $isEdit ? $quotation->id_client : '') == $client->id ? 'selected' : '' }}>
                                            {{ $client->nama_perusahaan }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-outline-success btn-sm" onclick="openQuickAddClient()" title="Tambah Client Baru" style="white-space:nowrap;padding:4px 10px;">
                                    <i class="bi bi-plus-lg"></i>
                                </button>
                            </div>
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
                                Tanggal Pembuatan <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="tanggal_pembuatan"
                                   class="form-control form-control-sm @error('tanggal_pembuatan') is-invalid @enderror"
                                   value="{{ old('tanggal_pembuatan', $isEdit ? $quotation->tanggal_pembuatan->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
                            @error('tanggal_pembuatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold" style="font-size:13px">
                                Berlaku Sampai <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="valid_sampai"
                                   class="form-control form-control-sm @error('valid_sampai') is-invalid @enderror"
                                   value="{{ old('valid_sampai', $isEdit ? $quotation->valid_sampai->format('Y-m-d') : now()->addDays(30)->format('Y-m-d')) }}" required>
                            @error('valid_sampai')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px">Nama Project</label>
                        <input type="text" name="nama_project"
                               class="form-control form-control-sm"
                               value="{{ old('nama_project', $isEdit ? $quotation->nama_project : '') }}"
                               placeholder="Nama project">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px">Keterangan</label>
                        <textarea name="keterangan" class="form-control form-control-sm" rows="3"
                                  placeholder="Keterangan atau catatan...">{{ old('keterangan', $isEdit ? $quotation->keterangan : '') }}</textarea>
                    </div>
                    <div class="mb-3 " >
                        {{-- Client preview --}}
                        <div id="client-preview" class="client-field-group card border-0 shadow-sm p-3 {{ old('id_client', $isEdit ? $quotation->id_client : '') ? '' : 'd-none' }}" style="background:#f8faff;">
                            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;margin-bottom:8px;">Data Perusahaan Tujuan</div>
                            <div class="row g-2">
                                <div class="col-12 col-sm-6">
                                    <div style="font-size:12px;color:#64748b;">Nama Perusahaan</div>
                                    <div style="font-size:14px;font-weight:600;" id="preview-company">-</div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div style="font-size:12px;color:#64748b;">Kontak</div>
                                    <div style="font-size:14px;" id="preview-contact">-</div>
                                </div>
                                <div class="col-12">
                                    <div style="font-size:12px;color:#64748b;">Email</div>
                                    <div style="font-size:14px;" id="preview-email">-</div>
                                </div>
                                <div class="col-12">
                                    <div style="font-size:12px;color:#64748b;">Alamat</div>
                                    <div style="font-size:14px;white-space:pre-line;" id="preview-address">-</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Item / Material --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <span class="fw-semibold">Biaya Produksi / Item</span>
                    <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-1" id="btn-add-item">
                        <i class="bi bi-plus-lg"></i> Tambah Item
                    </button>
                </div>
                <div class="card-body p-0">
                    <div id="items-container"></div>
                </div>
                <div class="card-footer bg-white border-top py-2 d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1" id="btn-add-item-2">
                        <i class="bi bi-plus-lg"></i> Tambah Item
                    </button>
                    <span class="fw-semibold" style="font-size:13px;">Subtotal Material: <span id="disp-sub-material" style="font-family:monospace;">Rp 0</span></span>
                </div>
            </div>

            {{-- Labor --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <span class="fw-semibold">Biaya Labor</span>
                    <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-1" id="btn-add-labor">
                        <i class="bi bi-plus-lg"></i> Tambah Labor
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table labor-table mb-0" style="font-size:13px;">
                        <thead>
                            <tr>
                                <th style="width:36px;">#</th>
                                <th style="min-width:180px;">Nama Pekerjaan</th>
                                <th style="width:80px;text-align:center;">Jumlah SDM</th>
                                <th style="width:90px;text-align:center;">Jumlah Hari</th>
                                <th style="width:140px;text-align:right;">Rate / Hari</th>
                                <th style="width:140px;text-align:right;">Subtotal</th>
                                <th style="width:40px;"></th>
                            </tr>
                        </thead>
                        <tbody id="labors-tbody"></tbody>
                    </table>
                </div>
                <div class="card-footer bg-white border-top py-2 d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1" id="btn-add-labor-2">
                        <i class="bi bi-plus-lg"></i> Tambah Labor
                    </button>
                    <span class="fw-semibold" style="font-size:13px;">Subtotal Labor: <span id="disp-sub-labor" style="font-family:monospace;">Rp 0</span></span>
                </div>
            </div>

            {{-- Biaya Lain-Lain --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <span class="fw-semibold">Biaya Lain-Lain</span>
                    <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-1" id="btn-add-cost">
                        <i class="bi bi-plus-lg"></i> Tambah Biaya
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table cost-table mb-0" style="font-size:13px;">
                        <thead>
                            <tr>
                                <th style="width:36px;">#</th>
                                <th style="min-width:200px;">Nama Biaya</th>
                                <th style="width:80px;text-align:center;">Qty</th>
                                <th style="width:140px;text-align:right;">Rate (Rp)</th>
                                <th style="width:140px;text-align:right;">Subtotal</th>
                                <th style="width:40px;"></th>
                            </tr>
                        </thead>
                        <tbody id="costs-tbody"></tbody>
                    </table>
                </div>
                <div class="card-footer bg-white border-top py-2 d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1" id="btn-add-cost-2">
                        <i class="bi bi-plus-lg"></i> Tambah Biaya
                    </button>
                    <span class="fw-semibold" style="font-size:13px;">Subtotal Lain-Lain: <span id="disp-sub-lainlain" style="font-family:monospace;">Rp 0</span></span>
                </div>
            </div>

        </div>

        {{-- RIGHT COLUMN --}}
        <div class="col-12 col-xl-4 d-flex flex-column gap-3">

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <span class="fw-semibold">Ringkasan</span>
                </div>
                <div class="card-body">
                    <div class="summary-row">
                        <span>Total Material</span>
                        <span class="summary-val" id="disp-subtotal-mat">Rp 0</span>
                    </div>
                    <div class="summary-row">
                        <span>Total Labor</span>
                        <span class="summary-val" id="disp-subtotal-lab">Rp 0</span>
                    </div>
                    <div class="summary-row">
                        <span>Total Biaya Lain-Lain</span>
                        <span class="summary-val" id="disp-subtotal-oth">Rp 0</span>
                    </div>
                    <div class="summary-row">
                        <span>Diskon (Rp)</span>
                        <input type="number" name="diskon" id="input-diskon"
                               class="form-control form-control-sm"
                               value="{{ old('diskon', $isEdit ? $quotation->diskon : 0) }}"
                               min="0" step="any"
                               style="width:140px;text-align:right;font-family:monospace;font-size:13px;">
                    </div>
                    <div class="summary-row total-row">
                        <span>Grand Total</span>
                        <span class="summary-val" id="disp-total">Rp 0</span>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <span class="fw-semibold">Terms & Conditions</span>
                </div>
                <div class="card-body">
                    <textarea name="termin" class="form-control form-control-sm" rows="5"
                              placeholder="Syarat pembayaran, garansi, catatan tambahan...">{{ old('termin', $isEdit ? $quotation->termin : '') }}</textarea>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-check-lg"></i>
                    {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Quotation' }}
                </button>
                <a href="{{ route('admin.quotations.index') }}" class="btn btn-outline-secondary text-center">
                    Batal
                </a>
            </div>

        </div>

    </div>
</form>

{{-- Quick Add Client Modal --}}
<div class="modal fade" id="quickAddClientModal" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Client Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="quick-client-form">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size:13px">ID Customer <span class="text-danger">*</span></label>
                            <input type="text" name="id_customer" class="item-input" required placeholder="ID unik customer">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size:13px">Nama Perusahaan <span class="text-danger">*</span></label>
                            <input type="text" name="nama_perusahaan" class="item-input" required placeholder="Nama perusahaan">
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold" style="font-size:13px">Email Perusahaan</label>
                            <input type="email" name="email_perusahaan" class="item-input" placeholder="email@perusahaan.com">
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold" style="font-size:13px">NPWP</label>
                            <input type="text" name="npwp_perusahaan" class="item-input" placeholder="NPWP perusahaan">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size:13px">Nama Kontak</label>
                            <input type="text" name="nama_kontak" class="item-input" placeholder="Nama kontak">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size:13px">Alamat Perusahaan</label>
                            <textarea name="alamat_perusahaan" class="item-input" rows="2" placeholder="Alamat perusahaan"></textarea>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold" style="font-size:13px">Telepon Faktur</label>
                            <input type="text" name="telepon_faktur" class="item-input" placeholder="No. telepon faktur">
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold" style="font-size:13px">No. Rekening</label>
                            <input type="text" name="rekening_perusahaan" class="item-input" placeholder="No. rekening">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btn-save-client">
                        <i class="bi bi-check-lg"></i> Simpan Client
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Quick Add Material Modal --}}
<div class="modal fade" id="quickAddMaterialModal" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Material Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="quick-material-form">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size:13px">Nama Material <span class="text-danger">*</span></label>
                            <input type="text" name="nama_material" class="item-input" required placeholder="Nama material/bahan">
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold" style="font-size:13px">Harga Satuan (Rp)</label>
                            <input type="text" name="harga_display" id="harga-display" class="item-input" value="0" placeholder="0" oninput="formatRupiahInput(this)" style="text-align:right;">
                            <input type="hidden" name="harga_material" id="harga-hidden" value="0">
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold" style="font-size:13px">Satuan</label>
                            <input type="text" name="satuan" class="item-input" value="pcs" placeholder="pcs">
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold" style="font-size:13px">Stok</label>
                            <input type="number" name="stok" class="item-input" min="0" value="0" placeholder="0">
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold" style="font-size:13px">Supplier</label>
                            <input type="text" name="supplier" class="item-input" placeholder="Nama supplier">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btn-save-material">
                        <i class="bi bi-check-lg"></i> Simpan Material
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
// ─── Data ───────────────────────────────────────────────────────────────────
const initialItems    = @json($oldItems);
const initialLabors   = @json($oldLabors);
const initialCosts    = @json($oldOtherCosts);
const defaultLabors   = @json($defaultLabors ?? []);
const materialsDB     = @json($materials ?? []);

let itemIndex  = 0;
let laborIndex = 0;
let costIndex  = 0;

// ─── Helpers ────────────────────────────────────────────────────────────────
function fmt(n) {
    return 'Rp ' + Math.round(n || 0).toLocaleString('id-ID');
}
function esc(str) {
    return String(str ?? '').replace(/&/g,'&').replace(/"/g,'"').replace(/</g,'<').replace(/>/g,'>');
}
function parseNum(v) { return parseFloat(v) || 0; }

// ─── CLIENT AUTOLOAD ────────────────────────────────────────────────────────
$(document).ready(function() {
    const $selectClient = $('#select-client');
    
    $selectClient.select2({
        theme: 'bootstrap-5',
        placeholder: '-- Pilih Client --',
        allowClear: true,
        width: '100%'
    });

    const $hiddenClient = $('#hidden-id-client');
    
    $selectClient.on('change', function() {
        const opt = $(this).find('option:selected');
        const box = document.getElementById('client-preview');
        
        // Sync value to hidden input
        $hiddenClient.val(opt.val() || '');
        
        if (!opt.val()) {
            box.classList.add('d-none');
            return;
        }
        document.getElementById('preview-company').textContent = opt.data('nama') || '-';
        document.getElementById('preview-contact').textContent = opt.data('kontak') || '-';
        document.getElementById('preview-email').textContent   = opt.data('email') || '-';
        document.getElementById('preview-address').textContent = opt.data('alamat') || '-';
        box.classList.remove('d-none');
    });

    // Ensure value is synced before form submit
    $('#quotation-form').on('submit', function() {
        // Force sync Select2 value to underlying select
        const selectedVal = $selectClient.val();
        if (selectedVal) {
            $selectClient.val(selectedVal).trigger('change.select2');
        }
    });

    // Trigger on load if edit
    @if($isEdit && $quotation->id_client)
    $selectClient.trigger('change');
    @endif
});

// ─── ITEMS + MATERIALS ──────────────────────────────────────────────────────
function createItemRow(item = {}) {
    const idx = itemIndex++;
    const tr = document.createElement('div');
    tr.className = 'p-3 border-bottom';
    tr.dataset.idx = idx;
    tr.dataset.type = 'item';

    const qty   = parseNum(item.jumlah_item ?? 1);
    const price = parseNum(item.harga_item ?? 0);
    const sub   = qty * price;

    tr.innerHTML = `
        <div class="d-flex align-items-start justify-content-between mb-2">
            <div class="d-flex align-items-center gap-2">
                <span class="item-no" id="ino-${idx}"></span>
                <strong style="font-size:14px;" id="ilabel-${idx}">Item #${idx+1}</strong>
            </div>
            <button type="button" class="btn-remove-item" onclick="removeItem(this)" title="Hapus item">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="row g-2 mb-2">
            <div class="col-12 col-sm-3">
                <input type="text" name="items[${idx}][nama_item]" class="item-input" required placeholder="Nama Item *" value="${esc(item.nama_item)}">
            </div>
            <div class="col-12 col-sm-2">
                <input type="text" name="items[${idx}][deskripsi_item]" class="item-input" placeholder="Deskripsi" value="${esc(item.deskripsi_item)}">
            </div>
            <div class="col-6 col-sm-1">
                <input type="text" name="items[${idx}][satuan]" class="item-input" required placeholder="Satuan *" value="${esc(item.satuan ?? 'Unit')}" style="text-align:center;">
            </div>
            <div class="col-6 col-sm-1">
                <input type="number" name="items[${idx}][jumlah_item]" class="item-input item-qty" required min="0" step="any" value="${qty}" placeholder="Qty *" style="text-align:right;">
            </div>
            <div class="col-6 col-sm-2">
                <div class="input-group">
                    <span class="input-group-text" style="font-size:13px;">Rp</span>
                    <input type="number" name="items[${idx}][harga_item]" class="item-input item-price form-control" required min="0" step="any" value="${price}" placeholder="Harga *" style="text-align:right;">
                </div>
            </div>
            <div class="col-6 col-sm-1 d-flex align-items-end" style="flex-direction: column; justify-content: center;">
                <div class="subtotal-cell w-100" id="isub-${idx}" style="font-size:14px;font-weight:600;">${fmt(sub)}</div>
            </div>
        </div>

        {{-- Materials sub-section --}}
        <div class="mat-section">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span style="font-size:11px;font-weight:700;text-transform:uppercase;color:#1B5DBC;letter-spacing:.04em;">Material</span>
                <div class="d-flex gap-1">
                    <button type="button" class="btn btn-outline-primary btn-sm" style="font-size:11px;padding:2px 8px;" onclick="addMaterialRow(this, ${idx})">
                        <i class="bi bi-plus"></i> Tambah Material
                    </button>
                    <button type="button" class="btn btn-outline-success btn-sm" style="font-size:11px;padding:2px 8px;" onclick="openQuickAddMaterial(${idx})">
                        <i class="bi bi-lightning"></i> Quick Add
                    </button>
                </div>
            </div>
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th style="width:24px;">#</th>
                        <th>Nama Material</th>
                        <th style="width:70px;text-align:center;">Satuan</th>
                        <th style="width:80px;text-align:right;">Qty</th>
                        <th style="width:120px;text-align:right;">Harga</th>
                        <th style="width:120px;text-align:right;">Subtotal</th>
                        <th style="width:30px;"></th>
                    </tr>
                </thead>
                <tbody class="mat-tbody" id="mat-tbody-${idx}"></tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" class="text-end fw-semibold" style="font-size:11px;color:#475569;background:#f0f4fa;">Subtotal Material</td>
                        <td class="text-end fw-bold" style="font-family:monospace;font-size:12px;color:#1B5DBC;background:#f0f4fa;" id="mat-sub-${idx}">Rp 0</td>
                        <td style="background:#f0f4fa;"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    `;

    // Bind events
    tr.querySelectorAll('.item-qty, .item-price').forEach(el => {
        el.addEventListener('input', () => { updateItemSubtotal(tr); recalcAll(); });
    });

    // Load existing materials
    const mats = item.materials || [];
    mats.forEach(m => addMaterialRow(tr.querySelector('.mat-section button'), idx, m));

    return tr;
}

function updateItemSubtotal(tr) {
    const idx = tr.dataset.idx;
    const qty = parseNum(tr.querySelector('.item-qty').value);
    const price = parseNum(tr.querySelector('.item-price').value);
    document.getElementById(`isub-${idx}`).textContent = fmt(qty * price);
}

function removeItem(btn) {
    const container = document.getElementById('items-container');
    if (container.children.length <= 1) { alert('Minimal 1 item harus ada.'); return; }
    btn.closest('[data-type="item"]').remove();
    reorderItems();
    recalcAll();
}

function reorderItems() {
    document.querySelectorAll('#items-container [data-type="item"]').forEach((tr, i) => {
        const no = tr.querySelector('[id^="ino-"]');
        const label = tr.querySelector('[id^="ilabel-"]');
        if (no) no.textContent = i + 1;
        if (label) label.textContent = 'Item #' + (i + 1);
    });
}

function addItem(item = {}) {
    const container = document.getElementById('items-container');
    const row = createItemRow(item);
    container.appendChild(row);
    reorderItems();
    recalcAll();
}

// ─── MATERIAL SELECT2 DROPDOWN ──────────────────────────────────────────────
function addMaterialRow(btn, itemIdx, mat = {}) {
    const tbody = document.getElementById(`mat-tbody-${itemIdx}`);
    const mIdx = tbody.rows.length;

    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td class="text-center text-muted" style="font-family:monospace;font-size:11px;">${mIdx + 1}</td>
        <td>
            <select class="form-select form-select-sm mat-select2" name="items[${itemIdx}][materials][${mIdx}][id_material]" style="width:100%;">
                <option value="">-- Pilih Material --</option>
                ${materialsDB.map(m => `<option value="${m.id_material}" data-satuan="${esc(m.satuan ?? '')}" data-harga="${m.harga_material ?? 0}" ${mat.id_material == m.id_material ? 'selected' : ''}>${esc(m.nama_material)}</option>`).join('')}
            </select>
            <input type="hidden" name="items[${itemIdx}][materials][${mIdx}][nama_material]" class="mat-nama-hidden" value="${esc(mat.nama_material)}">
        </td>
        <td><input type="text" class="mat-input mat-satuan" name="items[${itemIdx}][materials][${mIdx}][satuan_material]"
                   value="${esc(mat.satuan_material ?? 'pcs')}" style="text-align:center;" readonly></td>
        <td><input type="number" class="mat-input mat-qty" name="items[${itemIdx}][materials][${mIdx}][jumlah_material]"
                   value="${parseNum(mat.jumlah_material)}" min="0" step="any" style="text-align:right;"></td>
        <td><input type="number" class="mat-input mat-price" name="items[${itemIdx}][materials][${mIdx}][harga_material]"
                   value="${parseNum(mat.harga_material)}" min="0" step="any" style="text-align:right;"></td>
        <td class="text-end fw-semibold" style="font-family:monospace;font-size:12px;" class="mat-sub-cell">${fmt(parseNum(mat.jumlah_material) * parseNum(mat.harga_material))}</td>
        <td><button type="button" class="btn-remove-mat" onclick="removeMaterialRow(this, ${itemIdx})"><i class="bi bi-x"></i></button></td>
    `;

    tbody.appendChild(tr);

    // Initialize Select2
    const selectEl = tr.querySelector('.mat-select2');
    const namaHidden = tr.querySelector('.mat-nama-hidden');
    const qtyInput = tr.querySelector('.mat-qty');
    const priceInput = tr.querySelector('.mat-price');

    $(selectEl).select2({
        theme: 'bootstrap-5',
        placeholder: '-- Pilih Material --',
        allowClear: true,
        width: '100%',
        dropdownParent: $(tr).closest('.mat-section')
    });

    // Set initial nama hidden if editing
    if (mat.id_material) {
        const selectedOpt = selectEl.querySelector(`option[value="${mat.id_material}"]`);
        if (selectedOpt) {
            namaHidden.value = selectedOpt.textContent;
        }
    }

    // On material select change
    $(selectEl).on('change', function() {
        const selected = this.options[this.selectedIndex];
        if (this.value) {
            namaHidden.value = selected.textContent;
            tr.querySelector('.mat-satuan').value = selected.dataset.satuan || 'pcs';
            priceInput.value = selected.dataset.harga || 0;
        } else {
            namaHidden.value = '';
            tr.querySelector('.mat-satuan').value = 'pcs';
            priceInput.value = 0;
        }
        updateMatSubtotal(tr, itemIdx);
        recalcAll();
    });

    // Bind qty/price change
    [qtyInput, priceInput].forEach(el => {
        el.addEventListener('input', () => { updateMatSubtotal(tr, itemIdx); recalcAll(); });
    });

    // Initial subtotal
    updateMatSubtotal(tr, itemIdx);
}

function updateMatSubtotal(tr, itemIdx) {
    const qty = parseNum(tr.querySelector('.mat-qty')?.value);
    const price = parseNum(tr.querySelector('.mat-price')?.value);
    const sub = qty * price;
    const cells = tr.querySelectorAll('td');
    if (cells[5]) cells[5].textContent = fmt(sub);
    recalcMatSubtotal(itemIdx);
}

function recalcMatSubtotal(itemIdx) {
    const tbody = document.getElementById(`mat-tbody-${itemIdx}`);
    let total = 0;
    tbody.querySelectorAll('tr').forEach(tr => {
        const qty = parseNum(tr.querySelector('.mat-qty')?.value);
        const price = parseNum(tr.querySelector('.mat-price')?.value);
        total += qty * price;
    });
    const el = document.getElementById(`mat-sub-${itemIdx}`);
    if (el) el.textContent = fmt(total);
}

function removeMaterialRow(btn, itemIdx) {
    btn.closest('tr').remove();
    // Renumber
    const tbody = document.getElementById(`mat-tbody-${itemIdx}`);
    tbody.querySelectorAll('tr').forEach((tr, i) => {
        tr.querySelector('td:first-child').textContent = i + 1;
    });
    recalcMatSubtotal(itemIdx);
    recalcAll();
}

// ─── LABORS ─────────────────────────────────────────────────────────────────
function createLaborRow(labor = {}) {
    const idx = laborIndex++;
    const sdm   = parseNum(labor.jumlah_sdm ?? 1);
    const hari  = parseNum(labor.jumlah_hari ?? 1);
    const rate  = parseNum(labor.rate_hari ?? 0);
    const sub   = sdm * hari * rate;

    const tr = document.createElement('tr');
    tr.dataset.idx = idx;
    tr.innerHTML = `
        <td class="text-center text-muted" style="font-family:monospace;font-size:12px;" id="lno-${idx}"></td>
        <td><input type="text" name="labors[${idx}][nama_labor]" class="item-input" required placeholder="Nama pekerjaan" value="${esc(labor.nama_labor)}"></td>
        <td><input type="number" name="labors[${idx}][jumlah_sdm]" class="item-input labor-sdm" required min="0" value="${sdm}" style="text-align:center;"></td>
        <td><input type="number" name="labors[${idx}][jumlah_hari]" class="item-input labor-hari" required min="0" step="any" value="${hari}" style="text-align:center;"></td>
        <td><input type="number" name="labors[${idx}][rate_hari]" class="item-input labor-rate" required min="0" step="any" value="${rate}" style="text-align:right;"></td>
        <td class="subtotal-cell" id="lsub-${idx}">${fmt(sub)}</td>
        <td><button type="button" class="btn-remove-labor" onclick="removeLabor(this)"><i class="bi bi-x-lg"></i></button></td>
    `;

    tr.querySelectorAll('.labor-sdm, .labor-hari, .labor-rate').forEach(el => {
        el.addEventListener('input', () => { updateLaborSubtotal(tr); recalcAll(); });
    });
    return tr;
}

function updateLaborSubtotal(tr) {
    const idx = tr.dataset.idx;
    const sdm  = parseNum(tr.querySelector('.labor-sdm').value);
    const hari = parseNum(tr.querySelector('.labor-hari').value);
    const rate = parseNum(tr.querySelector('.labor-rate').value);
    document.getElementById(`lsub-${idx}`).textContent = fmt(sdm * hari * rate);
}

function removeLabor(btn) {
    btn.closest('tr').remove();
    reorderLabors();
    recalcAll();
}

function reorderLabors() {
    document.querySelectorAll('#labors-tbody tr').forEach((tr, i) => {
        const no = tr.querySelector('[id^="lno-"]');
        if (no) no.textContent = i + 1;
    });
}

function addLabor(labor = {}) {
    const tbody = document.getElementById('labors-tbody');
    const tr = createLaborRow(labor);
    tbody.appendChild(tr);
    reorderLabors();
    recalcAll();
}

// ─── OTHER COSTS ────────────────────────────────────────────────────────────
function createCostRow(cost = {}) {
    const idx = costIndex++;
    const qty = parseNum(cost.qty ?? 1);
    const rate = parseNum(cost.rate ?? cost.jumlah_biaya ?? 0);
    const sub = qty * rate;

    const tr = document.createElement('tr');
    tr.dataset.idx = idx;
    tr.innerHTML = `
        <td class="text-center text-muted" style="font-family:monospace;font-size:12px;" id="cno-${idx}"></td>
        <td><input type="text" name="other_costs[${idx}][nama_biaya]" class="item-input cost-name" required placeholder="Nama biaya" value="${esc(cost.nama_biaya)}"></td>
        <td><input type="number" name="other_costs[${idx}][qty]" class="item-input cost-qty" required min="0" step="1" value="${qty}" style="text-align:center;"></td>
        <td><input type="number" name="other_costs[${idx}][rate]" class="item-input cost-rate" required min="0" step="any" value="${rate}" style="text-align:right;"></td>
        <td class="subtotal-cell cost-subtotal" id="csub-${idx}">${fmt(sub)}</td>
        <td><button type="button" class="btn-remove-cost" onclick="removeCost(this)"><i class="bi bi-x-lg"></i></button></td>
    `;

    tr.querySelectorAll('.cost-qty, .cost-rate').forEach(el => {
        el.addEventListener('input', () => { updateCostSubtotal(tr); recalcAll(); });
    });
    return tr;
}

function updateCostSubtotal(tr) {
    const idx = tr.dataset.idx;
    const qty = parseNum(tr.querySelector('.cost-qty')?.value);
    const rate = parseNum(tr.querySelector('.cost-rate')?.value);
    const sub = qty * rate;
    const el = document.getElementById(`csub-${idx}`);
    if (el) el.textContent = fmt(sub);
}

function removeCost(btn) {
    const tr = btn.closest('tr');
    // Reset overheadIdx if removing overhead row
    if (tr.dataset.overhead === 'true') {
        overheadIdx = null;
    }
    tr.remove();
    reorderCosts();
    recalcAll();
}

function reorderCosts() {
    document.querySelectorAll('#costs-tbody tr').forEach((tr, i) => {
        const no = tr.querySelector('[id^="cno-"]');
        if (no) no.textContent = i + 1;
    });
}

function addCost(cost = {}) {
    const tbody = document.getElementById('costs-tbody');
    const tr = createCostRow(cost);
    tbody.appendChild(tr);
    reorderCosts();
    recalcAll();
}

// ─── OVERHEAD COST (10% dari subtotal Material) ────────────────────────────
let overheadIdx = null;

function createOverheadRow() {
    const idx = costIndex++;
    overheadIdx = idx;

    const tr = document.createElement('tr');
    tr.dataset.idx = idx;
    tr.dataset.overhead = 'true';
    tr.innerHTML = `
        <td class="text-center text-muted" style="font-family:monospace;font-size:12px;" id="cno-${idx}"></td>
        <td><input type="text" name="other_costs[${idx}][nama_biaya]" class="item-input cost-name" value="Overhead Cost" readonly style="background:#f0f4fa;font-weight:600;color:#1B5DBC;"></td>
        <td><input type="number" name="other_costs[${idx}][qty]" class="item-input cost-qty" value="1" readonly style="text-align:center;background:#f0f4fa;"></td>
        <td><input type="number" name="other_costs[${idx}][rate]" class="item-input cost-rate overhead-rate" readonly style="text-align:right;background:#f0f4fa;font-weight:600;color:#1B5DBC;"></td>
        <td class="subtotal-cell cost-subtotal" id="csub-${idx}" style="font-weight:600;color:#1B5DBC;">Rp 0</td>
        <td><button type="button" class="btn-remove-cost" onclick="removeCost(this)"><i class="bi bi-x-lg"></i></button></td>
    `;

    return tr;
}

function updateOverheadRate(subMat) {
    if (overheadIdx === null) return;
    const rate = subMat * 10 / 100; // 10% dari subtotal material
    const rateEl = document.querySelector(`tr[data-overhead="true"] .overhead-rate`);
    const subEl = document.getElementById(`csub-${overheadIdx}`);
    if (rateEl) rateEl.value = rate;
    if (subEl) subEl.textContent = fmt(rate); // qty = 1, jadi subtotal = rate
}

// ─── RECALC ALL ─────────────────────────────────────────────────────────────
function recalcAll() {
    // Material subtotal = sum of all item (qty * price) + sum of all sub-materials
    let subMat = 0;
    document.querySelectorAll('#items-container [data-type="item"]').forEach(tr => {
        const qty = parseNum(tr.querySelector('.item-qty')?.value);
        const price = parseNum(tr.querySelector('.item-price')?.value);
        subMat += qty * price;
        // Also add sub-materials
        const itemIdx = tr.dataset.idx;
        const matTbody = document.getElementById(`mat-tbody-${itemIdx}`);
        if (matTbody) {
            matTbody.querySelectorAll('tr').forEach(mtr => {
                const mq = parseNum(mtr.querySelector('.mat-qty')?.value);
                const mp = parseNum(mtr.querySelector('.mat-price')?.value);
                subMat += mq * mp;
            });
        }
    });

    // Labor subtotal
    let subLab = 0;
    document.querySelectorAll('#labors-tbody tr').forEach(tr => {
        const sdm  = parseNum(tr.querySelector('.labor-sdm')?.value);
        const hari = parseNum(tr.querySelector('.labor-hari')?.value);
        const rate = parseNum(tr.querySelector('.labor-rate')?.value);
        subLab += sdm * hari * rate;
    });

    // Other costs subtotal (qty * rate for each row)
    let subOth = 0;
    document.querySelectorAll('#costs-tbody tr').forEach(tr => {
        const qty = parseNum(tr.querySelector('.cost-qty')?.value);
        const rate = parseNum(tr.querySelector('.cost-rate')?.value);
        subOth += qty * rate;
    });

    // Update overhead rate (10% dari subtotal material)
    updateOverheadRate(subMat);

    // Recalculate other costs after overhead update
    subOth = 0;
    document.querySelectorAll('#costs-tbody tr').forEach(tr => {
        const qty = parseNum(tr.querySelector('.cost-qty')?.value);
        const rate = parseNum(tr.querySelector('.cost-rate')?.value);
        subOth += qty * rate;
    });

    const diskon = parseNum(document.getElementById('input-diskon')?.value);
    const total  = subMat + subLab + subOth - diskon;

    document.getElementById('disp-sub-material').textContent = fmt(subMat);
    document.getElementById('disp-sub-labor').textContent    = fmt(subLab);
    document.getElementById('disp-sub-lainlain').textContent  = fmt(subOth);
    document.getElementById('disp-subtotal-mat').textContent  = fmt(subMat);
    document.getElementById('disp-subtotal-lab').textContent  = fmt(subLab);
    document.getElementById('disp-subtotal-oth').textContent  = fmt(subOth);
    document.getElementById('disp-total').textContent         = fmt(total);
}

// ─── INIT ───────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    // Items
    const items = initialItems.length ? initialItems : [{}];
    items.forEach(item => addItem(item));

    // Labors
    if (initialLabors.length) {
        initialLabors.forEach(l => addLabor(l));
    } else {
        defaultLabors.forEach(l => addLabor(l));
    }

    // Other costs - add overhead row first, then any existing costs
    const overheadRow = createOverheadRow();
    document.getElementById('costs-tbody').appendChild(overheadRow);
    reorderCosts();

    if (initialCosts.length) {
        initialCosts.forEach(c => addCost(c));
    }

    // Button bindings
    document.getElementById('btn-add-item').addEventListener('click', () => addItem());
    document.getElementById('btn-add-item-2').addEventListener('click', () => addItem());
    document.getElementById('btn-add-labor').addEventListener('click', () => addLabor());
    document.getElementById('btn-add-labor-2').addEventListener('click', () => addLabor());
    document.getElementById('btn-add-cost').addEventListener('click', () => addCost());
    document.getElementById('btn-add-cost-2').addEventListener('click', () => addCost());

    // Diskon
    document.getElementById('input-diskon')?.addEventListener('input', recalcAll);

    // Initial calc
    recalcAll();
});

// ─── QUICK ADD CLIENT MODAL ─────────────────────────────────────────────────
function openQuickAddClient() {
    const modal = new bootstrap.Modal(document.getElementById('quickAddClientModal'));
    document.getElementById('quick-client-form').reset();
    modal.show();
}

document.getElementById('quick-client-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const btn = document.getElementById('btn-save-client');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Menyimpan...';
    btn.disabled = true;
    
    fetch('{{ route("admin.quotations.quick-add-client") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Add new option to select-client dropdown
            const selectClient = document.getElementById('select-client');
            const newOption = new Option(
                data.client.nama_perusahaan,
                data.client.id,
                true,
                true
            );
            // Store data attributes
            newOption.dataset.nama = data.client.nama_perusahaan;
            newOption.dataset.kontak = data.client.nama_kontak || '';
            newOption.dataset.email = data.client.email_perusahaan || '';
            newOption.dataset.alamat = data.client.alamat_perusahaan || '';
            newOption.dataset.telp = data.client.telepon_faktur || '';
            newOption.dataset.npwp = data.client.npwp_perusahaan || '';
            
            selectClient.add(newOption, null);
            
            // Trigger change to update preview
            $(selectClient).trigger('change');
            
            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('quickAddClientModal')).hide();
        } else {
            alert('Gagal menambahkan client.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menyimpan client.');
    })
    .finally(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
});

// ─── QUICK ADD MATERIAL MODAL ───────────────────────────────────────────────
let currentMaterialItemIdx = null;

function openQuickAddMaterial(itemIdx) {
    currentMaterialItemIdx = itemIdx;
    const modal = new bootstrap.Modal(document.getElementById('quickAddMaterialModal'));
    document.getElementById('quick-material-form').reset();
    document.getElementById('harga-hidden').value = 0;
    document.getElementById('harga-display').value = '0';
    modal.show();
}

function formatRupiahInput(input) {
    let value = input.value.replace(/[^0-9]/g, '');
    if (value === '') value = '0';
    input.value = Number(value).toLocaleString('id-ID');
    document.getElementById('harga-hidden').value = value;
}

document.getElementById('quick-material-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const btn = document.getElementById('btn-save-material');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Menyimpan...';
    btn.disabled = true;
    
    fetch('{{ route("admin.quotations.quickAddMaterial") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Add to materialsDB for autocomplete
            materialsDB.push({
                id_material: data.material.id_material,
                nama_material: data.material.nama_material,
                satuan: data.material.satuan,
                harga_material: data.material.harga_material
            });
            
            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('quickAddMaterialModal')).hide();
            
            // Add material row to current item
            if (currentMaterialItemIdx !== null) {
                const matBtn = document.querySelector(`[data-idx="${currentMaterialItemIdx}"] .mat-section button`);
                addMaterialRow(matBtn, currentMaterialItemIdx, {
                    id_material: data.material.id_material,
                    nama_material: data.material.nama_material,
                    satuan: data.material.satuan || 'pcs',
                    harga_material: data.material.harga_material || 0,
                    jumlah_material: 1
                });
                recalcAll();
            }
            
            // Show success message
            // alert('Material berhasil ditambahkan!');
        } else {
            alert('Gagal menambahkan material.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menyimpan material.');
    })
    .finally(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
});
</script>
@endpush
