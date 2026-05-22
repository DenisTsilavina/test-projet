<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tsenan'i Vohitsoa – @yield('title', 'Gestion')</title>

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #4f46e5;
            --sidebar-bg: #1e293b;
            --sidebar-color: #f8fafc;
            --content-bg: #f1f5f9;
            --topbar-height: 64px;
            --sidebar-width: 260px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--content-bg);
            margin: 0;
            overflow-x: hidden;
        }

        /* --- TOPBAR --- */
        .topbar {
            height: var(--topbar-height);
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        }

        .topbar-title {
            font-weight: 700;
            font-size: 1.15rem;
            color: var(--sidebar-bg);
            margin-bottom: 0;
        }

        /* --- SIDEBAR --- */
        #sidebar {
            width: var(--sidebar-width);
            background-color: var(--sidebar-bg);
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1050; /* Augmenté pour passer devant la topbar sur mobile */
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            padding: 0;
        }

        .sidebar-header {
            height: var(--topbar-height);
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            background: rgba(0,0,0,0.1);
        }

        .logo-text {
            color: #fff;
            font-weight: 800;
            font-size: 1.4rem;
            letter-spacing: -0.5px;
            margin-bottom: 0;
        }

        .sidebar-content {
            flex-grow: 1;
            padding: 1.5rem 0.8rem;
            overflow-y: auto;
        }

        /* --- NAVIGATION --- */
        .nav-link {
            color: #94a3b8 !important;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0.75rem 1rem !important;
            border-radius: 8px;
            font-weight: 500;
            transition: var(--transition);
            margin-bottom: 4px;
            text-decoration: none;
        }

        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.05);
            color: #fff !important;
        }

        .nav-link.active {
            background-color: var(--primary-color) !important;
            color: #fff !important;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
        }

        /* Sous-menus */
        .collapse-menu {
            background: rgba(0,0,0,0.15);
            margin: 0 0.5rem 0.5rem 0.5rem;
            border-radius: 8px;
        }

        .collapse-menu .nav-link {
            font-size: 0.85rem;
            padding-left: 2.8rem !important;
        }

        .transition-chevron {
            font-size: 0.8rem;
            transition: transform 0.3s;
        }

        [aria-expanded="true"] .transition-chevron {
            transform: rotate(180deg);
        }

        /* --- MAIN CONTENT --- */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            margin-top: var(--topbar-height);
            padding: 2rem;
            min-height: calc(100vh - var(--topbar-height));
            transition: var(--transition);
        }

        /* --- UI COMPONENTS --- */
        .avatar {
            width: 38px;
            height: 38px;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            font-weight: 600;
        }

        /* Overlay pour mobile */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1040;
        }

        /* --- RESPONSIVE --- */
        @media (max-width: 992px) {
            #sidebar {
                left: -100%;
            }
            .main-wrapper {
                margin-left: 0;
                padding: 1.5rem;
            }
            #sidebar.show {
                left: 0;
            }
            #sidebar.show + .sidebar-overlay {
                display: block;
            }
        }
    </style>
    @stack('styles')
</head>
<body>

<!-- Overlay (cliquable pour fermer sur mobile) -->
<div class="sidebar-overlay" onclick="toggleSidebar()"></div>

