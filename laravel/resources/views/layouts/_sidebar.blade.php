@php
/**
 * Hak akses sidebar per role:
 *   admin    = Staf Penjualan
 *   finance  = Finance / Accounting
 *   gudang   = Produksi / Gudang
 *   direktur = Direktur (read-only semua)
 *
 * Cara kerja:
 *   - 'roles' => null          → semua role boleh lihat menu ini
 *   - 'roles' => ['admin']     → hanya admin yang bisa klik
 *   - Item tanpa akses → tampil dengan icon gembok, tidak bisa diklik
 */

$role = auth()->user()->role ?? 'guest';

$menuItems = [

    // ── Dashboard ──────────────────────────────────────────────────
    [
        'type'  => 'single',
        'id'    => 'menu-dashboard',
        'label' => 'Dashboard',
        'icon'  => 'bi bi-speedometer2',
        'route' => 'admin.dashboard',
        'roles' => null, // semua role
    ],

    // ── Quotation ──────────────────────────────────────────────────
    [
        'type'  => 'single',
        'id'    => 'menu-quotation',
        'label' => 'Quotation',
        'icon'  => 'bi bi-receipt',
        'route' => 'admin.quotations.index',
        'roles' => ['admin', 'direktur'],
    ],

    // ── Sales Order ────────────────────────────────────────────────
    [
        'type'  => 'single',
        'id'    => 'menu-sales-order',
        'label' => 'Sales Order',
        'icon'  => 'bi bi-cart-check-fill',
        'route' => 'admin.sales-orders.index',
        'roles' => ['admin', 'gudang', 'direktur'],
    ],

    // ── Produksi ───────────────────────────────────────────────────
    [
        'type'  => 'single',
        'id'    => 'menu-produksi',
        'label' => 'Produksi',
        'icon'  => 'bi bi-gear-fill',
        'route' => 'admin.productions.index',
        'roles' => ['gudang', 'direktur'],
    ],

    // ── Delivery Order ─────────────────────────────────────────────
    [
        'type'  => 'single',
        'id'    => 'menu-delivery-order',
        'label' => 'Delivery Order',
        'icon'  => 'bi bi-truck',
        'route' => 'admin.delivery-orders.index',
        'roles' => ['admin', 'gudang', 'direktur'],
    ],

    // ── Invoice ────────────────────────────────────────────────────
    [
        'type'  => 'single',
        'id'    => 'menu-invoice',
        'label' => 'Invoice',
        'icon'  => 'bi bi-file-earmark-text-fill',
        'route' => 'admin.invoices.index',
        'roles' => ['admin', 'finance', 'direktur'],
    ],

    // ── Tanda Terima ───────────────────────────────────────────────
    [
        'type'  => 'single',
        'id'    => 'menu-tanda-terima',
        'label' => 'Tanda Terima',
        'icon'  => 'bi bi-journal-check',
        'route' => 'admin.receipts.index',
        'roles' => ['finance', 'direktur'],
    ],

    // ── Laporan ────────────────────────────────────────────────────
    [
        'type'  => 'group',
        'label' => 'Laporan',
    ],
    [
        'type'       => 'submenu',
        'id'         => 'menu-reports',
        'label'      => 'Laporan',
        'icon'       => 'bi bi-bar-chart-line',
        'routes'     => ['admin.reports.*'],
        'submenu_id' => 'submenu-laporan',
        'roles'      => ['admin', 'finance', 'direktur'],
        'children'   => [
            [
                'id'    => 'menu-report-sales',
                'label' => 'Lap. Penjualan',
                'icon'  => 'bi bi-graph-up-arrow',
                'route' => 'admin.reports.sales',
                'roles' => ['admin', 'finance', 'direktur'],
            ],
        ],
    ],

    // ── Master Data ────────────────────────────────────────────────
    [
        'type'  => 'group',
        'label' => 'Master Data',
    ],
    [
        'type'       => 'submenu',
        'id'         => 'menu-master',
        'label'      => 'Master Data',
        'icon'       => 'bi bi-database',
        'routes'     => ['admin.master-clients.*', 'admin.master-assets.*'],
        'submenu_id' => 'submenu-master',
        'roles'      => ['admin', 'finance', 'gudang', 'direktur'],
        'children'   => [
            [
                'id'    => 'menu-clients',
                'label' => 'Data Klien',
                'icon'  => 'bi bi-people',
                'route' => 'admin.master-clients.index',
                'roles' => ['admin', 'finance', 'direktur'],
            ],
            [
                'id'    => 'menu-products',
                'label' => 'Data Material',
                'icon'  => 'bi bi-x-diamond',
                'route' => 'admin.master-assets.index',
                'roles' => ['admin', 'gudang', 'direktur'],
            ],
        ],
    ],

];

/**
 * Helper: cek apakah role user punya akses ke item ini.
 * $allowedRoles = null  → semua boleh
 * $allowedRoles = []    → array role yang diizinkan
 */
$canAccess = fn($allowedRoles) =>
    $allowedRoles === null || in_array($role, $allowedRoles);

@endphp

