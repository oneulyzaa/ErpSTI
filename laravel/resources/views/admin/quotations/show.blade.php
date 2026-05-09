@extends('layouts.app')
@section('title', 'Detail ' . $quotation->quote_number)
@section('breadcrumb', 'Detail Quotation')

@push('styles')
<style>
    .info-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #94a3b8; margin-bottom: 4px; }
    .info-value { font-size: 14px; color: #1e293b; }
    .summary-row { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; font-size: 14px; color: #475569; border-bottom: 1px solid #f1f5f9; }
    .summary-row:last-child { border-bottom: none; }
    .summary-row.total-row { font-size: 18px; font-weight: 700; color: #1e293b; border-top: 2px solid #e2e8f0; border-bottom: none; margin-top: 4px; padding-top: 12px; }
    .summary-val { font-family: monospace; font-size: 13px; color: #1e293b; }
    .summary-row.total-row .summary-val { font-size: 18px; color: #1B5DBC; font-weight: 700; }
    .badge-draft    { background:#e2e8f0; color:#475569; }
    .badge-sent     { background:#dbeafe; color:#1d4ed8; }
    .badge-approved { background:#dcfce7; color:#15803d; }
    .badge-rejected { background:#fee2e2; color:#b91c1c; }
    .badge-expired  { background:#fef9c3; color:#92400e; }
</style>
@endpush

@section('content')
@include('admin.quotations.show-content')
@endsection