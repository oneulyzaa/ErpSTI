@extends('layouts.app')

@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@push('styles')
<style>
    /* Stat card hover lift */
    .stat-card { transition: transform .2s ease, box-shadow .2s ease; }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,.35) !important; }

    /* Stat icon circles */
    .stat-icon {
        width: 48px; height: 48px;
        border-radius: 12px;
        display: grid; place-items: center;
        font-size: 22px; flex-shrink: 0;
    }

    /* Quick link card */
    .quick-card { transition: transform .2s ease, border-color .2s ease; }
    .quick-card:hover { transform: translateY(-2px); border-color: #6366f1 !important; }

    /* Activity dot */
    .activity-dot {
        width: 34px; height: 34px; border-radius: 50%;
        display: grid; place-items: center;
        font-size: 15px; flex-shrink: 0;
    }

    /* Bar chart */
    .bar-col { display: flex; flex-direction: column; align-items: center; gap: 6px; flex: 1; }
    .bar-fill {
        width: 100%; border-radius: 6px 6px 0 0;
        background: linear-gradient(180deg, #6366f1, #4f52d9);
        min-height: 8px; transition: opacity .2s;
    }
    .bar-fill:hover { opacity: .75; }
    .chart-wrap {
        display: flex; align-items: flex-end;
        justify-content: space-between; gap: 8px;
        height: 160px; padding-top: 16px;
    }
</style>
@endpush

@section('content')

{{-- ── Page Header ── --}}
<div class="mb-4">
    <h1 class="h4 fw-bold text-dark mb-1">Selamat Datang 👋</h1>
    <p class="text-secondary mb-0" style="font-size:13px">
        Ringkasan aktivitas penjualan hari ini —
        {{ now()->translatedFormat('l, d F Y') }}
    </p>
</div>

{{-- ── Stat Cards ── --}}
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm stat-card h-100" style="background:#fff">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(99,102,241,.18);color:#818cf8">
                    <i class="bi bi-cart-check-fill"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark" style="font-size:22px;letter-spacing:-.02em">Rp 48,5 Jt</div>
                    <div class="text-secondary" style="font-size:12px">Total Penjualan Bulan Ini</div>
                    <div class="text-success d-flex align-items-center gap-1 mt-1" style="font-size:11px;font-weight:600">
                        <i class="bi bi-arrow-up-short"></i> +12% dari bulan lalu
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm stat-card h-100" style="background:#fff">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(34,197,94,.18);color:#4ade80">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark" style="font-size:22px;letter-spacing:-.02em">128</div>
                    <div class="text-secondary" style="font-size:12px">Total Klien Aktif</div>
                    <div class="text-success d-flex align-items-center gap-1 mt-1" style="font-size:11px;font-weight:600">
                        <i class="bi bi-arrow-up-short"></i> +5 klien baru
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm stat-card h-100" style="background:#fff">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(245,158,11,.18);color:#fbbf24">
                    <i class="bi bi-file-earmark-text-fill"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark" style="font-size:22px;letter-spacing:-.02em">24</div>
                    <div class="text-secondary" style="font-size:12px">Penawaran Pending</div>
                    <div class="text-warning d-flex align-items-center gap-1 mt-1" style="font-size:11px;font-weight:600">
                        <i class="bi bi-arrow-down-short"></i> 8 menunggu approval
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm stat-card h-100" style="background:#fff">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(34,211,238,.18);color:#22d3ee">
                    <i class="bi bi-file-earmark-richtext-fill"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark" style="font-size:22px;letter-spacing:-.02em">15</div>
                    <div class="text-secondary" style="font-size:12px">Invoice Belum Lunas</div>
                    <div class="text-danger d-flex align-items-center gap-1 mt-1" style="font-size:11px;font-weight:600">
                        <i class="bi bi-arrow-down-short"></i> Rp 12,3 Jt outstanding
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Quick Links ── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <a href="{{ url('/clients/create') }}" class="card border text-decoration-none quick-card h-100" style="background:#fff" id="quick-add-client">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="stat-icon" style="background:rgba(99,102,241,.15);color:#818cf8;width:40px;height:40px;font-size:18px">
                    <i class="bi bi-person-plus-fill"></i>
                </div>
                <div>
                    <div class="fw-semibold text-dark" style="font-size:13px">Tambah Klien</div>
                    <div class="text-secondary" style="font-size:11px">Daftarkan baru</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ url('/quotations/create') }}" class="card border text-decoration-none quick-card h-100" style="background:#fff" id="quick-add-quotation">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="stat-icon" style="background:rgba(99,102,241,.15);color:#818cf8;width:40px;height:40px;font-size:18px">
                    <i class="bi bi-file-earmark-plus-fill"></i>
                </div>
                <div>
                    <div class="fw-semibold text-dark" style="font-size:13px">Buat Penawaran</div>
                    <div class="text-secondary" style="font-size:11px">Quotation baru</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ url('/delivery-orders/create') }}" class="card border text-decoration-none quick-card h-100" style="background:#fff" id="quick-add-delivery">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="stat-icon" style="background:rgba(99,102,241,.15);color:#818cf8;width:40px;height:40px;font-size:18px">
                    <i class="bi bi-truck"></i>
                </div>
                <div>
                    <div class="fw-semibold text-dark" style="font-size:13px">Surat Jalan</div>
                    <div class="text-secondary" style="font-size:11px">Buat baru</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ url('/reports/sales') }}" class="card border text-decoration-none quick-card h-100" style="background:#fff" id="quick-report">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="stat-icon" style="background:rgba(99,102,241,.15);color:#818cf8;width:40px;height:40px;font-size:18px">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
                <div>
                    <div class="fw-semibold text-dark" style="font-size:13px">Lap. Penjualan</div>
                    <div class="text-secondary" style="font-size:11px">Lihat rekap</div>
                </div>
            </div>
        </a>
    </div>
