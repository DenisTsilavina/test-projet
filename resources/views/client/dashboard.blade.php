@extends('layouts.client')
@section('title', 'Dashboard')
@section('content')

    {{-- ================= PRODUITS ================= --}}
    @include('client.partials.stock')

    {{-- ================= AUTH / INFO CLIENT ================= --}}
    <div class="container-fluid px-4 mt-4">

        @guest('client')
            {{-- 🔒 Non connecté — Bannière d'appel à l'action --}}
            <div class="card border-0 shadow-sm"
                 style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 16px;">
                <div class="card-body py-4 px-4">
                    <div class="row align-items-center">

                        <div class="col-md-8 text-white">
                            <h4 class="fw-bold mb-1">
                                <i class="bi bi-bag-heart me-2"></i>Prêt à commander ?
                            </h4>
                            <p class="mb-0 opacity-75">
                                Créez un compte ou connectez-vous pour passer votre commande.
                            </p>
                        </div>

                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <button class="btn btn-light fw-semibold me-2"
                                    data-bs-toggle="modal"
                                    data-bs-target="#authModal"
                                    onclick="openTab('pane-register')">
                                <i class="bi bi-person-plus me-1"></i>S'inscrire
                            </button>
                            <button class="btn btn-outline-light fw-semibold"
                                    data-bs-toggle="modal"
                                    data-bs-target="#authModal"
                                    onclick="openTab('pane-login')">
                                <i class="bi bi-box-arrow-in-right me-1"></i>Se connecter
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        @endguest

    </div>
    {{-- ================= FIN AUTH / INFO ================= --}}


    {{-- ================= MODAL AUTH ================= --}}
    <div class="modal fade" id="authModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">

                {{-- Header onglets --}}
                <div class="modal-header border-0 p-0"
                     style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <ul class="nav nav-pills flex-grow-1 px-3 pt-3" id="authTabs">
                        <li class="nav-item w-50 text-center">
                            <button class="nav-link active w-100 text-white fw-semibold"
                                    id="tab-register"
                                    data-bs-toggle="pill"
                                    data-bs-target="#pane-register">
                                <i class="bi bi-person-plus me-1"></i>S'inscrire
                            </button>
                        </li>
                        <li class="nav-item w-50 text-center">
                            <button class="nav-link w-100 text-white fw-semibold"
                                    id="tab-login"
                                    data-bs-toggle="pill"
                                    data-bs-target="#pane-login">
                                <i class="bi bi-box-arrow-in-right me-1"></i>Se connecter
                            </button>
                        </li>
                    </ul>
                    <button type="button"
                            class="btn-close btn-close-white m-3 mt-2"
                            data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body tab-content p-4">

                    {{-- ===== INSCRIPTION ===== --}}
                    <div class="tab-pane fade show active" id="pane-register">

                        {{-- Message de succès (ex: après vérification) --}}
                        @if(session('success'))
                            <div class="alert alert-success py-2 mb-3">
                                <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
                            </div>
                        @endif

                        {{-- Message d'info générique --}}
                        @if(session('message'))
                            <div class="alert alert-info py-2 mb-3">
                                <i class="bi bi-info-circle me-1"></i>{{ session('message') }}
                            </div>
                        @endif

                        <form action="{{ route('client.register') }}" method="POST">
                            @csrf
                            <div class="row g-3">

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nom</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                                        <input type="text" name="nom"
                                               class="form-control @error('nom') is-invalid @enderror"
                                               value="{{ old('nom') }}"
                                               placeholder="Votre nom" required>
                                    </div>
                                    @error('nom')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Prénom</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                                        <input type="text" name="prenom"
                                               class="form-control @error('prenom') is-invalid @enderror"
                                               value="{{ old('prenom') }}"
                                               placeholder="Votre prénom" required>
                                    </div>
                                    @error('prenom')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                        <input type="email" name="email"
                                               class="form-control @error('email') is-invalid @enderror"
                                               value="{{ old('email') }}"
                                               placeholder="exemple@mail.com" required>
                                    </div>
                                    @error('email')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Téléphone</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-phone"></i></span>
                                        <input type="text" name="phone"
                                               class="form-control @error('phone') is-invalid @enderror"
                                               value="{{ old('phone') }}"
                                               placeholder="034 XX XXX XX" required>
                                    </div>
                                    @error('phone')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Adresse</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                        <input type="text" name="adresse"
                                               class="form-control @error('adresse') is-invalid @enderror"
                                               value="{{ old('adresse') }}"
                                               placeholder="Votre adresse" required>
                                    </div>
                                    @error('adresse')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Ville</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-building"></i></span>
                                        <input type="text" name="ville"
                                               class="form-control @error('ville') is-invalid @enderror"
                                               value="{{ old('ville') }}"
                                               placeholder="Votre ville" required>
                                    </div>
                                    @error('ville')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{--
                                    CHAMPS MOT DE PASSE RETIRÉS de la blade :
                                    Le contrôleur génère un code aléatoire comme mot de passe initial.
                                    L'utilisateur n'en choisit pas un à l'inscription —
                                    il se connectera avec le code envoyé par email.
                                    Ces champs ne servent donc à rien ici et ont été supprimés.
                                --}}

                            </div>

                            <button type="submit" class="btn btn-primary w-100 mt-4">
                                <i class="bi bi-send me-1"></i>Recevoir mon code de vérification
                            </button>
                        </form>
                    </div>

                    {{-- ===== CONNEXION ===== --}}
                    <div class="tab-pane fade" id="pane-login">

                        {{--
                            CORRECTION : session('login_error') correspond bien au contrôleur.
                            La clé 'show_register_modal' dans le contrôleur était mal orthographiée
                            ('show_reistre_modal') — à corriger dans ClientAuthController::login()
                            pour que l'ouverture automatique du modal fonctionne après erreur login.
                        --}}
                        @if(session('login_error'))
                            <div class="alert alert-danger py-2 mb-3">
                                <i class="bi bi-exclamation-circle me-1"></i>{{ session('login_error') }}
                            </div>
                        @endif

                        <form action="{{ route('client.login') }}" method="POST">
                            @csrf
                            <div class="row g-3">

                                <div class="col-12">
                                    <label class="form-label fw-semibold">Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                        <input type="email" name="email"
                                               class="form-control @error('email') is-invalid @enderror"
                                               value="{{ old('email') }}"
                                               placeholder="exemple@mail.com" required>
                                    </div>
                                    @error('email')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">Mot de passe</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                        <input type="password" name="password"
                                               id="login-password"
                                               class="form-control"
                                               placeholder="Votre mot de passe" required>
                                        <button class="btn btn-outline-secondary" type="button"
                                                onclick="togglePwd('login-password','icon-login-pwd')">
                                            <i class="bi bi-eye" id="icon-login-pwd"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-check">
                                        <input type="checkbox" name="remember"
                                               class="form-check-input" id="remember">
                                        <label class="form-check-label" for="remember">
                                            Se souvenir de moi
                                        </label>
                                    </div>
                                </div>

                            </div>

                            <button type="submit" class="btn btn-primary w-100 mt-4">
                                <i class="bi bi-box-arrow-in-right me-1"></i>Se connecter
                            </button>
                        </form>
                    </div>

                </div>{{-- fin tab-content --}}
            </div>
        </div>
    </div>
    {{-- ================= FIN MODAL ================= --}}

@endsection

@push('scripts')
    <script>
        function togglePwd(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon  = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        }

        function openTab(paneId) {
            // Légère attente pour laisser Bootstrap initialiser le modal
            setTimeout(() => {
                const tab = document.querySelector(`[data-bs-target="#${paneId}"]`);
                if (tab) tab.click();
            }, 150);
        }

        document.addEventListener('DOMContentLoaded', function () {
            const modalEl = document.getElementById('authModal');
            const modal   = bootstrap.Modal.getOrCreate(modalEl);

            {{-- Erreurs de validation inscription → ouvre le modal sur l'onglet inscription --}}
            @if($errors->any() && !session('login_error'))
            modal.show();
            {{-- L'onglet "register" est actif par défaut, rien à faire --}}
            @endif

            {{-- Erreur de connexion → ouvre le modal directement sur l'onglet connexion --}}
            @if(session('login_error'))
            modal.show();
            openTab('pane-login');
            @endif

            {{-- Cas générique : forcer l'ouverture du modal (ex: redirect après inscription) --}}
            @if(session('show_register_modal'))
            modal.show();
            @endif
        });
    </script>
@endpush
