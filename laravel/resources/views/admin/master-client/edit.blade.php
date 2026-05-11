@extends('layouts.app')

@section('title', $title ?? 'Edit Data Client')
@section('breadcrumb', $title)

@push('styles')
@endpush

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.master-clients.index') }}" class="btn btn-secondary btn-sm">
        <i class="bi-arrow-left"></i> Kembali
    </a>
</div>
<div class="mb-4">
    <h1 class="h4 fw-bold text-dark mb-1">{{ $title }}</h1>
    <p class="text-secondary mb-0">{{ $description}}</p>
</div>
<div class="row g-3 mb-4">
    <div class="col-lg-8 col-md-12 bg-white p-3 rounded">
        <form action="{{ route('admin.master-clients.update', $client->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group mb-3">
                <label for="id_perusahaan">ID Perusahaan</label>
                <input type="text" class="form-control" id="id_perusahaan" name="id_perusahaan" value="{{ $client->id_perusahaan }}" required>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-lg-6 col-md-12 mb-3">
                        <label for="nama_perusahaan">Nama Perusahaan</label>
                        <input type="text" class="form-control" id="nama_perusahaan" name="nama_perusahaan" value="{{ $client->nama_perusahaan }}" required>
                    </div>
                    <div class="col-lg-6 col-md-12 mb-3">
                        <label for="email_perusahaan">Email Perusahaan</label>
                        <input type="email" class="form-control" id="email_perusahaan" name="email_perusahaan" value="{{ $client->email_perusahaan }}">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-lg-6 col-md-12 mb-3">
                        <label for="nama_kontak_perusahaan">Nama Kontak Perusahaan</label>
                        <input type="text" class="form-control" id="nama_kontak_perusahaan" name="nama_kontak_perusahaan" value="{{ $client->nama_kontak_perusahaan }}" required>
                    </div>
                    <div class="col-lg-6 col-md-12 mb-3">
                        <label for="npwp_perusahaan">NPWP Perusahaan</label>
                        <input type="text" class="form-control" id="npwp_perusahaan" name="npwp_perusahaan" value="{{ $client->npwp_perusahaan }}">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-lg-4 col-md-12">
                        <div class="mb-3">
                            <label for="alamat_pengiriman">Alamat Pengiriman</label>
                            <textarea class="form-control" id="alamat_pengiriman" name="alamat_pengiriman" rows="3" required>{{ $client->alamat_pengiriman }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="telepon_pengiriman">Telepon Pengiriman</label>
                            <input type="text" class="form-control" id="telepon_pengiriman" name="telepon_pengiriman" value="{{ $client->telepon_pengiriman }}" required>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-12">
                        <div class="mb-3">
                            <label for="alamat_faktur">Alamat Faktur</label>
                            <textarea class="form-control" id="alamat_faktur" name="alamat_faktur" rows="3" required>{{ $client->alamat_faktur }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="telepon_faktur">Telepon Faktur</label>
                            <input type="text" class="form-control" id="telepon_faktur" name="telepon_faktur" value="{{ $client->telepon_faktur }}" required>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-12">
                        <div class="mb-3">
                            <label for="alamat_efaktur">Alamat E-Faktur</label>
                            <textarea class="form-control" id="alamat_efaktur" name="alamat_efaktur" rows="3" required>{{ $client->alamat_efaktur }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group mb-3">
                <label for="nomor_rekening_perusahaan">Nomor Rekening</label>
                <input type="text" class="form-control" id="nomor_rekening_perusahaan" name="nomor_rekening_perusahaan" value="{{ $client->nomor_rekening_perusahaan }}">
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</div>
@push('scripts')
@endpush
@endsection