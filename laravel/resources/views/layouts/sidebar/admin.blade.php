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
    // ── Quotation ──────────────────────────────────────────────────
    [
        'type'  => 'single',
        'id'    => 'menu-quotation',
        'label' => 'Quotation',
        'icon'  => 'bi bi-receipt',

        'route' => 'admin.quotations.index',        // TODO: daftarkan route

    ],
    // ── Sales Order ──────────────────────────────────────────────────
    [
        'type'  => 'single',
        'id'    => 'menu-sales-order',
        'label' => 'Sales Order',
        'icon'  => 'bi bi-cart-check-fill',
        'route' => 'admin.sales-orders.index',
    ],
    // ── Produksi ──────────────────────────────────────────────────
    [
        'type'  => 'single',
        'id'    => 'menu-produksi',
        'label' => 'Produksi',
        'icon'  => 'bi bi-gear-fill',
        'route' => 'admin.productions.index',
    ],
    // ── Delivery Order ────────────────────────────────────────────
    [
        'type'  => 'single',
        'id'    => 'menu-delivery-order',
        'label' => 'Delivery Order',
        'icon'  => 'bi bi-truck',
        'route' => 'admin.delivery-orders.index',
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
        'children'   => [
            [
                'id'    => 'menu-clients',
                'label' => 'Data Klien',
                'icon'  => 'bi bi-people',
                'route' => 'admin.master-clients.index',
            ],
            [
                'id'    => 'menu-products',
                'label' => 'Data Material',
                'icon'  => 'bi bi-x-diamond',
                'route' => 'admin.master-assets.index',
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
@include('layouts.sidebar.layout', ['menuItems' => $menuItems])    