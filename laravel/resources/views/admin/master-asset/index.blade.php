@extends('layouts.app')

@section('title', $title ?? 'Data Material')
@section('breadcrumb', $title ?? 'Data Material')

@push('styles')
@endpush

@section('content')

{{-- ── Page Header ── --}}
<div class="mb-4">
    <h1 class="h4 fw-bold text-dark mb-1">{{ $title }}</h1>
    <p class="text-secondary mb-0">{{ $description }}</p>
</div>

{{-- ── Stat Cards ── --}}
<div class="row g-3 mb-4">
    <div class="col-12 bg-white p-3 rounded">
        <div class="d-flex mb-3">
            <a href="{{ route('admin.master-assets.create') }}" class="btn btn-primary">+ Tambah Data Material</a>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width=5>No</th>
                        <th>Nama Material</th>
                        <th>Harga</th>
                        <th>Satuan</th>
                        <th>Stok</th>
                        <th>Supplier</th>
                        <th>Status</th>
                        <th width=195>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($materials as $material)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $material->nama_material }}</td>
                        <td>{{ \App\Http\Controllers\Controller::rupiah($material->harga_material) }}</td>
                        <td>{{ $material->satuan }}</td>
                        <td>{{ $material->stok }}</td>
                        <td>{{ $material->supplier }}</td>
                        <td>
                            @if($material->status_material == 'Tersedia')
                                <span class="badge bg-success">Tersedia</span>
                            @else
                                <span class="badge bg-dark">Habis</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.master-assets.edit', $material->id_material) }}" class="btn btn-sm btn-primary">
                                <i class="bi-pencil"></i> Ubah
                            </a>
                            <form action="{{ route('admin.master-assets.destroy', $material->id_material) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                    <i class="bi-trash"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center">Tidak ada data material tersedia.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
@endpush
@endsection