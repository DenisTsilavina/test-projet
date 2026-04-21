<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ecommerce-Denis – @yield('title', 'Boutique')</title>

    {{-- Bootstrap 5 & Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    {{-- Google Font: Inter pour un look moderne --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --accent: #10b981; /* Emeraude moderne */
            --accent-soft: rgba(16, 185, 129, 0.1);
            --bg-page: #f8fafc;
            --text-dark: #1e293b;
            --header-height: 70px;
        }

        body {
            background-color: var(--bg-page);
            font-family: 'Inter', sans-serif;
            color: var(--text-dark);
            padding-top: var(--header-height);
            font-size: 14px;
        }

        /* ── Header / Navbar ── */
        .navbar-main {
            height: var(--header-height);
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 1px solid #e2e8f0;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--accent) !important;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .brand-icon {
            width: 8px;
            height: 8px;
            background: var(--accent);
            border-radius: 50%;
        }

        /* ── Liens de Navigation ── */
        .nav-link {
            color: #64748b;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            border-radius: 8px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-link:hover {
            color: var(--accent);
            background: var(--accent-soft);
        }

        .nav-link.active {
            color: var(--accent) !important;
            background: var(--accent-soft);
            font-weight: 600;
        }

        /* ── Profil & Dropdown ── */
        .profile-container {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 4px 12px;
            border-radius: 50px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .profile-container:hover {
            background: #f1f5f9;
        }

        .avatar-modern {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--accent), #059669);
            color: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.85rem;
            box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.2);
        }

        /* ── Contenu ── */
        .main-wrapper {
            padding: 2rem 0;
        }

        .content-card {
            background: white;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        /* Alerts stylisées */
        .alert {
            border: none;
            border-radius: 12px;
            padding: 12px 20px;
            font-weight: 500;
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
            border-radius: 12px;
            padding: 8px;
            margin-top: 10px !important;
        }

        .dropdown-item {
            border-radius: 8px;
            padding: 8px 12px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.9rem;
        }
    </style>
        @stack('styles')
    </head>

    <body>

        <header class="navbar-main">
        <div class="container d-flex align-items-center justify-content-between h-100">

            <a href="/" class="navbar-brand text-decoration-none">
                <div class="brand-icon"></div>
                <span>Denis Shop</span>
            </a>

            <nav class="d-none d-lg-block">
                <ul class="nav gap-1">
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="bi bi-grid"></i> Produits
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('client.achat') }}" class="nav-link">
                            <i class="bi bi-cart3"></i> Panier
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="bi bi-bag-check"></i> Commandes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="bi bi-search"></i> Recherche
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="dropdown">
                <div class="profile-container" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="text-end d-none d-sm-block me-1">
                        <div class="fw-bold" style="font-size: 0.85rem;">{{ auth()->user()->name ?? 'Utilisateur' }}</div>
                        <div class="text-muted" style="font-size: 0.75rem;">Client</div>
                    </div>
                    <div class="avatar-modern">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>
                </div>

                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#"><i class="bi bi-gear"></i> Paramètres</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right"></i> Déconnexion
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </header>
    <main class="main-wrapper container">
        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert alert-success d-flex align-items-center shadow-sm mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <div>{!! session('success') !!}</div>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger shadow-sm mb-4">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Contenu Principal --}}
        <div class="page-content">
            @yield('content')
        </div>
    </main>

{{-- Bootstrap 5 JS --}}
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        @stack('scripts')
    </body>
</html>
