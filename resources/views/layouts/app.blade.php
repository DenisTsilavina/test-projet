<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Denis-app – @yield('title', 'Gestion')</title>

    {{-- Bootstrap 5 & Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    {{-- Google Fonts: Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #10b981;
            --primary-light: #ecfdf5;
            --primary-dark: #065f46;
            --sidebar-bg: #ffffff;
            --body-bg: #f8fafc;
            --border-color: #e2e8f0;
            --text-main: #1e293b;
            --text-muted: #64748b;
        }

        body {
            background-color: var(--body-bg);
            color: var(--text-main);
            font-family: 'Inter', sans-serif;
            font-size: 0.875rem;
            margin: 0;
        }

        /* ── Topbar ── */
        .topbar {
            background: #fff;
            border-bottom: 1px solid var(--border-color);
            height: 64px;
            position: sticky;
            top: 0;
            z-index: 1020;
        }

        .topbar-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-main);
        }

        /* ── Avatar ── */
        .avatar {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: var(--primary-light);
            color: var(--primary-dark);
            font-size: 12px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        /* ── Sidebar ── */
        #sidebar {
            width: 250px;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--border-color);
            transition: all 0.3s;
            padding: 1.25rem;
        }

        .sidebar-logo {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-main);
            padding-bottom: 1.5rem;
            margin-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .sidebar-logo .dot {
            width: 12px;
            height: 12px;
            border-radius: 4px;
            background: var(--primary);
            display: inline-block;
        }

        /* ── Nav Links ── */
        .nav-link {
            color: var(--text-muted);
            font-weight: 500;
            border-radius: 8px;
            padding: 0.6rem 1rem;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }

        .nav-link i {
            font-size: 1.1rem;
        }

        .nav-link:hover {
            background-color: var(--body-bg);
            color: var(--primary);
        }

        .nav-link.active {
            background: var(--primary-light);
            color: var(--primary-dark);
            font-weight: 600;
        }

        /* ── Dropdown & Collapse ── */
        .collapse.show {
            margin-bottom: 0.5rem;
        }

        .transition-chevron {
            transition: transform 0.2s ease;
            font-size: 0.75rem;
        }

        [aria-expanded="true"] .transition-chevron {
            transform: rotate(180deg);
        }

        /* ── Alerts ── */
        .alert {
            border: none;
            border-radius: 10px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            font-weight: 500;
        }

        /* ── Main Content ── */
        .content {
            padding: 2rem;
            overflow-x: hidden;
        }

        @media (max-width: 768px) {
            #sidebar { display: none; } /* On pourrait ajouter un menu mobile ici */
        }
    </style>

    @stack('styles')
</head>

<body>
{{-- Topbar --}}
<header class="topbar d-flex align-items-center justify-content-between px-4">
    <span class="topbar-title">@yield('title', 'Tableau de bord')</span>

    <div class="dropdown">
        <div class="d-flex align-items-center gap-3" style="cursor: pointer;" data-bs-toggle="dropdown">
            <div class="d-none d-md-block text-end">
                <div class="fw-bold mb-0" style="line-height: 1.2;">{{ auth()->user()->name ?? 'Tsilavina' }}</div>
                <small class="text-muted">Administrateur</small>
            </div>
            <div class="avatar shadow-sm">
                {{ strtoupper(substr(auth()->user()->name ?? 'TS', 0, 2)) }}
            </div>
        </div>

        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-3" style="border-radius: 12px; min-width: 180px;">
            <li><a class="dropdown-item py-2" href="#"><i class="bi bi-person me-2"></i> Profil</a></li>
            <li><hr class="dropdown-divider mx-2"></li>
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
</header>

<div class="d-flex" style="min-height: calc(100vh - 64px);">
    {{-- Sidebar --}}
    <aside id="sidebar" class="d-flex flex-column gap-1">
        <div class="sidebar-logo d-flex align-items-center gap-2 px-2">
            <span class="dot"></span>
            <span>Denis-app</span>
        </div>

        <a href="{{ route('admin.vente.dashboard') }}"
           class="nav-link {{ request()->routeIs('admin.vente.dashboard') ? 'active' : '' }}">
            <i class="bi bi-house-door"></i> Accueil
        </a>

        {{-- Groupe Vente --}}
        @php $venteOpen = request()->routeIs('admin.vente.*') || request()->routeIs('vente.*'); @endphp
        <button class="nav-link border-0 bg-transparent w-100 d-flex align-items-center justify-content-between {{ $venteOpen ? 'active' : '' }}"
                data-bs-toggle="collapse" data-bs-target="#groupVente"
                aria-expanded="{{ $venteOpen ? 'true' : 'false' }}">
                <span class="d-flex align-items-center gap-2">
                    <i class="bi bi-cart3"></i> Ventes
                </span>
            <i class="bi bi-chevron-down transition-chevron"></i>
        </button>
        <div class="collapse ps-3 {{ $venteOpen ? 'show' : '' }}" id="groupVente">
            <a href="{{ route('admin.vente.create') }}" class="nav-link py-2 {{ request()->routeIs('admin.vente.create') ? 'active' : '' }}">
                Nouvelle vente
            </a>
            <a href="{{ route('admin.vente.index') }}" class="nav-link py-2 {{ request()->routeIs('admin.vente.index') ? 'active' : '' }}">
                Historique
            </a>
        </div>

        {{-- Groupe Stock --}}
        @php $stockOpen = request()->routeIs('stock.*'); @endphp
        <button class="nav-link border-0 bg-transparent w-100 d-flex align-items-center justify-content-between {{ $stockOpen ? 'active' : '' }}"
                data-bs-toggle="collapse" data-bs-target="#groupStock"
                aria-expanded="{{ $stockOpen ? 'true' : 'false' }}">
                <span class="d-flex align-items-center gap-2">
                    <i class="bi bi-box-seam"></i> Stocks
                </span>
            <i class="bi bi-chevron-down transition-chevron"></i>
        </button>
        <div class="collapse ps-3 {{ $stockOpen ? 'show' : '' }}" id="groupStock">
            <a href="{{ route('stock.index') }}" class="nav-link py-2 {{ request()->routeIs('stock.index') ? 'active' : '' }}">
                Inventaire
            </a>
            <a href="{{ route('stock.create') }}" class="nav-link py-2 {{ request()->routeIs('stock.create') ? 'active' : '' }}">
                Entrée stock
            </a>
        </div>
    </aside>

    {{-- Contenu principal --}}
    <main class="content flex-grow-1">
        @if(session('success'))
            <div class="alert alert-success border-start border-success border-4 mb-4" role="alert">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>{!! session('success') !!}</span>
                </div>
            </div>
        @endif

        @if(session('error') || $errors->any())
            <div class="alert alert-danger border-start border-danger border-4 mb-4" role="alert">
                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-exclamation-triangle-fill mt-1"></i>
                    <ul class="mb-0 ps-0 list-unstyled">
                        @if(session('error')) <li>{{ session('error') }}</li> @endif
                        @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @yield('content')
    </main>
</div>

{{-- Bootstrap 5 JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