@foreach ($menuItems as $item)

    {{-- ── JENIS: single ──────────────────────────────────────────── --}}
    @if ($item['type'] === 'single')
        @php $allowed = $canAccess($item['roles'] ?? null); @endphp
        <ul class="nav flex-column gap-1">
            <li class="nav-item">
                @if ($allowed)
                    {{-- Punya akses → link normal --}}
                    <a href="{{ $item['route'] ? route($item['route']) : '#' }}"
                       class="nav-link {{ $item['route'] && Request::routeIs($item['route']) ? 'active' : '' }}"
                       id="{{ $item['id'] }}">
                        <i class="{{ $item['icon'] }}"></i>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @else
                    {{-- Tidak punya akses → tampil gembok, tidak bisa diklik --}}
                    <span class="nav-link nav-locked"
                          id="{{ $item['id'] }}"
                          title="Anda tidak memiliki akses ke menu ini">
                        <i class="{{ $item['icon'] }}"></i>
                        <span>{{ $item['label'] }}</span>
                        <i class="bi bi-lock-fill ms-auto nav-lock-icon"
                           aria-label="Akses terbatas"></i>
                    </span>
                @endif
            </li>
        </ul>

    {{-- ── JENIS: group (label bagian) ───────────────────────────── --}}
    @elseif ($item['type'] === 'group')
        <div class="sidebar-section sidebar-label">{{ $item['label'] }}</div>

    {{-- ── JENIS: submenu (collapsible) ──────────────────────────── --}}
    @elseif ($item['type'] === 'submenu')
        @php
            $submenuAllowed  = $canAccess($item['roles'] ?? null);
            $isOpen          = $submenuAllowed && Request::routeIs(...$item['routes']);
        @endphp
        <ul class="nav flex-column gap-1">
            <li class="nav-item">
                @if ($submenuAllowed)
                    {{-- Parent submenu bisa dibuka --}}
                    <a class="nav-link {{ $isOpen ? '' : 'collapsed' }}"
                       data-bs-toggle="collapse"
                       href="#{{ $item['submenu_id'] }}"
                       role="button"
                       aria-expanded="{{ $isOpen ? 'true' : 'false' }}"
                       id="{{ $item['id'] }}">
                        <i class="{{ $item['icon'] }}"></i>
                        <span>{{ $item['label'] }}</span>
                        <i class="bi bi-chevron-right collapse-arrow"></i>
                    </a>
                    <div class="collapse submenu {{ $isOpen ? 'show' : '' }}"
                         id="{{ $item['submenu_id'] }}">
                        <ul class="nav flex-column gap-1 mt-1">
                            @foreach ($item['children'] as $child)
                                @php $childAllowed = $canAccess($child['roles'] ?? null); @endphp
                                <li>
                                    @if ($childAllowed)
                                        <a href="{{ $child['route'] ? route($child['route']) : '#' }}"
                                           class="nav-link {{ $child['route'] && Request::routeIs($child['route']) ? 'active' : '' }}"
                                           id="{{ $child['id'] }}">
                                            <i class="{{ $child['icon'] }}"></i>
                                            <span>{{ $child['label'] }}</span>
                                        </a>
                                    @else
                                        <span class="nav-link nav-locked"
                                              id="{{ $child['id'] }}"
                                              title="Anda tidak memiliki akses ke menu ini">
                                            <i class="{{ $child['icon'] }}"></i>
                                            <span>{{ $child['label'] }}</span>
                                            <i class="bi bi-lock-fill ms-auto nav-lock-icon"
                                               aria-label="Akses terbatas"></i>
                                        </span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    {{-- Seluruh submenu terkunci --}}
                    <span class="nav-link nav-locked"
                          id="{{ $item['id'] }}"
                          title="Anda tidak memiliki akses ke menu ini">
                        <i class="{{ $item['icon'] }}"></i>
                        <span>{{ $item['label'] }}</span>
                        <i class="bi bi-lock-fill ms-auto nav-lock-icon"
                           aria-label="Akses terbatas"></i>
                    </span>
                @endif
            </li>
        </ul>

    @endif

@endforeach

{{-- Logout (mobile only) --}}
<div class="d-lg-none border-top border-secondary border-opacity-25 mt-3 pt-3">
    <ul class="nav flex-column">
        <li>
            <a href="{{ route('logout') }}" class="nav-link text-danger" id="menu-logout-mobile">
                <i class="bi bi-box-arrow-left"></i>
                <span>Keluar</span>
            </a>
        </li>
    </ul>
</div>

{{-- ── CSS: styling untuk menu yang terkunci ─────────────────────────────── --}}
<style>
.nav-locked {
    /* tampil seperti nav-link tapi tidak bisa diklik */
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.45rem 0.75rem;
    border-radius: 0.375rem;
    cursor: not-allowed;
    opacity: 0.45;
    color: var(--bs-secondary-color, #6c757d);
    user-select: none;
    pointer-events: none;   /* klik diblokir sepenuhnya */
    text-decoration: none;
}

.nav-lock-icon {
    font-size: 0.7rem;
    opacity: 0.8;
    flex-shrink: 0;
}
</style>