<!-- Sidebar -->
<aside id="sidebar">
    <div class="sidebar-header">
        <div class="d-flex align-items-center gap-2">
            <img src="{{ asset('images/logo.png') }}" alt="" style="height: 28px;">
            <span class="logo-text text-white">Vohitsoa</span>
        </div>
    </div>

    <div class="sidebar-content">
        @auth
            @if(auth()->user()->user_type === 'admin')
                <a href="{{ route('admin.vente.dashboard') }}"
                   class="nav-link {{ request()->routeIs('admin.vente.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid"></i> Accueil
                </a>

                <!-- Ventes -->
                @php $venteOpen = request()->routeIs('admin.vente.*'); @endphp
                <div class="mb-1">
                    <a class="nav-link justify-content-between {{ $venteOpen ? 'active' : '' }}"
                       data-bs-toggle="collapse" href="#groupVente" role="button" aria-expanded="{{ $venteOpen ? 'true' : 'false' }}">
                        <span class="d-flex align-items-center gap-2">
                            <i class="bi bi-cart3"></i> Ventes
                        </span>
                        <i class="bi bi-chevron-down transition-chevron"></i>
                    </a>
                    <div class="collapse collapse-menu {{ $venteOpen ? 'show' : '' }}" id="groupVente">
                        <a href="{{ route('admin.vente.create') }}" class="nav-link {{ request()->routeIs('admin.vente.create') ? 'active' : '' }}">Nouvelle vente</a>
                        <a href="{{ route('admin.vente.index') }}" class="nav-link {{ request()->routeIs('admin.vente.index') ? 'active' : '' }}">Historique</a>
                    </div>
                </div>

                <!-- Stocks -->
                @php $stockOpen = request()->routeIs('stock.*'); @endphp
                <div class="mb-1">
                    <a class="nav-link justify-content-between {{ $stockOpen ? 'active' : '' }}"
                       data-bs-toggle="collapse" href="#groupStock" role="button" aria-expanded="{{ $stockOpen ? 'true' : 'false' }}">
                        <span class="d-flex align-items-center gap-2">
                            <i class="bi bi-box-seam"></i> Stocks
                        </span>
                        <i class="bi bi-chevron-down transition-chevron"></i>
                    </a>
                    <div class="collapse collapse-menu {{ $stockOpen ? 'show' : '' }}" id="groupStock">
                        <a href="{{ route('stock.index') }}" class="nav-link {{ request()->routeIs('stock.index') ? 'active' : '' }}">Inventaire</a>
                        <a href="{{ route('stock.create') }}" class="nav-link {{ request()->routeIs('stock.create') ? 'active' : '' }}">Entrée stock</a>
                    </div>
                </div>

                <a href="{{ route('admin.article.create') }}">
                    <i class="bi bi-bag-check"></i> Nouvelle article
                </a>
            @else
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-house"></i> Accueil
                </a>
            @endif
        @endauth
    </div>
</aside>

<!-- Topbar -->
<header class="topbar">
    <div class="d-flex align-items-center gap-3">
        <button class="btn d-lg-none" onclick="toggleSidebar()">
            <i class="bi bi-list fs-4"></i>
        </button>
        <span class="topbar-title">@yield('title', 'Tableau de bord')</span>
    </div>

    <div class="dropdown">
        @auth
            <div class="dropdown">
                <div class="d-flex align-items-center gap-3" style="cursor: pointer;" data-bs-toggle="dropdown">
                    <div class="d-none d-md-block text-end">
                        <div class="fw-bold mb-0" style="line-height: 1.2; font-size: 0.9rem;">
                            {{ auth()->user()->name }}
                        </div>
                        <small class="text-muted" style="font-size: 0.75rem;">
                            {{ auth()->user()->user_type === 'admin' ? 'Administrateur' : 'Client' }}
                        </small>
                    </div>
                    <div class="avatar shadow-sm">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                </div>

                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-3 p-2" style="border-radius: 12px; min-width: 200px;">
                    <li><a class="dropdown-item py-2" href="#"><i class="bi bi-person me-2"></i> Profil</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger py-2">
                                <i class="bi bi-box-arrow-right me-2"></i> Déconnexion
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        @endauth
    </div>
</header>

<!-- Main Wrapper -->
<main class="main-wrapper">
    @if(session('success'))
        <div class="alert alert-success border-start border-success border-4 shadow-sm mb-4">
            <i class="bi bi-check-circle-fill me-2"></i> {!! session('success') !!}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger border-start border-danger border-4 shadow-sm mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('show');
    }
</script>
@stack('scripts')
</body>
</html>
