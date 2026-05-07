<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Aplikasi Penjualan - Sistem Manajemen Penjualan">
    <title>@yield('title', 'Dashboard') — Aplikasi Penjualan</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f1f5f9; }

        /* ── Sidebar ── */
        #sidebar {
            width: 260px;
            min-height: 100vh;
            background-color: #ffffff;
            border-right: 1px solid rgba(0,0,0,.08);
            flex-shrink: 0;
            transition: width .22s ease;
            overflow: hidden;
        }
        #sidebar.collapsed { width: 68px; }
        #sidebar.collapsed .sidebar-label,
        #sidebar.collapsed .brand-text,
        #sidebar.collapsed .collapse-arrow { display: none !important; }
        #sidebar.collapsed .nav-link span { display: none; }
        #sidebar.collapsed .nav-link { justify-content: center; }
        #sidebar.collapsed .submenu { display: none !important; }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 18px 16px;
            border-bottom: 1px solid rgba(0,0,0,.08);
            text-decoration: none;
            min-height: 64px;
        }
        .brand-icon {
            width: 36px; height: 36px;
            background: linear-gradient(135deg,#6366f1,#22d3ee);
            border-radius: 10px;
            display: grid; place-items: center;
            font-size: 18px; color: #fff;
            flex-shrink: 0;
        }

        .sidebar-section {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #31373f;
            padding: 16px 16px 6px;
        }

        #sidebar .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 8px;
            color: #31373f;
            font-size: 14px;
            font-weight: 500;
            transition: all .2s;
            white-space: nowrap;
        }
        #sidebar .nav-link i { font-size: 17px; flex-shrink: 0; }
        #sidebar .nav-link:hover { background: #f1f5f9; color: #1e293b; }
        #sidebar .nav-link.active {
            background: #1B5DBC;
            color: #cfcfcf;
        }

        /* submenu indent */
        .submenu .nav-link { padding-left: 20px; font-size: 13px; }

        /* parent toggle arrow */
        .collapse-arrow { transition: transform .25s ease; font-size: 11px; margin-left: auto; }
        .nav-link[aria-expanded="true"] .collapse-arrow { transform: rotate(90deg); }

        /* ── Topbar ── */
        #topbar {
            height: 64px;
            background-color: #fff;
            border-bottom: 1px solid rgba(0,0,0,.08);
        }

        /* ── Main ── */
        #main-content { flex: 1; min-width: 0; }

        /* ── Offcanvas sidebar for mobile ── */
        #mobileSidebar { background-color: #fff; width: 260px; }
        #mobileSidebar .nav-link {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 12px; border-radius: 8px;
            color: #fff; font-size: 14px; font-weight: 500;
            transition: all .2s;
        }
        #mobileSidebar .nav-link i { font-size: 17px; flex-shrink: 0; }
        #mobileSidebar .nav-link:hover { background: #f1f5f9; color: #1e293b; }
        #mobileSidebar .nav-link.active { background: #1B5DBC; color: #cfcfcf; }
        #mobileSidebar .submenu .nav-link { padding-left: 20px; font-size: 13px; }
        #mobileSidebar .sidebar-brand { display: flex; align-items: center; gap: 12px; padding: 18px 16px; border-bottom: 1px solid rgba(0,0,0,.08); text-decoration: none; }
        #mobileSidebar .sidebar-section { font-size: 10px; font-weight: 600; letter-spacing: .08em; text-transform: uppercase; color: #31373f; padding: 16px 16px 6px; }

        /* user chip */
        .user-chip {
            background: #f1f5f9;
            border: 1px solid rgba(0,0,0,.1);
            border-radius: 40px;
            padding: 5px 14px 5px 6px;
            cursor: pointer;
            transition: all .2s;
        }
        .user-chip:hover { border-color: #6366f1; }
        .user-avatar {
            width: 30px; height: 30px; border-radius: 50%;
            background: linear-gradient(135deg,#6366f1,#22d3ee);
            display: grid; place-items: center;
            font-weight: 700; font-size: 13px; color: #fff;
        }
        table>tbody>tr>td {
            vertical-align: middle; 
            font-size:14px
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- ═══════════════════════════════════════
     MOBILE OFFCANVAS SIDEBAR
═══════════════════════════════════════ --}}
<div class="offcanvas offcanvas-start" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
    <div class="offcanvas-header p-0 border-0">
        <a href="{{ url('/dashboard') }}" class="sidebar-brand w-100">
            <div class="brand-icon">
                <img src="/assets/gambar/logo-sti.png" alt="" width="50">
            </div>
            <div>
                <div class="fw-bold text-dark" style="font-size:15px">{{ env('APP_NAME') }}</div>
            </div>
        </a>
        <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-2">
        @include('layouts._sidebar')
    </div>
</div>

{{-- ═══════════════════════════════════════
     MAIN WRAPPER
═══════════════════════════════════════ --}}
<div class="d-flex" style="min-height:100vh">

    {{-- Desktop Sidebar --}}
    <div id="sidebar" class="d-none d-lg-flex flex-column">
        <a href="{{ url('/dashboard') }}" class="sidebar-brand">
            <div class="brand-icon">
                <img src="/assets/gambar/logo-sti.png" alt="" width="100%">
            </div>
            <div class="brand-text">
                <div class="fw-bold text-dark" style="font-size:15px">{{ env('APP_NAME') }}</div>
                {{-- <div class=" text-white" style="font-size:11px">{{ env('APP_DESCRIPTION' ,'Sistem Manajemen Penjualan') }}</div> --}}
            </div>
        </a>

        <div class="flex-grow-1 overflow-y-auto py-2 px-2">
            @include('layouts._sidebar')
        </div>

        <div class="p-2 border-top border-secondary border-opacity-25">
            <a href="{{ url('/logout') }}" class="nav-link text-danger" id="menu-logout">
                <i class="bi bi-box-arrow-left"></i>
                <span>Keluar</span>
            </a>
        </div>
    </div>

    {{-- Right Side --}}
    <div id="main-content" class="d-flex flex-column">

        {{-- Topbar --}}
        <nav id="topbar" class="d-flex align-items-center px-3 px-lg-4 gap-3">
            {{-- Mobile toggle --}}
            <button class="btn btn-outline-secondary btn-sm d-lg-none"
                    data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
                <i class="bi bi-list fs-5"></i>
            </button>

            {{-- Desktop collapse toggle --}}
            <button class="btn btn-outline-secondary btn-sm d-none d-lg-inline-flex"
                    id="sidebarToggle" aria-label="Toggle sidebar">
                <i class="bi bi-list fs-5"></i>
            </button>

            {{-- Breadcrumb --}}
            <nav aria-label="breadcrumb" class="d-none d-sm-block">
                <ol class="breadcrumb mb-0" style="font-size:13px">
                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}" class="text-decoration-none"><i class="bi bi-house"></i></a></li>
                    <li class="breadcrumb-item active">@yield('breadcrumb', 'Dashboard')</li>
                </ol>
            </nav>

            <div class="ms-auto d-flex align-items-center gap-2">
                <button class="btn btn-outline-secondary btn-sm position-relative d-none" aria-label="Notifikasi">
                    <i class="bi bi-bell fs-6"></i>
                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-primary border border-dark rounded-circle" style="width:8px;height:8px"></span>
                </button>
                <div class="user-chip d-flex align-items-center gap-2">
                    <div class="user-avatar">A</div>
                    <span class="text-dark fw-500 d-none d-sm-block" style="font-size:13px">Admin</span>
                    <i class="bi bi-chevron-down text-muted d-none d-sm-block" style="font-size:11px"></i>
                </div>
            </div>
        </nav>

        {{-- Page Content --}}
        <main class="p-3 p-lg-4 flex-grow-1">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible d-flex align-items-center gap-2 fade show" role="alert">
                <i class="bi bi-check-circle-fill"></i>
                <span>{{ session('success') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible d-flex align-items-center gap-2 fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <span>{{ session('error') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Desktop sidebar collapse
    const sidebar = document.getElementById('sidebar');
    document.getElementById('sidebarToggle')?.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
        localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
    });
    if (localStorage.getItem('sidebarCollapsed') === 'true') {
        sidebar?.classList.add('collapsed');
    }
</script>
@stack('scripts')
</body>
</html>
