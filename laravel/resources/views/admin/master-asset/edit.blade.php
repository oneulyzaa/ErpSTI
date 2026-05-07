@extends('layouts.app')

@section('title', $title ?? 'Tambah Data Asset')
@section('breadcrumb', $title)

@push('styles')
<style>
    
</style>
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
    <p class="text-secondary mb-0">{{ $description}}</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-6 col-md-12 bg-white p-3 rounded">
        <form action="{{ route('admin.master-assets.update', $asset->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group mb-3">
                <label for="nama_aset">Nama Aset</label>
                <input type="text" class="form-control" id="nama_aset" name="nama_aset" value="{{ $asset->nama_aset }}" required>
            </div>
            <div class="form-group mb-3">
                <label for="harga">Harga</label>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="text" class="form-control" id="harga" name="harga" value="{{ number_format($asset->harga, 0, ',', '.') }}" required oninput="formatRupiah(this)" autocomplete="off">
                </div>
            </div>
            <div class="form-group mb-3">
                <label for="satuan">Satuan</label>
                <select class="form-select" id="satuan" name="satuan" required>
                    {{-- <option value="" disabled selected>Pilih Satuan</option> --}}
                    <option value="pcs" {{ $asset->satuan == 'pcs' ? 'selected' : '' }}>Pcs</option>
                    <option value="box" {{ $asset->satuan == 'box' ? 'selected' : '' }}>Box</option>
                    <option value="kg" {{ $asset->satuan == 'kg' ? 'selected' : '' }}>Kg</option>
                    <option value="liter" {{ $asset->satuan == 'liter' ? 'selected' : '' }}>Liter</option>
                </select>
            </div>
            <div class="form-group mb-3">
                <label for="stok">Stok</label>
                <input type="text" class="form-control" id="stok" name="stok" value="{{ $asset->stok }}" required>
            </div>
            <div class="form-group mb-3">
                <label for="supplier_from">Supplier</label>
                <input type="text" class="form-control" id="supplier_from" name="supplier_from" value="{{ $asset->supplier_from }}">
            </div>
            <div class="form-group mb-3">
                <label for="status">Status</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="" disabled >Pilih Status</option>
                    <option value="1" {{ $asset->is_active == 1 ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ $asset->is_active == 0 ? 'selected' : '' }}>Tidak Aktif</option>
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
