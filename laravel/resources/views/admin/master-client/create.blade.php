@extends('layouts.app')

@section('title', $title ?? 'Tambah Data Client')
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
        <form action="{{ route('admin.master-clients.store') }}" method="POST">
            @csrf
            <div class="form-group mb-3">
                <label for="id_perusahaan">ID Perusahaan</label>
                <input type="text" class="form-control" id="id_perusahaan" name="id_perusahaan" required>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-lg-6 col-md-12 mb-3">
                        <label for="nama_perusahaan">Nama Perusahaan</label>
                        <input type="text" class="form-control" id="nama_perusahaan" name="nama_perusahaan" required>
                    </div>
                    <div class="col-lg-6 col-md-12 mb-3">
                        <label for="email_perusahaan">Email Perusahaan</label>
                        <input type="email" class="form-control" id="email_perusahaan" name="email_perusahaan">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-lg-6 col-md-12 mb-3">
                        <label for="nama_kontak_perusahaan">Nama Kontak Perusahaan</label>
                        <input type="text" class="form-control" id="nama_kontak_perusahaan" name="nama_kontak_perusahaan" required>
                    </div>
                    <div class="col-lg-6 col-md-12 mb-3">
                        <label for="npwp_perusahaan">NPWP Perusahaan</label>
                        <input type="text" class="form-control" id="npwp_perusahaan" name="npwp_perusahaan">
                    </div>
                </div>
            </div>
            <div class="form-group mb-3">
                <label for="alamat_pengiriman_perusahaah">Alamat Pengiriman</label>
                <textarea class="form-control" id="alamat_pengiriman_perusahaah" name="alamat_pengiriman_perusahaah"></textarea>
            </div>
            <div class="form-group mb-3">
                <label for="nomor_telepon_pengiriman">Telepon Pengiriman</label>
                <input type="text" class="form-control" id="nomor_telepon_pengiriman" name="nomor_telepon_pengiriman">
            </div>
            <div class="form-group mb-3">
                <label for="alamat_faktur_perusahaan">Alamat Faktur</label>
                <textarea class="form-control" id="alamat_faktur_perusahaan" name="alamat_faktur_perusahaan"></textarea>
            </div>
            <div class="form-group mb-3">
                <label for="nomor_telepon_faktur">Telepon Faktur</label>
                <input type="text" class="form-control" id="nomor_telepon_faktur" name="nomor_telepon_faktur">
            </div>
            <div class="form-group mb-3">
                <label for="alamat_efaktur_perusahaan">Alamat Efaktur</label>
                <textarea class="form-control" id="alamat_efaktur_perusahaan" name="alamat_efaktur_perusahaan"></textarea>
            </div>
            <div class="form-group mb-3">
                <label for="nomor_rekening_perusahaan">Nomor Rekening</label>
                <input type="text" class="form-control" id="nomor_rekening_perusahaan" name="nomor_rekening_perusahaan">
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</div>
@push('scripts')
@endpush
@endsection