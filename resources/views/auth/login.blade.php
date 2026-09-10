@extends('layouts.app')

@section('content')
    <style>
        .vs-auth {
            --ink: #241B14;
            --clay: #9C4A2E;
            --clay-dark: #7A3A24;
            --gold: #C89B3C;
            --highland: #4F6B4A;
            --parchment: #EFE3CF;
            --paper: #FBF8F2;

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
            position: relative;
            background: linear-gradient(165deg, var(--clay) 0%, var(--clay-dark) 100%);
            background-image:
                repeating-linear-gradient(45deg, rgba(255,255,255,0.035) 0 2px, transparent 2px 14px),
                repeating-linear-gradient(-45deg, rgba(0,0,0,0.05) 0 2px, transparent 2px 14px),
                linear-gradient(165deg, var(--clay) 0%, var(--clay-dark) 100%);
            color: var(--paper);
            padding: 2.75rem 2.25rem;
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
            font-size: clamp(1.9rem, 3.4vw, 2.5rem);
            font-weight: 600;
            line-height: 1.08;
            letter-spacing: -0.01em;
            margin: 0 0 1.75rem;
        }

        .vs-marker {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            width: 92px;
            border-radius: 6px 6px 3px 3px;
            overflow: hidden;
            border: 2px solid rgba(251, 248, 242, 0.85);
            margin-bottom: 1.5rem;
            box-shadow: 0 8px 18px -8px rgba(0,0,0,0.45);
        }

        .vs-marker__cap {
            width: 100%;
            background: var(--gold);
            color: var(--ink);
            font-family: 'IBM Plex Mono', monospace;
            font-size: 0.62rem;
            letter-spacing: 0.1em;
            text-align: center;
            padding: 3px 0;
        }

        .vs-marker__body {
            width: 100%;
            background: var(--paper);
            color: var(--ink);
            font-family: 'IBM Plex Mono', monospace;
            font-weight: 600;
            font-size: 1.05rem;
            text-align: center;
            padding: 8px 0 9px;
        }

        .vs-auth__address {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 0.78rem;
            line-height: 1.65;
            color: rgba(251, 248, 242, 0.85);
            border-top: 1px solid rgba(251, 248, 242, 0.25);
            padding-top: 1rem;
            margin-top: auto;
        }

        .vs-auth__form-side {
            padding: 3rem 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .vs-auth__heading {
            font-family: 'Fraunces', Georgia, serif;
            font-size: 1.6rem;
            font-weight: 600;
            margin-bottom: 0.35rem;
        }

        .vs-auth__subheading {
            font-size: 0.88rem;
            color: #6b6055;
            margin-bottom: 2rem;
        }

        .vs-field {
            margin-bottom: 1.35rem;
        }

        .vs-field label {
            display: block;
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 0.02em;
            color: var(--ink);
            margin-bottom: 0.4rem;
        }

        .vs-field input[type="email"],
        .vs-field input[type="password"] {
            width: 100%;
            border: 1.5px solid #ddd0ba;
            background: var(--paper);
            border-radius: 9px;
            padding: 0.65rem 0.85rem;
            font-size: 0.95rem;
            color: var(--ink);
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .vs-field input:focus {
            outline: none;
            border-color: var(--clay);
            box-shadow: 0 0 0 3px rgba(156, 74, 46, 0.15);
        }

        .vs-field input.is-invalid {
            border-color: #b3402e;
        }

        .vs-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.75rem;
        }

        .vs-remember {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.85rem;
            color: #4a4038;
        }

        .vs-remember input {
            accent-color: var(--clay);
        }

        .vs-actions {
            display: flex;
            align-items: center;
            gap: 1.25rem;
        }

        .vs-btn {
            background: var(--clay);
            color: var(--paper);
            border: none;
            border-radius: 9px;
            padding: 0.7rem 1.6rem;
            font-size: 0.92rem;
            font-weight: 600;
            letter-spacing: 0.01em;
            cursor: pointer;
            transition: background 0.15s ease, transform 0.1s ease;
        }

        .vs-btn:hover {
            background: var(--clay-dark);
        }

        .vs-btn:active {
            transform: translateY(1px);
        }

        .vs-link {
            font-size: 0.85rem;
            color: var(--highland);
            text-decoration: none;
        }

        .vs-link:hover {
            text-decoration: underline;
        }

        @media (prefers-reduced-motion: no-preference) {
            .vs-auth {
                animation: vs-rise 0.5s ease both;
            }
        }

        @keyframes vs-rise {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 760px) {
            .vs-auth {
                grid-template-columns: 1fr;
                margin: 1rem;
            }
            .vs-auth__panel {
                padding: 2rem 1.75rem;
            }
            .vs-auth__form-side {
                padding: 2rem 1.75rem;
            }
        }
    </style>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@500;600&family=Work+Sans:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

    <div class="vs-auth">
        <div class="vs-auth__panel">
            <div>
                <div class="vs-auth__eyebrow">Gestion des stocks</div>
                <div class="vs-auth__wordmark">Tsenan'i<br>Vohitsoa</div>

                <div class="vs-marker">
                    <div class="vs-marker__cap">RN7</div>
                    <div class="vs-marker__body">PK135</div>
                </div>
            </div>

            <div class="vs-auth__address">
                PK135, Route Nationale 7<br>
                Antsirabe, Madagascar<br>
                110
            </div>
        </div>

        <div class="vs-auth__form-side">
            <div class="vs-auth__heading">{{ __('Login') }}</div>
            <div class="vs-auth__subheading">Accédez à l'espace de gestion de Tsenan'i Vohitsoa.</div>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="vs-field">
                    <label for="email">{{ __('Email Address') }}</label>
                    <input id="email" type="email" class="@error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                    @error('email')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="vs-field">
                    <label for="password">{{ __('Password') }}</label>
                    <input id="password" type="password" class="@error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                    @error('password')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="vs-row">
                    <label class="vs-remember" for="remember">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        {{ __('Remember Me') }}
                    </label>
                </div>

                <div class="vs-actions">
                    <button type="submit" class="vs-btn">
                        {{ __('Login') }}
                    </button>

                    @if (Route::has('password.request'))
                        <a class="vs-link" href="{{ route('password.request') }}">
                            {{ __('Forgot Your Password?') }}
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>
@endsection
