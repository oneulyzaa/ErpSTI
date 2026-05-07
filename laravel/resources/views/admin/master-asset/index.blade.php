@extends('layouts.app')

@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@push('styles')
<style>
    
</style>
@endpush

@section('content')

{{-- ── Page Header ── --}}
<div class="mb-4">
    <h1 class="h4 fw-bold text-dark mb-1">Data Asset</h1>
    <p class="text-secondary mb-0">Kelola data aset perusahaan Anda dengan mudah dan efisien.</p>
</div>

{{-- ── Stat Cards ── --}}
<div class="row g-3 mb-4">
    <div class="col-12 bg-white p-3 rounded">
        <div class="d-flex mb-3">
            <a href="{{ route('admin.master-asset.create') }}" class="btn btn-primary">+ Tambah Data Aset</a>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th width=5>No</th>
                        <th>Nama Aset</th>
                        <th>Kategori</th>
                        <th>Lokasi</th>
                        <th>Status</th>
                        <th width=25>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($assets as $asset)
                    <tr>
                        <td>{{ $asset->id }}</td>
                        <td>{{ $asset->nama_aset }}</td>
                        <td>{{ $asset->kategori }}</td>
                        <td>{{ $asset->lokasi }}</td>
                        <td>
                            @if($asset->is_active == 1)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-warning">Perawatan</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.master-asset.edit', $asset->id) }}" class="btn btn-sm btn-primary">Edit</a>
                            <form action="{{ route('admin.master-asset.destroy', $asset->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data aset tersedia.</td>
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
