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
    <p class="text-secondary mb-0">{{ $description }}</p>
</div>
<div class="row g-3 mb-4">
    <div class="col-lg-8 col-md-12 bg-white p-3 rounded">
        <form action="{{ route('admin.master-clients.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <div class="row">
                    <div class="col-lg-6 col-md-12 mb-3">
                        <label for="id_customer">ID Customer <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="id_customer" name="id_customer" maxlength="5" required>
                    </div>
                    <div class="col-lg-6 col-md-12 mb-3">
                        <label for="nama_perusahaan">Nama Perusahaan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama_perusahaan" name="nama_perusahaan" required>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-lg-6 col-md-12 mb-3">
                        <label for="nama_kontak">Nama Kontak <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama_kontak" name="nama_kontak" maxlength="100" required>
                    </div>
                    <div class="col-lg-6 col-md-12 mb-3">
                        <label for="email_perusahaan">Email Perusahaan</label>
                        <input type="email" class="form-control" id="email_perusahaan" name="email_perusahaan" maxlength="100">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-lg-4 col-md-12">
                        <div class="mb-3">
                            <label for="alamat_perusahaan">Alamat Perusahaan <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="alamat_perusahaan" name="alamat_perusahaan" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-12">
                        <div class="mb-3">
                            <label for="alamat_faktur">Alamat Faktur</label>
                            <textarea class="form-control" id="alamat_faktur" name="alamat_faktur" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-12">
                        <div class="mb-3">
                            <label for="alamat_efaktur">Alamat E-Faktur</label>
                            <textarea class="form-control" id="alamat_efaktur" name="alamat_efaktur" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12">
                        <div class="mb-3">
                            <label for="telepon_faktur">Telepon Faktur</label>
                            <input type="text" class="form-control" id="telepon_faktur" name="telepon_faktur" maxlength="20">
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12">
                        <div class="mb-3">
                            <label for="telepon_efaktur">Telepon E-Faktur</label>
                            <input type="text" class="form-control" id="telepon_efaktur" name="telepon_efaktur" maxlength="20">
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-lg-6 col-md-12 mb-3">
                        <label for="npwp_perusahaan">NPWP Perusahaan</label>
                        <input type="text" class="form-control" id="npwp_perusahaan" name="npwp_perusahaan" maxlength="50">
                    </div>
                    <div class="col-lg-6 col-md-12 mb-3">
                        <label for="rekening_perusahaan">Nomor Rekening</label>
                        <input type="text" class="form-control" id="rekening_perusahaan" name="rekening_perusahaan" maxlength="50">
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</div>
@push('scripts')
@endpush
@endsection