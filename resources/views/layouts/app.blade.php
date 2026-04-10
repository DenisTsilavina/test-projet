<!DOCTYPE html>
    <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Ecommerce-Denis – @yield('title', 'App de vente')</title>

            {{-- Bootstrap 5 --}}
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
            {{-- Bootstrap Icons --}}
            <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

            <link rel="dns-prefetch" href="//fonts.bunny.net">
            <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
            @vite(['resources/css/app.css', 'resources/js/app.js'])

            <style>
                :root {
                    --accent:       #1D9E75;
                    --accent-light: #E1F5EE;
                    --accent-mid:   #5DCAA5;
                    --accent-dark:  #085041;
                    --bg:           #F1EFE8;
                }

                body {
                    background: var(--bg);
                    font-size: 14px;
                }

                /* ── Topbar ── */
                .topbar {
                    background: #fff;
                    border-bottom: 1px solid rgba(0,0,0,.1);
                    padding: 12px 2rem;
                    position: sticky;
                    top: 0;
                    z-index: 100;
                }

                .topbar-title {
                    font-size: 15px;
                    font-weight: 600;
                }

                /* ── Avatar ── */
                .avatar {
                    width: 32px;
                    height: 32px;
                    border-radius: 50%;
                    background: var(--accent-light);
                    color: var(--accent-dark);
                    font-size: 12px;
                    font-weight: 700;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }

                /* ── Sidebar ── */
                #sidebar {
                    width: 220px;
                    min-height: calc(100vh - 57px);
                    background: #fff;
                    border-right: 1px solid rgba(0,0,0,.1);
                    flex-shrink: 0;
                }

                .sidebar-logo {
                    font-size: 15px;
                    font-weight: 700;
                    border-bottom: 1px solid rgba(0,0,0,.1);
                }

                .sidebar-logo .dot {
                    width: 10px;
                    height: 10px;
                    border-radius: 50%;
                    background: var(--accent);
                    display: inline-block;
                }

                .nav-link {
                    color: #888780;
                    font-size: 13px;
                    border-radius: 8px;
                    padding: 8px 12px;
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    transition: background .15s, color .15s;
                }

                .nav-link:hover {
                    background: var(--bg);
                    color: #2C2C2A;
                }

                .nav-link.active {
                    background: var(--accent-light);
                    color: var(--accent-dark);
                    font-weight: 600;
                }

                /* ── Chevron animé ── */
                .transition-chevron {
                    transition: transform .2s ease;
                }
                [aria-expanded="true"] .transition-chevron {
                    transform: rotate(180deg);
                }

                /* ── Content ── */
                .content {
                    padding: 2rem;
                    flex: 1;
                    overflow-y: auto;
                }
            </style>

            @stack('styles')
        </head>

        <body>
        {{-- ───── Header (Topbar) en premier ───── --}}
            <header class="topbar d-flex align-items-center justify-content-between px-4 py-2 bg-white border-bottom shadow-sm">
                <span class="topbar-title fw-bold">@yield('title', 'App de vente')</span>

                <div class="dropdown">
                    <div class="d-flex align-items-center gap-2 text-muted"
                         style="cursor: pointer;"
                         data-bs-toggle="dropdown"
                         aria-expanded="false">

                        <div class="d-none d-md-block text-end">
                            <div class="fw-bold text-dark" style="font-size: 13px;">
                                {{ auth()->user()->name ?? 'Utilisateur' }}
                            </div>
                        </div>

                        <div class="avatar shadow-sm">
                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                        </div>
                    </div>

                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger d-flex align-items-center gap-2">
                                    <i class="bi bi-box-arrow-right"></i> Déconnexion
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </header>

            {{-- ───── Corps : Sidebar + Contenu ───── --}}
            <div class="d-flex" style="min-height: calc(100vh - 57px);">

                {{-- ───── Sidebar ───── --}}
                <aside id="sidebar" class="d-flex flex-column p-3 gap-1">

                    <div class="sidebar-logo d-flex align-items-center gap-2 px-2 pb-3 mb-2">
                        <span class="dot"></span> Denis-app
                    </div>

                    <a href="{{ route('admin.vente.dashboard') }}"
                       class="nav-link {{ request()->routeIs('admin.vente.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-grid-fill"></i> Tableau de bord
                    </a>

                    {{-- ── Groupe Vente ── --}}
                    @php $venteOpen = request()->routeIs('admin.vente.*') || request()->routeIs('vente.*'); @endphp
                    <button class="nav-link border-0 bg-transparent w-100 d-flex align-items-center justify-content-between {{ $venteOpen ? 'active' : '' }}"
                            data-bs-toggle="collapse" data-bs-target="#groupVente"
                            aria-expanded="{{ $venteOpen ? 'true' : 'false' }}">
                        <span class="d-flex align-items-center gap-2">
                            <i class="bi bi-cart-fill"></i> Vente
                        </span>
                        <i class="bi bi-chevron-down small transition-chevron"></i>
                    </button>
                    <div class="collapse ps-3 {{ $venteOpen ? 'show' : '' }}" id="groupVente">
                        <a href="{{ route('admin.vente.create') }}"
                           class="nav-link {{ request()->routeIs('admin.vente.create') ? 'active' : '' }}">
                            <i class="bi bi-plus-circle"></i> Nouvelle vente
                        </a>
                        <a href="{{ route('admin.vente.index') }}"
                           class="nav-link {{ request()->routeIs('admin.vente.index') ? 'active' : '' }}">
                            <i class="bi bi-list-ul"></i> Historique
                        </a>
                    </div>

                    {{-- ── Groupe Stock ── --}}
                    @php $stockOpen = request()->routeIs('stock.*'); @endphp
                    <button class="nav-link border-0 bg-transparent w-100 d-flex align-items-center justify-content-between {{ $stockOpen ? 'active' : '' }}"
                            data-bs-toggle="collapse" data-bs-target="#groupStock"
                            aria-expanded="{{ $stockOpen ? 'true' : 'false' }}">
                        <span class="d-flex align-items-center gap-2">
                            <i class="bi bi-box-seam-fill"></i> Stock
                        </span>
                        <i class="bi bi-chevron-down small transition-chevron"></i>
                    </button>
                    <div class="collapse ps-3 {{ $stockOpen ? 'show' : '' }}" id="groupStock">
                        <a href="{{ route('stock.index') }}"
                           class="nav-link {{ request()->routeIs('stock.index') ? 'active' : '' }}">
                            <i class="bi bi-list-ul"></i> Liste du stock
                        </a>
                        <a href="{{ route('stock.create') }}"
                           class="nav-link {{ request()->routeIs('stock.create') ? 'active' : '' }}">
                            <i class="bi bi-plus-circle"></i> Nouvel stock
                        </a>
                    </div>

                </aside>

                {{-- ───── Contenu principal ───── --}}
                <main class="content">

                    {{-- Messages flash --}}
                    @if(session('success'))
                        <div class="alert alert-success border-start border-success border-3 py-2 px-3" role="alert">
                            {!! session('success') !!}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger border-start border-danger border-3 py-2 px-3" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger border-start border-danger border-3 py-2 px-3" role="alert">
                            <ul class="mb-0 ps-3">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
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
