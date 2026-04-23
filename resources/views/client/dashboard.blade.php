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
                            {{-- Bouton S'inscrire --}}
                            <button class="btn btn-light fw-semibold me-2"
                                    data-bs-toggle="modal"
                                    data-bs-target="#authModal"
                                    id="btn-open-register"
                                    onclick="openTab('pane-register')">
                                <i class="bi bi-person-plus me-1"></i>S'inscrire
                            </button>
                            {{-- Bouton Se connecter --}}
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

        @auth('client')
            {{-- ✅ Connecté — Carte informations --}}
            <div class="card border-primary shadow-sm" style="border-radius: 16px;">
                <div class="card-header bg-primary text-white fw-semibold">
                    <i class="bi bi-person-fill me-1"></i>
                    Bonjour, {{ Auth::guard('client')->user()->prenom }} {{ Auth::guard('client')->user()->nom }} !
                </div>
                <form action="{{ route('client.createNewClient') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row g-3">

                            {{-- NOM --}}
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Nom</label>
                                <input type="text" class="form-control bg-light"
                                       value="{{ Auth::guard('client')->user()->nom }}" disabled>
                            </div>

                            {{-- PRENOM --}}
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Prénom</label>
                                <input type="text" class="form-control bg-light"
                                       value="{{ Auth::guard('client')->user()->prenom }}" disabled>
                            </div>

                            {{-- PHONE --}}
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Téléphone</label>
                                <input type="text" class="form-control bg-light"
                                       value="{{ Auth::guard('client')->user()->phone }}" disabled>
                            </div>

                            {{-- ADRESSE --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Adresse</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                    <input type="text" name="adresse"
                                           class="form-control @error('adresse') is-invalid @enderror"
                                           value="{{ old('adresse', Auth::guard('client')->user()->adresse) }}"
                                           placeholder="Adresse de livraison">
                                </div>
                                @error('adresse')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- VILLE --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Ville</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-building"></i></span>
                                    <input type="text" name="ville"
                                           class="form-control @error('ville') is-invalid @enderror"
                                           value="{{ old('ville', Auth::guard('client')->user()->ville) }}"
                                           placeholder="Ville de livraison">
                                </div>
                                @error('ville')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>

                        <div class="d-flex gap-2 mt-3">
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="bi bi-check-circle me-1"></i>Enregistrer la commande
                            </button>
                            {{-- Logout client --}}
                            <form action="{{ route('client.logout') }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger">
                                    <i class="bi bi-box-arrow-right me-1"></i>Déconnexion
                                </button>
                            </form>
                        </div>

                    </div>
                </form>
            </div>
        @endauth

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

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Mot de passe</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                        <input type="password" name="password"
                                               id="reg-password"
                                               class="form-control @error('password') is-invalid @enderror"
                                               placeholder="Minimum 8 caractères" required>
                                        <button class="btn btn-outline-secondary" type="button"
                                                onclick="togglePwd('reg-password','icon-reg-pwd')">
                                            <i class="bi bi-eye" id="icon-reg-pwd"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Confirmer</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                        <input type="password" name="password_confirmation"
                                               id="reg-confirm"
                                               class="form-control"
                                               placeholder="Répétez le mot de passe" required>
                                        <button class="btn btn-outline-secondary" type="button"
                                                onclick="togglePwd('reg-confirm','icon-reg-confirm')">
                                            <i class="bi bi-eye" id="icon-reg-confirm"></i>
                                        </button>
                                    </div>
                                </div>

                            </div>

                            <button type="submit" class="btn btn-primary w-100 mt-4">
                                <i class="bi bi-check-circle me-1"></i>Créer mon compte
                            </button>
                        </form>
                    </div>

                    {{-- ===== CONNEXION ===== --}}
                    <div class="tab-pane fade" id="pane-login">

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
                                               class="form-control"
                                               value="{{ old('email') }}"
                                               placeholder="exemple@mail.com" required>
                                    </div>
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

        // Ouvrir un onglet spécifique du modal
        function openTab(paneId) {
            setTimeout(() => {
                const tab = document.querySelector(`[data-bs-target="#${paneId}"]`);
                if (tab) tab.click();
            }, 100);
        }

        document.addEventListener('DOMContentLoaded', function () {
            const modalEl = document.getElementById('authModal');
            const modal   = new bootstrap.Modal(modalEl);

            @if($errors->any())
            modal.show(); // erreur inscription
            @endif

            @if(session('login_error'))
            modal.show();
            openTab('pane-login'); // erreur login → onglet connexion
            @endif

            @if(session('show_register_modal'))
            modal.show();
            @endif
        });
    </script>
@endpush
