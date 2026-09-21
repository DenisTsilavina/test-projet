@extends('layouts.app')

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@500;600&family=Work+Sans:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

    <style>
        .vs-auth {
            --ink: #241B14;
            --clay: #9C4A2E;
            --clay-dark: #7A3A24;
            --gold: #C89B3C;
            --highland: #4F6B4A;
            --paper: #FBF8F2;
            --danger: #b3402e;

            display: grid;
            grid-template-columns: minmax(280px, 38%) 1fr;
            min-height: 600px;
            max-width: 900px;
            margin: 2rem auto;
            background: var(--paper);
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 24px 60px -20px rgba(36, 27, 20, 0.35);
            font-family: 'Work Sans', -apple-system, sans-serif;
            color: var(--ink);
        }

        .vs-auth__panel {
            background:
                repeating-linear-gradient(45deg, rgba(255,255,255,0.035) 0 2px, transparent 2px 14px),
                repeating-linear-gradient(-45deg, rgba(0,0,0,0.05) 0 2px, transparent 2px 14px),
                linear-gradient(165deg, var(--clay) 0%, var(--clay-dark) 100%);
            color: var(--paper);
            padding: 2.5rem 2rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .vs-auth__eyebrow {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 0.7rem;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: var(--gold);
            margin-bottom: 0.9rem;
        }

        .vs-auth__wordmark {
            font-family: 'Fraunces', Georgia, serif;
            font-size: clamp(1.8rem, 3vw, 2.3rem);
            font-weight: 600;
            line-height: 1.08;
            margin: 0 0 1.5rem;
        }

        .vs-marker {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            width: 92px;
            border-radius: 6px;
            overflow: hidden;
            border: 2px solid rgba(251, 248, 242, 0.85);
            margin-bottom: 1.5rem;
        }

        .vs-marker__cap {
            width: 100%;
            background: var(--gold);
            color: var(--ink);
            font-family: 'IBM Plex Mono', monospace;
            font-size: 0.62rem;
            text-align: center;
            padding: 3px 0;
        }

        .vs-marker__body {
            width: 100%;
            background: var(--paper);
            color: var(--ink);
            font-family: 'IBM Plex Mono', monospace;
            font-weight: 600;
            font-size: 1rem;
            text-align: center;
            padding: 6px 0;
        }

        .vs-auth__address {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 0.75rem;
            line-height: 1.6;
            color: rgba(251, 248, 242, 0.85);
            border-top: 1px solid rgba(251, 248, 242, 0.25);
            padding-top: 1rem;
        }

        .vs-auth__form-side {
            padding: 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .vs-auth__heading {
            font-family: 'Fraunces', Georgia, serif;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.35rem;
        }

        .vs-auth__subheading {
            font-size: 0.85rem;
            color: #6b6055;
            margin-bottom: 1.5rem;
        }

        .vs-field {
            margin-bottom: 1.15rem;
        }

        .vs-field label {
            display: block;
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--ink);
            margin-bottom: 0.35rem;
        }

        .vs-field input {
            width: 100%;
            border: 1.5px solid #ddd0ba;
            background: var(--paper);
            border-radius: 8px;
            padding: 0.55rem 0.8rem;
            font-size: 0.9rem;
            color: var(--ink);
            box-sizing: border-box;
        }

        .vs-field input:focus {
            outline: none;
            border-color: var(--clay);
            box-shadow: 0 0 0 3px rgba(156, 74, 46, 0.15);
        }

        .vs-field input.is-invalid {
            border-color: var(--danger);
        }

        .vs-alert-success {
            background-color: #e6f4ea;
            color: #1e7e34;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            font-size: 0.85rem;
            margin-bottom: 1.25rem;
            border: 1px solid #b7e1cd;
        }

        .vs-error {
            display: block;
            color: var(--danger);
            font-size: 0.78rem;
            margin-top: 0.3rem;
            font-weight: 500;
        }

        .vs-btn {
            width: 100%;
            background: var(--clay);
            color: var(--paper);
            border: none;
            border-radius: 8px;
            padding: 0.75rem;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s ease;
            margin-top: 0.5rem;
        }

        .vs-btn:hover {
            background: var(--clay-dark);
        }

        @media (max-width: 760px) {
            .vs-auth {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
    <div class="vs-auth">
        <div class="vs-auth__panel">
            <div>
                <div class="vs-auth__eyebrow">Espace Administration</div>
                <div class="vs-auth__wordmark">Tsenan'i<br>Vohitsoa</div>

                <div class="vs-marker">
                    <div class="vs-marker__cap">RN7</div>
                    <div class="vs-marker__body">PK135</div>
                </div>
            </div>

            <div class="vs-auth__address">
                Vohitsoakofafabe, Antsoatany<br>
                Antsirabe II, RN 7<br>
                Code postal 111
            </div>
        </div>

        <div class="vs-auth__form-side">
            <h1 class="vs-auth__heading">Inscription Client</h1>
            <p class="vs-auth__subheading">Création d'un nouveau compte client.</p>

            @if (session('status'))
                <div class="vs-alert-success">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('user.store') }}">
                @csrf

                {{-- Champ caché garantissant l'envoi du rôle Client (0) --}}
                <input type="hidden" name="role" value="{{ \App\Enums\UserRole::CLIENT->value ?? 0 }}">

                <div class="vs-field">
                    <label for="name">Nom complet</label>
                    <input id="name" type="text" class="@error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                    @error('name')
                    <span class="vs-error" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="vs-field">
                    <label for="email">Adresse E-mail</label>
                    <input id="email" type="email" class="@error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
                    @error('email')
                    <span class="vs-error" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="vs-field">
                    <label for="password">Mot de passe</label>
                    <input id="password" type="password" class="@error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                    @error('password')
                    <span class="vs-error" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="vs-field">
                    <label for="password-confirm">Confirmer le mot de passe</label>
                    <input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password">
                </div>

                <div>
                    <button type="submit" class="vs-btn">
                        Enregistrer l'utilisateur
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
