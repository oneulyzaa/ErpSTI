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
    
    // ── Produksi ──────────────────────────────────────────────────
    [
        'type'  => 'single',
        'id'    => 'menu-produksi',
        'label' => 'Produksi',
        'icon'  => 'bi bi-gear-fill',
        'route' => 'admin.productions.index',
    ],
    
];
@endphp

@include('layouts.sidebar.layout', ['menuItems' => $menuItems])    