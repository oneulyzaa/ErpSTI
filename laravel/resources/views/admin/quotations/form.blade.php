@php
    $isEdit = $isEdit ?? false;
    $action = $isEdit ? route('admin.quotations.update', $quotation) : route('admin.quotations.store');
    $method = $isEdit ? 'PUT' : 'POST';
    $oldItems = old('items', $isEdit && isset($quotation) ? $quotation->items->toArray() : []);
@endphp

@extends('layouts.app')
@section('title', $isEdit ? 'Edit Quotation' : 'Buat Quotation Baru')
@section('breadcrumb', $isEdit ? 'Edit Quotation' : 'Buat Quotation')

@section('content')
<form action="{{ $action }}" method="POST">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif
    <!-- Tambahkan field sesuai kebutuhan quotation -->
    <div class="mb-3">
        <label for="quote_number" class="form-label">No. Quotation</label>
        <input type="text" class="form-control" id="quote_number" name="quote_number" value="{{ old('quote_number', $isEdit ? $quotation->quote_number : $quoteNumber ?? '') }}" required>
    </div>
    <div class="mb-3">
        <label for="date" class="form-label">Tanggal</label>
        <input type="date" class="form-control" id="date" name="date" value="{{ old('date', $isEdit ? $quotation->date->format('Y-m-d') : '') }}" required>
    </div>
    <div class="mb-3">
        <label for="valid_until" class="form-label">Berlaku s/d</label>
        <input type="date" class="form-control" id="valid_until" name="valid_until" value="{{ old('valid_until', $isEdit ? $quotation->valid_until->format('Y-m-d') : '') }}" required>
    </div>
    <div class="mb-3">
        <label for="client_name" class="form-label">Nama Klien</label>
        <input type="text" class="form-control" id="client_name" name="client_name" value="{{ old('client_name', $isEdit ? $quotation->client_name : '') }}" required>
    </div>
    <div class="mb-3">
        <label for="client_company" class="form-label">Perusahaan Klien</label>
        <input type="text" class="form-control" id="client_company" name="client_company" value="{{ old('client_company', $isEdit ? $quotation->client_company : '') }}" required>
    </div>
    <!-- Tambahkan field lain sesuai kebutuhan -->
    <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Update' : 'Simpan' }}</button>
</form>
@endsection
