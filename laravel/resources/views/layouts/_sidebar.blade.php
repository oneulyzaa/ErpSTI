{{--
    Sidebar Dinamis
    ───────────────
    Template ini me-render array $menuItems secara otomatis.

    Cara pakai di controller / view composer:
        $menuItems = require config_path('sidebar_menu.php');
        // atau simpan di config/sidebar_menu.php dan panggil:
        $menuItems = config('sidebar_menu');

    Lalu pass ke view:
        return view('layouts.app', compact('menuItems'));

    Atau di AppServiceProvider / ViewServiceProvider:
        View::composer('partials._sidebar', function ($view) {
            $view->with('menuItems', config('sidebar_menu'));
        });
--}}
@php
$menuItems = [

    // ── Dashboard ──────────────────────────────────────────────────
    [
        'type'  => 'single',
        'id'    => 'menu-dashboard',
        'label' => 'Dashboard',
        'icon'  => 'bi bi-speedometer2',
        'href'  => '/admin/dashboard',
        'route' => 'admin.dashboard',
    ],
    // ── Quotation ──────────────────────────────────────────────────
    [
        'type'  => 'single',
        'id'    => 'menu-quotation',
        'label' => 'Quotation',
        'icon'  => 'bi bi-receipt',
        'href'  => '/admin/quotations',
        'route' => 'admin/quotations',
    ],
    // ── Sales Order ──────────────────────────────────────────────────
    [
        'type'  => 'single',
        'id'    => 'menu-sales-order',
        'label' => 'Sales Order',
        'icon'  => 'bi bi-cart-check-fill',
        'href'  => '/sales-order',
        'route' => 'sales-order',
    ],
    // ── Produksi ──────────────────────────────────────────────────
    [
        'type'  => 'single',
        'id'    => 'menu-produksi',
        'label' => 'Produksi',
        'icon'  => 'bi bi-gear-fill',
        'href'  => '/produksi',
        'route' => 'produksi',
    ],
    // ── Delivery Order
    [
        'type'  => 'single',
        'id'    => 'menu-delivery-order',
        'label' => 'Delivery Order',
        'icon'  => 'bi bi-truck',
        'href'  => '/delivery-order',
        'route' => 'delivery-order',
    ],
    // ── Invoice
    [
        'type'  => 'single',
        'id'    => 'menu-invoice',
        'label' => 'Invoice',
        'icon'  => 'bi bi-file-earmark-text-fill',
        'href'  => '/invoice',
        'route' => 'invoice',
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
        'routes'     => ['reports*'],
        'submenu_id' => 'submenu-laporan',
        'children'   => [
            [
                'id'    => 'menu-report-sales',
                'label' => 'Lap. Penjualan',
                'icon'  => 'bi bi-graph-up-arrow',
                'href'  => '/reports/sales',
                'route' => 'reports/sales',
            ],
            [
                'id'    => 'menu-report-products',
                'label' => 'Lap. Produk',
                'icon'  => 'bi bi-box-arrow-up-right',
                'href'  => '/reports/products',
                'route' => 'reports/products',
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
        'routes'     => ['admin.master-clients*', 'admin.master-assets*'],
        'submenu_id' => 'submenu-master',
        'children'   => [
            [
                'id'    => 'menu-clients',
                'label' => 'Data Klien',
                'icon'  => 'bi bi-people',
                'href'  => '/admin/master-clients',
                'route' => 'admin.master-clients*',
            ],
            [
                'id'    => 'menu-products',
                'label' => 'Data Aset',
                'icon'  => 'bi bi-x-diamond',
                'href'  => '/admin/master-assets',
                'route' => 'admin.master-assets*',
            ],
        ],
    ],

    // // ── Transaksi ──────────────────────────────────────────────────
    // [
    //     'type'  => 'group',
    //     'label' => 'Transaksi',
    // ],
    // [
    //     'type'       => 'submenu',
    //     'id'         => 'menu-transactions',
    //     'label'      => 'Transaksi',
    //     'icon'       => 'bi bi-receipt',
    //     'routes'     => ['quotations*', 'sales*', 'delivery-orders*', 'invoices*'],
    //     'submenu_id' => 'submenu-transaksi',
    //     'children'   => [
    //         [
    //             'id'    => 'menu-quotations',
    //             'label' => 'Penawaran',
    //             'icon'  => 'bi bi-file-earmark-text',
    //             'href'  => 'admin/quotations',
    //             'route' => 'admin/quotations*',
    //         ],
    //         [
    //             'id'    => 'menu-sales',
    //             'label' => 'Penjualan',
    //             'icon'  => 'bi bi-cart-check',
    //             'href'  => '/sales',
    //             'route' => 'sales*',
    //         ],
    //         [
    //             'id'    => 'menu-delivery',
    //             'label' => 'Surat Jalan',
    //             'icon'  => 'bi bi-truck',
    //             'href'  => '/delivery-orders',
    //             'route' => 'delivery-orders*',
    //         ],
    //         [
    //             'id'    => 'menu-invoices',
    //             'label' => 'Invoice',
    //             'icon'  => 'bi bi-file-earmark-richtext',
    //             'href'  => '/invoices',
    //             'route' => 'invoices*',
    //         ],
    //     ],
    // ],

    

];
@endphp
@foreach ($menuItems as $item)

    {{-- ── JENIS: single ──────────────────────────────────────────── --}}
    @if ($item['type'] === 'single')
        <ul class="nav flex-column gap-1">
            <li class="nav-item">
                <a href="{{ url($item['href']) }}"
                   class="nav-link {{ Request::is($item['route']) ? 'active' : '' }}"
                   id="{{ $item['id'] }}">
                    <i class="{{ $item['icon'] }}"></i>
                    <span>{{ $item['label'] }}</span>
                </a>
            </li>
        </ul>

    {{-- ── JENIS: group (label bagian) ───────────────────────────── --}}
    @elseif ($item['type'] === 'group')
        <div class="sidebar-section sidebar-label">{{ $item['label'] }}</div>

    {{-- ── JENIS: submenu (collapsible) ──────────────────────────── --}}
    @elseif ($item['type'] === 'submenu')
        @php
            $isOpen = Request::is(...$item['routes']);
        @endphp
        <ul class="nav flex-column gap-1">
            <li class="nav-item">
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
                            <li>
                                <a href="{{ url($child['href']) }}"
                                   class="nav-link {{ Request::is($child['route']) ? 'active' : '' }}"
                                   id="{{ $child['id'] }}">
                                    <i class="{{ $child['icon'] }}"></i>
                                    <span>{{ $child['label'] }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </li>
        </ul>

    @endif

@endforeach

{{-- Logout (mobile only, desktop ada di footer sidebar) --}}
<div class="d-lg-none border-top border-secondary border-opacity-25 mt-3 pt-3">
    <ul class="nav flex-column">
        <li>
            <a href="{{ url('/logout') }}" class="nav-link text-danger" id="menu-logout-mobile">
                <i class="bi bi-box-arrow-left"></i>
                <span>Keluar</span>
            </a>
        </li>
    </ul>
</div>