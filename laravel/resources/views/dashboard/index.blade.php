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
     /* Bar chart — responsive height */
    /* Bar chart — responsive height */
    .chart-card-body {
        display: flex;
        flex-direction: column;
        flex: 1;
        padding: 16px;
    }
    .chart-wrap {
        display: flex; align-items: flex-end;
        justify-content: space-between; gap: 8px;
        flex: 1;          /* ← ini kuncinya, ikuti tinggi parent */
        min-height: 120px;
        padding-top: 16px;
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

{{-- ── Table + Activity ── --}}
<div class="row g-3 mb-4 align-items-stretch">
    <div class="col-12 col-md-12 col-lg-6">
    <div class="card border-0 h-100 d-flex flex-column" style="background:#fff">
        <div class="card-header d-flex align-items-center justify-content-between border-bottom border-secondary border-opacity-25 bg-transparent">
            <span class="fw-semibold text-dark">
                <i class="bi bi-graph-up-arrow me-1 text-warning"></i> Grafik Penjualan 6 Bulan Terakhir
            </span>
            <span class="text-secondary" style="font-size:12px">dalam jutaan rupiah</span>
        </div>
        <div class="card-body chart-card-body">
            <canvas id="salesLineChart"></canvas>
        </div>
    </div>
</div>
    {{-- Recent Sales --}}
    <div class="col-12 col-md-12 col-lg-6">
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
</div>


@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
    const ctx = document.getElementById('salesLineChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Nov', 'Des', 'Jan', 'Feb', 'Mar', 'Apr'],
            datasets: [{
                label: 'Penjualan (Juta)',
                data: [32, 45, 28, 38, 41, 48],
                borderColor: '#6366f1',
                backgroundColor: 'rgba(99,102,241,0.08)',
                borderWidth: 2.5,
                pointBackgroundColor: '#6366f1',
                pointRadius: 4,
                pointHoverRadius: 6,
                tension: 0.4,
                fill: true,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.parsed.y} Jt`
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11 }, color: '#6b7280' }
                },
                y: {
                    beginAtZero: false,
                    grid: { color: 'rgba(0,0,0,0.05)' },
                    ticks: {
                        font: { size: 11 }, color: '#6b7280',
                        callback: val => val + ' Jt'
                    }
                }
            }
        }
    });
</script>
@endpush
@endsection
