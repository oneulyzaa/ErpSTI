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

    {{-- Card 1: Total Penjualan → Lap. Penjualan --}}
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm stat-card h-100" style="background:#fff;cursor:pointer"
             onclick="window.location='{{ route('admin.reports.sales') }}'">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(99,102,241,.18);color:#818cf8">
                    <i class="bi bi-cart-check-fill"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark" style="font-size:22px;letter-spacing:-.02em">{{ $stats['total_penjualan_formatted'] }}</div>
                    <div class="text-secondary" style="font-size:12px">Total Penjualan Bulan Ini</div>
                    @if($stats['persentase_perubahan'] > 0)
                        <div class="text-success d-flex align-items-center gap-1 mt-1" style="font-size:11px;font-weight:600">
                            <i class="bi bi-arrow-up-short"></i> +{{ $stats['persentase_perubahan'] }}% dari bulan lalu
                        </div>
                    @elseif($stats['persentase_perubahan'] < 0)
                        <div class="text-danger d-flex align-items-center gap-1 mt-1" style="font-size:11px;font-weight:600">
                            <i class="bi bi-arrow-down-short"></i> {{ $stats['persentase_perubahan'] }}% dari bulan lalu
                        </div>
                    @else
                        <div class="text-secondary d-flex align-items-center gap-1 mt-1" style="font-size:11px;font-weight:600">
                            <i class="bi bi-dash"></i> Sama dengan bulan lalu
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Card 2: Total Klien → Data Klien --}}
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm stat-card h-100" style="background:#fff;cursor:pointer"
             onclick="window.location='{{ route('admin.master-clients.index') }}'">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(34,197,94,.18);color:#4ade80">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark" style="font-size:22px;letter-spacing:-.02em">{{ $stats['total_klien'] }}</div>
                    <div class="text-secondary" style="font-size:12px">Total Klien Aktif</div>
                    <div class="text-success d-flex align-items-center gap-1 mt-1" style="font-size:11px;font-weight:600">
                        <i class="bi bi-arrow-up-short"></i> +{{ $stats['klien_baru'] }} klien baru
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Card 3: Penawaran Pending → Quotation --}}
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm stat-card h-100" style="background:#fff;cursor:pointer"
             onclick="window.location='{{ route('admin.quotations.index') }}'">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(245,158,11,.18);color:#fbbf24">
                    <i class="bi bi-file-earmark-text-fill"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark" style="font-size:22px;letter-spacing:-.02em">{{ $stats['penawaran_pending'] }}</div>
                    <div class="text-secondary" style="font-size:12px">Penawaran Pending</div>
                    <div class="text-warning d-flex align-items-center gap-1 mt-1" style="font-size:11px;font-weight:600">
                        <i class="bi bi-clock-fill"></i> Menunggu approval
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Card 4: Invoice Belum Lunas → Invoice --}}
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm stat-card h-100" style="background:#fff;cursor:pointer"
             onclick="window.location='{{ route('admin.invoices.index') }}'">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(34,211,238,.18);color:#22d3ee">
                    <i class="bi bi-file-earmark-richtext-fill"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark" style="font-size:22px;letter-spacing:-.02em">{{ $stats['invoice_belum_lunas'] }}</div>
                    <div class="text-secondary" style="font-size:12px">Invoice Belum Lunas</div>
                    <div class="text-danger d-flex align-items-center gap-1 mt-1" style="font-size:11px;font-weight:600">
                        <i class="bi bi-arrow-down-short"></i> {{ $stats['invoice_outstanding_formatted'] }} outstanding
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
                            @forelse($penjualanTerbaru as $index => $so)
                            <tr>
                                <td class="ps-3 text-secondary">{{ $index + 1 }}</td>
                                <td><span class="fw-semibold text-primary">#{{ $so->so_number }}</span></td>
                                <td>{{ $so->client_name ?? $so->client?->nama_perusahaan ?? '-' }}</td>
                                <td class="fw-semibold">Rp {{ number_format($so->total, 0, ',', '.') }}</td>
                                <td>
                                    @php
                                        $statusClass = match($so->status) {
                                            'completed', 'selesai' => 'success',
                                            'cancelled', 'batal' => 'danger',
                                            'invoiced' => 'primary',
                                            default => 'warning',
                                        };
                                        $statusLabel = match($so->status) {
                                            'completed', 'selesai' => 'Selesai',
                                            'cancelled', 'batal' => 'Batal',
                                            'invoiced' => 'Invoice',
                                            default => 'Proses',
                                        };
                                        $statusIcon = match($so->status) {
                                            'completed', 'selesai' => 'bi-check-circle-fill',
                                            'cancelled', 'batal' => 'bi-x-circle-fill',
                                            'invoiced' => 'bi-file-earmark-check-fill',
                                            default => 'bi-clock-fill',
                                        };
                                    @endphp
                                    <span class="badge rounded-pill text-bg-{{ $statusClass }}">
                                        <i class="bi {{ $statusIcon }} me-1"></i>{{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="pe-3 text-secondary">{{ $so->date?->format('d M Y') ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-secondary py-4">Belum ada data penjualan</td>
                            </tr>
                            @endforelse
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
    const salesLabels = @json($salesChartData['labels']);
    const salesData = @json($salesChartData['data']);
    
    // Convert data to millions for display
    const dataInMillions = salesData.map(val => parseFloat((val / 1000000).toFixed(2)));
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: salesLabels,
            datasets: [{
                label: 'Penjualan (Juta)',
                data: dataInMillions,
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