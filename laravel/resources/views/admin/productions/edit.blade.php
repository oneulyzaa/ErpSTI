@extends('layouts.app')
@section('title', 'Edit Status Produksi ' . $production->production_number)
@section('breadcrumb', 'Edit Status Produksi')

@push('styles')
<style>
    .badge-planned     { background:#e2e8f0; color:#475569; }
    .badge-in_progress { background:#fef3c7; color:#92400e; }
    .badge-completed   { background:#dcfce7; color:#15803d; }
    .badge-cancelled   { background:#fee2e2; color:#b91c1c; }
</style>
@endpush

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Edit Status Produksi</h4>
        <p class="text-muted mb-0" style="font-size:13px">
            No. Produksi: <span class="fw-semibold">{{ $production->production_number }}</span>
        </p>
    </div>
    <a href="{{ route('admin.productions.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="row g-3 align-items-start">
    <div class="col-12 col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <span class="fw-semibold">Detail Produksi</span>
            </div>
            <div class="card-body">
                <table class="table table-sm" style="font-size:13px;">
                    <tr>
                        <td class="text-muted" style="width:140px;">No. Produksi</td>
                        <td class="fw-semibold">{{ $production->production_number }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Ref. SO</td>
                        <td>{{ $production->so_number }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Project</td>
                        <td>{{ $production->project_name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Klien</td>
                        <td>{{ $production->client_company ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tanggal</td>
                        <td>{{ $production->date->format('d M Y') }}</td>
                    </tr>
                    @if($production->target_date)
                    <tr>
                        <td class="text-muted">Target Selesai</td>
                        <td>{{ $production->target_date->format('d M Y') }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="text-muted">Status Saat Ini</td>
                        <td>
                            @php
                                $statusMap = [
                                    'planned' => ['planned', 'Planned'],
                                    'in_progress' => ['in_progress', 'In Progress'],
                                    'completed' => ['completed', 'Completed'],
                                    'cancelled' => ['cancelled', 'Cancelled'],
                                ];
                                $s = $statusMap[$production->status] ?? ['planned', '-'];
                            @endphp
                            <span class="badge badge-{{ $s[0] }}">{{ $s[1] }}</span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <span class="fw-semibold">Update Status</span>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.productions.update', $production) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            @foreach(['planned'=>'Planned','in_progress'=>'In Progress','completed'=>'Completed','cancelled'=>'Cancelled'] as $v=>$l)
                                <option value="{{ $v }}" {{ old('status', $production->status) === $v ? 'selected' : '' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                        @error('status')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px">Catatan</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Catatan update status...">{{ old('notes', $production->notes) }}</textarea>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Simpan Status
                        </button>
                        <a href="{{ route('admin.productions.index') }}" class="btn btn-outline-secondary text-center">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
