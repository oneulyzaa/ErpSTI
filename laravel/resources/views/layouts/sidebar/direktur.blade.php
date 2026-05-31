@php

$menuItems = [

    // ── Dashboard ──────────────────────────────────────────────────
    [
        'type'  => 'single',
        'id'    => 'menu-dashboard',
        'label' => 'Dashboard',
        'icon'  => 'bi bi-speedometer2',
        'route' => 'admin.dashboard',
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
        'children'   => [
            [
                'id'    => 'menu-report-sales',
                'label' => 'Lap. Penjualan',
                'icon'  => 'bi bi-graph-up-arrow',
                'route' => 'admin.reports.sales',
            ],
            /*
            [
                'id'    => 'menu-report-products',
                'label' => 'Lap. Produk',
                'icon'  => 'bi bi-box-arrow-up-right',
                'route' => null,              // TODO: daftarkan route
            ],
            */
        ],
    ],

   
];
@endphp

@include('layouts.sidebar.layout', ['menuItems' => $menuItems])    

