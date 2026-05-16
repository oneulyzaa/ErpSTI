@extends('layouts.app')

@section('title', $title ?? 'Data Client')
@section('breadcrumb', $title ?? 'Data Client')

@push('styles')
@endpush

@section('content')

{{-- ── Page Header ── --}}
<div class="mb-4">
    <h1 class="h4 fw-bold text-dark mb-1">{{ $title }}</h1>
    <p class="text-secondary mb-0">{{ $description}}</p>
</div>

{{-- ── Stat Cards ── --}}
<div class="row g-3 mb-4">
    <div class="col-12 bg-white p-3 rounded">
        <div class="d-flex mb-3">
            <a href="{{ route('admin.master-clients.create') }}" class="btn btn-primary">+ Tambah Data Client</a>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>ID Perusahaan</th>
                        <th>Nama Perusahaan</th>
                        <th>Email</th>
                        <th>Kontak</th>
                        <th>NPWP</th>
                        <th>Alamat Pengiriman</th>
                        <th>Telepon Pengiriman</th>
                        <th>Alamat Faktur</th>
                        <th>Telepon Faktur</th>
                        <th>Alamat Efaktur</th>
                        <th>Rekening</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($clients as $client)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $client->id_perusahaan }}</td>
                        <td>{{ $client->nama_perusahaan }}</td>
                        <td>{{ $client->email_perusahaan }}</td>
                        <td>{{ $client->nama_kontak_perusahaan }}</td>
                        <td>{{ $client->npwp_perusahaan }}</td>
                        <td>{{ $client->alamat_pengiriman_perusahaan }}</td>
                        <td>{{ $client->nomor_telepon_pengiriman }}</td>
                        <td>{{ $client->alamat_faktur_perusahaan }}</td>
                        <td>{{ $client->nomor_telepon_faktur }}</td>
                        <td>{{ $client->alamat_efaktur_perusahaan }}</td>
                        <td>{{ $client->nomor_rekening_perusahaan }}</td>
                        <td>
                            <a href="{{ route('admin.master-clients.edit', $client->id) }}" class="btn btn-sm btn-primary">
                                <i class="bi-pencil"></i> Ubah
                            </a>
                            <form action="{{ route('admin.master-clients.destroy', $client->id) }}" method="POST" class="d-inline">
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
                        <td colspan="13" class="text-center">Tidak ada data client tersedia.</td>
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