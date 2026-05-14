@php
    $pIdx = $pIdx ?? 0;
    $pItem = $pItem ?? [];
    $assets = $assets ?? [];
    $materials = $pItem['materials'] ?? [];
@endphp
<div class="product-card" data-idx="{{ $pIdx }}">
    <div class="product-card-header">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <span class="fw-semibold" style="font-size:14px;min-width:60px;">#{{ $loop->iteration ?? ($pIdx + 1) }}</span>
            <input type="hidden" name="items[{{ $pIdx }}][sales_order_item_id]" value="{{ $pItem['sales_order_item_id'] ?? '' }}">
            <input type="text" name="items[{{ $pIdx }}][product_name]" class="form-control form-control-sm" required
                   style="width:200px;" value="{{ $pItem['product_name'] ?? '' }}" placeholder="Nama Produk">
            <input type="number" name="items[{{ $pIdx }}][product_qty]" class="form-control form-control-sm" required
                   style="width:80px;" min="0" step="any" value="{{ $pItem['product_qty'] ?? 1 }}" placeholder="Qty">
            <input type="text" name="items[{{ $pIdx }}][unit]" class="form-control form-control-sm" required
                   style="width:80px;" value="{{ $pItem['unit'] ?? 'Unit' }}" placeholder="Satuan">
            <select name="items[{{ $pIdx }}][status]" class="form-select form-select-sm" style="width:120px;">
                <option value="pending" {{ ($pItem['status'] ?? 'pending') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="in_progress" {{ ($pItem['status'] ?? '') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="completed" {{ ($pItem['status'] ?? '') === 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeProduct(this)"><i class="bi bi-trash"></i></button>
        </div>
    </div>
    <div class="p-3">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <span style="font-size:12px;font-weight:600;color:#1B5DBC;">BAHAN BAKU</span>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addMaterialRow(this, {{ $pIdx }})">
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
            <tbody id="mattbody-{{ $pIdx }}">
                @foreach($materials as $mIdx => $mat)
                <tr data-idx="{{ $mIdx }}">
                    <td class="item-no" id="mno-{{ $pIdx }}-{{ $mIdx }}">{{ $loop->iteration }}</td>
                    <td>
                        <select name="items[{{ $pIdx }}][materials][{{ $mIdx }}][asset_id]" class="form-select form-select-sm material-select"
                                onchange="onMatSelect(this, {{ $pIdx }}, {{ $mIdx }})"
                                style="font-size:12px;">
                            <option value="">-- Pilih Bahan Baku --</option>
                            @foreach($assets as $a)
                                <option value="{{ $a->id }}" data-nama="{{ $a->nama_aset }}" data-satuan="{{ $a->satuan }}"
                                    {{ ($mat['asset_id'] ?? '') == $a->id ? 'selected' : '' }}>{{ $a->nama_aset }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="items[{{ $pIdx }}][materials][{{ $mIdx }}][nama_bahan_baku]" class="mat-name-hidden"
                               value="{{ $mat['nama_bahan_baku'] ?? '' }}">
                    </td>
                    <td><input type="text" name="items[{{ $pIdx }}][materials][{{ $mIdx }}][satuan]" class="form-control form-control-sm mat-satuan"
                               style="font-size:12px;text-align:center;" value="{{ $mat['satuan'] ?? 'pcs' }}"></td>
                    <td><input type="number" name="items[{{ $pIdx }}][materials][{{ $mIdx }}][qty_required]" class="form-control form-control-sm"
                               style="font-size:12px;text-align:right;" min="0" step="any" value="{{ $mat['qty_required'] ?? 1 }}"></td>
                    <td><button type="button" class="btn-remove-row" onclick="removeMatRow(this)"><i class="bi bi-x-lg"></i></button></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
// Ensure mIdx is initialized for this product card
document.addEventListener('DOMContentLoaded', function() {
    if (typeof mIdx === 'undefined') mIdx = {};
    mIdx['p{{ $pIdx }}'] = {{ count($materials) }};
});
</script>
@endpush
