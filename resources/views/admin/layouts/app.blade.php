<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $adminSiteSettings = \App\Models\SiteSetting::current();
        $adminFavicon = $adminSiteSettings->favicon ?: 'images/bakeon-favicon.svg';
    @endphp
    <title>@yield('title', 'Admin Dashboard') | {{ $adminSiteSettings->store_name ?? 'Bakeon' }}</title>
    <link rel="icon" href="{{ asset($adminFavicon) }}?v=202609080435" type="{{ \Illuminate\Support\Str::endsWith($adminFavicon, '.svg') ? 'image/svg+xml' : 'image/png' }}">
    <link rel="shortcut icon" href="{{ asset($adminFavicon) }}?v=202609080435">

    <!-- Bootstrap 5 & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- DataTables Bootstrap 5 CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-chocolate: #ff1730;
            --secondary-cream: #fff7f4;
            --accent-gold: #ff1730;
            --text-dark: #2B2B2B;
            --sidebar-width: 250px;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #F8F9FA;
            color: var(--text-dark);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Sidebar Styling */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: var(--primary-chocolate);
            color: #FFFFFF;
            z-index: 1000;
            transition: all 0.3s;
            overflow-y: auto;
            overflow-x: hidden;
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,.3) transparent;
        }

        .sidebar::-webkit-scrollbar { width: 6px; }
        .sidebar::-webkit-scrollbar-track { background: transparent; }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,.26); border-radius: 10px; }

        .sidebar-brand {
            padding: 20px;
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--accent-gold);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-menu {
            padding: 15px 0 35px;
            list-style: none;
            margin: 0;
        }

        .sidebar-menu .nav-link {
            padding: 12px 20px;
            color: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.95rem;
            transition: all 0.2s;
        }

        .sidebar-menu .nav-link:hover, 
        .sidebar-menu .nav-link.active {
            color: #FFFFFF;
            background-color: rgba(212, 163, 115, 0.2);
            border-left: 4px solid var(--accent-gold);
        }

        /* Main Content Styling */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .top-navbar {
            height: 60px;
            background-color: #FFFFFF;
            border-bottom: 1px solid #E2D7CD;
            padding: 0 25px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }

        .content-body {
            padding: 25px;
            flex: 1;
        }
        .product-image-thumb-wrap {
    width: 140px;
    height: 140px;
    position: relative;
    border: 1px solid #eee;
    border-radius: 8px;
    overflow: hidden;
    display: inline-block;
}
.product-image-thumb-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.product-image-thumb-wrap .remove-image-btn {
    position: absolute;
    top: 4px;
    right: 4px;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    padding: 0;
    line-height: 1;
    font-size: 0.75rem;
}
.sidebar-menu .nav-link {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 16px;
    color: #ddd;
    cursor: pointer;
}
.sidebar-menu .nav-link:hover,
.sidebar-menu .nav-link.active {
    background: #3a2f2f;
    color: #fff;
}
.sidebar-menu .submenu {
    margin: 4px 12px 8px;
    padding: 6px 0;
    border: 1px solid #efdcd6;
    border-radius: 10px;
    background: #fff !important;
}
.sidebar-menu .submenu-link {
    margin: 2px 6px !important;
    padding: 9px 12px 9px 38px !important;
    border-radius: 7px !important;
    color: #765f59 !important;
    font-size: 0.82rem;
}
.sidebar-menu .submenu-link:hover { background:#fff0ec !important;color:#e8172d !important; }
.sidebar-menu .submenu-link.active { background:#ffe6e1 !important;color:#e8172d !important;box-shadow:none !important; }
.sidebar-menu .nav-link[data-bs-toggle="collapse"] .fa-chevron-down {
    transition: transform 0.2s;
}
.sidebar-menu .nav-link[aria-expanded="true"] .fa-chevron-down {
    transform: rotate(180deg);
}
.sidebar-section-label {
    padding: 18px 20px 7px;
    color: rgba(255,255,255,.44);
    font-size: .68rem;
    font-weight: 700;
    letter-spacing: 1.5px;
    text-transform: uppercase;
}
.sidebar {
    background: linear-gradient(180deg, #fffaf8 0%, #fff3ef 100%) !important;
    color: #3c2926 !important;
    border-right: 1px solid #eedbd5;
    box-shadow: 7px 0 24px rgba(73,39,31,.06);
}
.admin-sidebar-brand {
    position: sticky;
    z-index: 3;
    top: 0;
    display: flex;
    min-height: 78px;
    align-items: center;
    gap: 12px;
    padding: 16px 20px;
    border-bottom: 1px solid #eedbd5;
    background: rgba(255,250,248,.96);
    backdrop-filter: blur(8px);
}
.admin-sidebar-brand img { display: block; max-width: 155px; max-height: 46px; object-fit: contain; }
.admin-sidebar-brand .brand-fallback { color: #34211e; font-size: 1.1rem; font-weight: 700; }
.admin-sidebar-brand .brand-fallback i { margin-right: 8px; color: #ff1730; }
.admin-sidebar-brand .admin-bakeon-wordmark { color:#ff1730;font-family:Georgia,'Times New Roman',serif;font-size:2rem;font-style:italic;font-weight:700;line-height:1; }
.sidebar-menu .nav-link { margin: 3px 12px; padding: 11px 13px; border-radius: 9px; color: #624e49 !important; }
.sidebar-menu .nav-link i { width: 20px; color: #b28d84; text-align: center; }
.sidebar-menu .nav-link:hover { border-left: 0; background: #ffe9e5 !important; color: #e8172d !important; }
.sidebar-menu .nav-link:hover i { color: #e8172d; }
.sidebar-menu .nav-link.active { border-left: 0; background: #ff1730 !important; color: #fff !important; box-shadow: 0 7px 16px rgba(232,23,45,.18); }
.sidebar-menu .nav-link.active i { color: #fff; }
.sidebar-section-label { color: #aa8c85; }
.sidebar::-webkit-scrollbar-thumb { background: #e6c7c0; }
.top-navbar { box-shadow: 0 2px 14px rgba(65,36,29,.05); }
.admin-sidebar-brand { height: 78px; min-height: 78px; box-sizing: border-box; }
.top-navbar { height: 78px !important; min-height: 78px; box-sizing: border-box; padding: 0 28px !important; justify-content: flex-end !important; }
.sidebar,.main-wrapper{transition:transform .28s ease,margin-left .28s ease,width .28s ease}
.sidebar-toggle{position:fixed;z-index:1002;top:57px;left:calc(var(--sidebar-width) - 18px);display:grid;width:36px;height:36px;place-items:center;border:1px solid #efd3cc;border-radius:50%;background:#fff;color:#ff1730;font-size:13px;box-shadow:0 5px 16px rgba(65,36,29,.14);transition:left .28s ease,background .2s ease,color .2s ease}.sidebar-toggle:hover{border-color:#ff1730;background:#ff1730;color:#fff}.sidebar-toggle i{transition:transform .28s ease}
body.sidebar-closed .sidebar{transform:translateX(-100%)}
body.sidebar-closed .main-wrapper{margin-left:0}
body.sidebar-closed .sidebar-toggle{left:18px}
body.sidebar-closed .sidebar-toggle i{transform:rotate(180deg)}
@media(max-width:768px){.sidebar{box-shadow:12px 0 30px rgba(50,25,20,.14)}.main-wrapper{margin-left:0!important}.sidebar{transform:translateX(-100%)}.sidebar-toggle{left:18px}body.sidebar-mobile-open .sidebar{transform:translateX(0)}body.sidebar-mobile-open .sidebar-toggle{left:calc(var(--sidebar-width) - 18px)}body.sidebar-mobile-open::after{position:fixed;z-index:999;inset:0;background:rgba(42,24,20,.28);content:''}}
    </style>
</head>
<body>

    <div class="sidebar">
        <a href="{{ route('admin.dashboard') }}" class="admin-sidebar-brand text-decoration-none">
            @if($adminSiteSettings->logo)
                <img src="{{ asset($adminSiteSettings->logo) }}" alt="{{ $adminSiteSettings->store_name ?? 'Bakeon' }}">
            @else
                <span class="brand-fallback admin-bakeon-wordmark">bakeon</span>
            @endif
        </a>

        <ul class="sidebar-menu list-unstyled mb-0">

            <li>
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-line"></i> Dashboard
                </a>
            </li>

            <li class="sidebar-section-label">Catalog</li>
            <li><a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}"><i class="fa-solid fa-box-open"></i> Products</a></li>
            <li><a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}"><i class="fa-solid fa-layer-group"></i> Categories</a></li>
            <li><a href="{{ route('admin.flavors.index') }}" class="nav-link {{ request()->routeIs('admin.flavors.*') ? 'active' : '' }}"><i class="fa-solid fa-ice-cream"></i> Flavors</a></li>
            <li><a href="{{ route('admin.weights.index') }}" class="nav-link {{ request()->routeIs('admin.weights.*') ? 'active' : '' }}"><i class="fa-solid fa-weight-scale"></i> Weights</a></li>
            <li><a href="{{ route('admin.addons.index') }}" class="nav-link {{ request()->routeIs('admin.addons.*') ? 'active' : '' }}"><i class="fa-solid fa-puzzle-piece"></i> Addons</a></li>
            <li><a href="{{ route('admin.delivery-options.index') }}" class="nav-link {{ request()->routeIs('admin.delivery-options.*') ? 'active' : '' }}"><i class="fa-solid fa-truck-fast"></i> Delivery Options</a></li>
            <li><a href="{{ route('admin.occasions.index') }}" class="nav-link {{ request()->routeIs('admin.occasions.*') ? 'active' : '' }}"><i class="fa-solid fa-gift"></i> Occasions</a></li>

            <li>
                <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-receipt"></i> Orders
                </a>
            </li>

            <li>
                <a href="{{ route('admin.pincodes.index') }}" class="nav-link {{ request()->routeIs('admin.pincodes.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-location-dot"></i> Serviceable Pincodes
                </a>
            </li>

            <li>
                <a href="{{ route('admin.coupons.index') }}" class="nav-link {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-tag"></i> Coupons
                </a>
            </li>

            <li>
                <a href="{{ route('admin.banners.index') }}" class="nav-link {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-images"></i> Banners
                </a>
            </li>

            <li>
                <a href="{{ route('admin.settings.edit') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-gear"></i> Site Settings
                </a>
            </li>

        </ul>
    </div>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <!-- Navbar -->
        <nav class="top-navbar">
            <button type="button" class="sidebar-toggle" id="sidebarToggle" aria-label="Open or close sidebar" aria-expanded="true"><i class="fa-solid fa-angles-left"></i></button>
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle btn-sm" type="button" data-bs-toggle="dropdown">
                    <i class="fa-solid fa-user me-1"></i> {{ auth()->user()->name ?? 'Admin' }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="fa-solid fa-right-from-bracket me-2"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Dynamic Content -->
        <main class="content-body">
            <!-- @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif -->

            @yield('content')
        </main>
    </div>
    <!-- Toast Notification -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 2000;">
        <div id="appToast" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="appToastBody">
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close">
                </button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        function showToast(message, type = 'success') {
            const toastEl = document.getElementById('appToast');
            const toastBody = document.getElementById('appToastBody');

            toastEl.className = 'toast align-items-center text-white border-0 bg-' + (type === 'danger' ? 'danger' : 'success');
            toastBody.textContent = message;

            const toast = new bootstrap.Toast(toastEl, { delay: 4000 });
            toast.show();
        }

        // Agar Laravel session me success/error message hai to page load pe turant toast dikhao
        @if (session('success'))
            document.addEventListener('DOMContentLoaded', () => showToast(@json(session('success')), 'success'));
        @endif
        @if (session('error'))
            document.addEventListener('DOMContentLoaded', () => showToast(@json(session('error')), 'danger'));
        @endif

        const sidebarToggle = document.getElementById('sidebarToggle');
        if (sidebarToggle) {
            const desktopClosed = localStorage.getItem('adminSidebarClosed') === '1';
            if (window.innerWidth > 768 && desktopClosed) {
                document.body.classList.add('sidebar-closed');
                sidebarToggle.setAttribute('aria-expanded', 'false');
            }
            sidebarToggle.addEventListener('click', function () {
                if (window.innerWidth <= 768) {
                    const open = document.body.classList.toggle('sidebar-mobile-open');
                    sidebarToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
                } else {
                    const closed = document.body.classList.toggle('sidebar-closed');
                    localStorage.setItem('adminSidebarClosed', closed ? '1' : '0');
                    sidebarToggle.setAttribute('aria-expanded', closed ? 'false' : 'true');
                }
            });
            document.addEventListener('click', function (event) {
                if (window.innerWidth <= 768 && document.body.classList.contains('sidebar-mobile-open') && !event.target.closest('.sidebar') && !event.target.closest('#sidebarToggle')) {
                    document.body.classList.remove('sidebar-mobile-open');
                    sidebarToggle.setAttribute('aria-expanded', 'false');
                }
            });
        }
    </script>
    @stack('scripts')
</body>
</html>
