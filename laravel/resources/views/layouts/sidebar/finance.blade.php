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
    
    // ── Invoice ───────────────────────────────────────────────────
    [
        'type'  => 'single',
        'id'    => 'menu-invoice',
        'label' => 'Invoice',
        'icon'  => 'bi bi-file-earmark-text-fill',
        'route' => 'admin.invoices.index',
    ],
    // -- Tanda Terima
    [
        'type'  => 'single',
        'id'    => 'menu-tanda-terima',
        'label' => 'Tanda Terima',
        'icon'  => 'bi bi-journal-check',
        'route' => 'admin.receipts.index',
    ],
    
    
    

  
    


];
@endphp
@include('layouts.sidebar.layout', ['menuItems' => $menuItems])    