</div>

{{-- ── Table + Activity ── --}}
<div class="row g-3 mb-4">

    {{-- Recent Sales --}}
    <div class="col-12 col-lg-8">
        <div class="card border-0 h-100" style="background:#fff">
            <div class="card-header d-flex align-items-center justify-content-between border-bottom border-secondary border-opacity-25 bg-transparent">
                <span class="fw-semibold text-dark">
                    <i class="bi bi-cart-check text-primary me-1"></i> Penjualan Terbaru
                </span>
                <a href="{{ url('/sales') }}" class="btn btn-sm btn-outline-secondary" id="btn-view-all-sales">
                    Lihat Semua <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-borderless align-middle mb-0" style="font-size:13px">
                        <thead class="border-bottom border-secondary border-opacity-25">
                            <tr class="text-secondary" style="font-size:11px;text-transform:uppercase;letter-spacing:.05em">
                                <th class="ps-3">#</th>
                                <th>No. Order</th>
                                <th>Klien</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th class="pe-3">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="ps-3 text-secondary">1</td>
                                <td><span class="fw-semibold text-primary">#SO-2024-001</span></td>
                                <td>PT. Maju Bersama</td>
                                <td class="fw-semibold">Rp 5.500.000</td>
                                <td><span class="badge rounded-pill text-bg-success"><i class="bi bi-check-circle-fill me-1"></i>Selesai</span></td>
                                <td class="pe-3 text-secondary">25 Apr 2026</td>
                            </tr>
                            <tr>
                                <td class="ps-3 text-secondary">2</td>
                                <td><span class="fw-semibold text-primary">#SO-2024-002</span></td>
                                <td>CV. Sukses Jaya</td>
                                <td class="fw-semibold">Rp 3.200.000</td>
                                <td><span class="badge rounded-pill text-bg-warning"><i class="bi bi-clock-fill me-1"></i>Proses</span></td>
                                <td class="pe-3 text-secondary">24 Apr 2026</td>
                            </tr>
                            <tr>
                                <td class="ps-3 text-secondary">3</td>
                                <td><span class="fw-semibold text-primary">#SO-2024-003</span></td>
                                <td>PT. Teknologi Nusantara</td>
                                <td class="fw-semibold">Rp 8.750.000</td>
                                <td><span class="badge rounded-pill text-bg-success"><i class="bi bi-check-circle-fill me-1"></i>Selesai</span></td>
                                <td class="pe-3 text-secondary">23 Apr 2026</td>
                            </tr>
                            <tr>
                                <td class="ps-3 text-secondary">4</td>
                                <td><span class="fw-semibold text-primary">#SO-2024-004</span></td>
                                <td>UD. Karya Mandiri</td>
                                <td class="fw-semibold">Rp 1.800.000</td>
                                <td><span class="badge rounded-pill text-bg-danger"><i class="bi bi-x-circle-fill me-1"></i>Batal</span></td>
                                <td class="pe-3 text-secondary">22 Apr 2026</td>
                            </tr>
                            <tr>
                                <td class="ps-3 text-secondary">5</td>
                                <td><span class="fw-semibold text-primary">#SO-2024-005</span></td>
                                <td>PT. Globalindo Raya</td>
                                <td class="fw-semibold">Rp 12.400.000</td>
                                <td><span class="badge rounded-pill text-bg-primary"><i class="bi bi-file-earmark-check-fill me-1"></i>Invoice</span></td>
                                <td class="pe-3 text-secondary">21 Apr 2026</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Activity Feed --}}
    <div class="col-12 col-lg-4">
        <div class="card border-0 h-100" style="background:#fff">
            <div class="card-header border-bottom border-secondary border-opacity-25 bg-transparent">
                <span class="fw-semibold text-dark">
                    <i class="bi bi-activity me-1" style="color:#22d3ee"></i> Aktivitas Terbaru
                </span>
            </div>
            <div class="card-body p-3">
                <div class="d-flex flex-column">

                    <div class="d-flex gap-3 py-3 border-bottom border-secondary border-opacity-25">
                        <div class="activity-dot" style="background:rgba(34,197,94,.18);color:#4ade80">
                            <i class="bi bi-check-lg"></i>
                        </div>
                        <div>
                            <div style="font-size:13px">Invoice <strong>#INV-055</strong> telah dibayar lunas oleh PT. Maju Bersama</div>
                            <div class="text-secondary mt-1" style="font-size:11px"><i class="bi bi-clock me-1"></i>2 jam lalu</div>
                        </div>
                    </div>

                    <div class="d-flex gap-3 py-3 border-bottom border-secondary border-opacity-25">
                        <div class="activity-dot" style="background:rgba(99,102,241,.18);color:#818cf8">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                        <div>
                            <div style="font-size:13px">Penawaran <strong>#QT-088</strong> dibuat untuk CV. Sukses Jaya</div>
                            <div class="text-secondary mt-1" style="font-size:11px"><i class="bi bi-clock me-1"></i>4 jam lalu</div>
                        </div>
                    </div>

                    <div class="d-flex gap-3 py-3 border-bottom border-secondary border-opacity-25">
                        <div class="activity-dot" style="background:rgba(34,211,238,.18);color:#22d3ee">
                            <i class="bi bi-truck"></i>
                        </div>
                        <div>
                            <div style="font-size:13px">Surat Jalan <strong>#SJ-031</strong> dikirim ke PT. Teknologi Nusantara</div>
                            <div class="text-secondary mt-1" style="font-size:11px"><i class="bi bi-clock me-1"></i>6 jam lalu</div>
                        </div>
                    </div>

                    <div class="d-flex gap-3 py-3 border-bottom border-secondary border-opacity-25">
                        <div class="activity-dot" style="background:rgba(99,102,241,.18);color:#818cf8">
                            <i class="bi bi-person-plus"></i>
                        </div>
                        <div>
                            <div style="font-size:13px">Klien baru <strong>UD. Karya Mandiri</strong> berhasil didaftarkan</div>
                            <div class="text-secondary mt-1" style="font-size:11px"><i class="bi bi-clock me-1"></i>Kemarin, 15:30</div>
                        </div>
                    </div>

                    <div class="d-flex gap-3 py-3">
                        <div class="activity-dot" style="background:rgba(245,158,11,.18);color:#fbbf24">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <div>
                            <div style="font-size:13px">Stok <strong>Monitor LED 24"</strong> hampir habis (sisa 3 unit)</div>
                            <div class="text-secondary mt-1" style="font-size:11px"><i class="bi bi-clock me-1"></i>Kemarin, 11:00</div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Bar Chart ── --}}
