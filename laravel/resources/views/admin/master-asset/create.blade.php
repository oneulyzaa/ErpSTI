@extends('layouts.app')

@section('title', $title ?? 'Tambah Data Material')
@section('breadcrumb', $title)

@push('styles')
@endpush

@section('content')

{{-- ── Page Header ── --}}
<div class="mb-4">
    <a href="{{ route('admin.master-assets.index') }}" class="btn btn-secondary btn-sm">
        <i class="bi-arrow-left"></i> Kembali
    </a>
</div>
<div class="mb-4">
    <h1 class="h4 fw-bold text-dark mb-1">{{ $title }}</h1>
    <p class="text-secondary mb-0">{{ $description }}</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-6 col-md-12 bg-white p-3 rounded">
        <form action="{{ route('admin.master-assets.store') }}" method="POST">
            @csrf
            <div class="form-group mb-3">
                <label for="nama_material">Nama Material <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nama_material" name="nama_material" required>
            </div>
            <div class="form-group mb-3">
                <label for="harga_material">Harga <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="text" class="form-control" id="harga_material" name="harga_material" required oninput="formatRupiah(this)" autocomplete="off">
                </div>
            </div>
            <div class="form-group mb-3">
                <label for="satuan">Satuan <span class="text-danger">*</span></label>
                <select class="form-select" id="satuan" name="satuan" required>
                    <option value="" disabled selected>- Pilih Satuan -</option>
                    <option value="pcs">Pcs</option>
                    <option value="meter">Meter</option>
                    <option value="box">Box</option>
                    <option value="kg">Kg</option>
                </select>
            </div>
            <div class="form-group mb-3">
                <label for="stok">Stok <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="stok" name="stok" min="0" required>
            </div>
            <div class="form-group mb-3">
                <label for="supplier">Supplier</label>
                <input type="text" class="form-control" id="supplier" name="supplier">
            </div>
            <div class="form-group mb-3">
                <label for="status_material">Status <span class="text-danger">*</span></label>
                <select class="form-select" id="status_material" name="status_material" required>
                    <option value="" disabled selected>- Pilih Status -</option>
                    <option value="Tersedia" selected>Tersedia</option>
                    <option value="Habis">Habis</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</div>

@push('scripts')
<script>
function formatRupiah(el) {
    let value = el.value.replace(/[^\d]/g, "");
    if (!value) {
        el.value = "";
        return;
    }
    el.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}
</script>
@endpush
@endsection