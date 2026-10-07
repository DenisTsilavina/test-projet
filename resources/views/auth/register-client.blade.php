{{-- resources/views/auth/register-client.blade.php --}}
@extends('layouts.app')

@section('content')
    <style>
        .vs-auth {
            --ink: #241B14; --clay: #9C4A2E; --clay-dark: #7A3A24;
            --gold: #C89B3C; --highland: #4F6B4A; --paper: #FBF8F2;

            display: grid;
            grid-template-columns: minmax(280px, 38%) 1fr;
            min-height: 620px;
            max-width: 980px;
            margin: 2.5rem auto;
            background: var(--paper);
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 24px 60px -20px rgba(36, 27, 20, 0.35);
            font-family: 'Work Sans', -apple-system, sans-serif;
            color: var(--ink);
        }
        .vs-auth__panel {
            background-image:
                repeating-linear-gradient(45deg, rgba(255,255,255,0.035) 0 2px, transparent 2px 14px),
                repeating-linear-gradient(-45deg, rgba(0,0,0,0.05) 0 2px, transparent 2px 14px),
                linear-gradient(165deg, var(--clay) 0%, var(--clay-dark) 100%);
            color: var(--paper);
            padding: 2.75rem 2.25rem;
            display: flex; flex-direction: column; justify-content: space-between;
        }
        .vs-auth__eyebrow { font-family: 'IBM Plex Mono', monospace; font-size: .7rem; letter-spacing: .16em; text-transform: uppercase; color: var(--gold); margin-bottom: .9rem; }
        .vs-auth__wordmark { font-family: 'Fraunces', Georgia, serif; font-size: clamp(1.9rem, 3.4vw, 2.5rem); font-weight: 600; line-height: 1.08; margin: 0 0 1.25rem; }
        .vs-auth__pitch { font-size: .9rem; line-height: 1.6; color: rgba(251,248,242,.85); max-width: 22rem; }
        .vs-auth__address { font-family: 'IBM Plex Mono', monospace; font-size: .78rem; line-height: 1.65; color: rgba(251,248,242,.85); border-top: 1px solid rgba(251,248,242,.25); padding-top: 1rem; margin-top: 2rem; }
        .vs-auth__form-side { padding: 3rem; display: flex; flex-direction: column; justify-content: center; }
        .vs-auth__heading { font-family: 'Fraunces', Georgia, serif; font-size: 1.6rem; font-weight: 600; margin-bottom: .35rem; }
        .vs-auth__subheading { font-size: .88rem; color: #6b6055; margin-bottom: 1.75rem; }
        .vs-field { margin-bottom: 1.15rem; }
        .vs-field label { display: block; font-size: .78rem; font-weight: 600; margin-bottom: .4rem; }
        .vs-field input { width: 100%; border: 1.5px solid #ddd0ba; background: var(--paper); border-radius: 9px; padding: .65rem .85rem; font-size: .95rem; color: var(--ink); transition: border-color .15s, box-shadow .15s; }
        .vs-field input:focus { outline: none; border-color: var(--clay); box-shadow: 0 0 0 3px rgba(156,74,46,.15); }
        .vs-field input.is-invalid { border-color: #b3402e; }
        .vs-error { display: block; margin-top: .3rem; font-size: .78rem; color: #b3402e; }
        .vs-btn { background: var(--clay); color: var(--paper); border: none; border-radius: 9px; padding: .75rem 1.6rem; font-size: .92rem; font-weight: 600; cursor: pointer; width: 100%; margin-top: .5rem; transition: background .15s; }
        .vs-btn:hover { background: var(--clay-dark); }
        .vs-alt { margin-top: 1.25rem; font-size: .85rem; color: #6b6055; text-align: center; }
        .vs-alt a { color: var(--highland); font-weight: 600; text-decoration: none; }
        .vs-alt a:hover { text-decoration: underline; }
        @media (max-width: 760px) {
            .vs-auth { grid-template-columns: 1fr; margin: 1rem; }
            .vs-auth__panel, .vs-auth__form-side { padding: 2rem 1.75rem; }
        }
    </style>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@500;600&family=Work+Sans:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

    <div class="vs-auth">
        <div class="vs-auth__panel">
            <div>
                <div class="vs-auth__eyebrow">Boutique en ligne</div>
                <div class="vs-auth__wordmark">Tsenan'i<br>Vohitsoa</div>
                <p class="vs-auth__pitch">
                    Créez votre compte pour passer vos commandes et suivre leur traitement.
                </p>
            </div>
            <div class="vs-auth__address">
                PK135, Route Nationale 7<br>
                Antsirabe, Madagascar<br>
                110
            </div>
        </div>

        <div class="vs-auth__form-side">
            <div class="vs-auth__heading">Créer un compte</div>
            <div class="vs-auth__subheading">Quelques secondes suffisent.</div>

            {{-- Aucun champ "rôle" ici : le contrôleur force CLIENT côté serveur. --}}
            <form method="POST" action="{{ route('client.register.store') }}">
                @csrf

                <div class="vs-field">
                    <label for="name">Nom complet</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}"
                           class="@error('name') is-invalid @enderror" required autofocus autocomplete="name">
                    @error('name') <span class="vs-error">{{ $message }}</span> @enderror
                </div>

                <div class="vs-field">
                    <label for="email">Adresse e-mail</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                           class="@error('email') is-invalid @enderror" required autocomplete="email">
                    @error('email') <span class="vs-error">{{ $message }}</span> @enderror
                </div>

                <div class="vs-field">
                    <label for="password">Mot de passe</label>
                    <input id="password" type="password" name="password"
                           class="@error('password') is-invalid @enderror" required autocomplete="new-password">
                    @error('password') <span class="vs-error">{{ $message }}</span> @enderror
                </div>

                <div class="vs-field">
                    <label for="password_confirmation">Confirmer le mot de passe</label>
                    <input id="password_confirmation" type="password" name="password_confirmation"
                           required autocomplete="new-password">
                </div>

                <button type="submit" class="vs-btn">Créer mon compte</button>
            </form>

            <p class="vs-alt">
                Déjà un compte ? <a href="{{ route('login') }}">Se connecter</a>
            </p>
        </div>
    </div>
@endsection