<div class="card border-0" style="background:#fff">
    <div class="card-header d-flex align-items-center justify-content-between border-bottom border-secondary border-opacity-25 bg-transparent">
        <span class="fw-semibold text-dark">
            <i class="bi bi-bar-chart-line-fill me-1 text-warning"></i> Grafik Penjualan 6 Bulan Terakhir
        </span>
        <span class="text-secondary" style="font-size:12px">dalam jutaan rupiah</span>
    </div>
    <div class="card-body">
        <div class="chart-wrap">
            @php
                $months = [
                    ['label'=>'Nov','val'=>32,'h'=>55],
                    ['label'=>'Des','val'=>45,'h'=>78],
                    ['label'=>'Jan','val'=>28,'h'=>48],
                    ['label'=>'Feb','val'=>38,'h'=>66],
                    ['label'=>'Mar','val'=>41,'h'=>71],
                    ['label'=>'Apr','val'=>48,'h'=>83],
                ];
            @endphp
            @foreach($months as $m)
            <div class="bar-col">
                <span class="text-dark" style="font-size:10px;font-weight:600">{{ $m['val'] }} Jt</span>
                <div class="bar-fill" style="height:{{ $m['h'] }}px" title="{{ $m['val'] }} juta"></div>
                <span class="text-secondary" style="font-size:10px">{{ $m['label'] }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

@endsection
