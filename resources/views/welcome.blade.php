<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tsenan'i Vohitsoa - Gargotte Épicerie</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@500;600&family=Work+Sans:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

    <style>
        :root {
            --ink: #241B14;
            --clay: #9C4A2E;
            --clay-dark: #7A3A24;
            --gold: #C89B3C;
            --highland: #4F6B4A;
            --paper: #FBF8F2;
        }

        body {
            margin: 0;
            font-family: 'Work Sans', sans-serif;
            background-color: #121824;
            color: var(--paper);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem 2rem;
            max-width: 1100px;
            margin: 0 auto;
            width: 100%;
            box-sizing: border-box;
        }

        .nav-links a {
            color: var(--paper);
            text-decoration: none;
            margin-left: 1.5rem;
            font-weight: 500;
            font-size: 0.95rem;
            transition: color 0.2s;
        }

        .nav-links a:hover {
            color: var(--gold);
        }

        .hero {
            max-width: 1100px;
            margin: 2rem auto;
            padding: 0 1.5rem;
            width: 100%;
            box-sizing: border-box;
        }

        .hero-card {
            background: linear-gradient(165deg, var(--clay) 0%, var(--clay-dark) 100%);
            border-radius: 18px;
            padding: 3rem 2.5rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }

        .eyebrow {
            font-family: 'IBM Plex Mono', monospace;
            color: var(--gold);
            text-transform: uppercase;
            letter-spacing: 0.15em;
            font-size: 0.8rem;
            margin-bottom: 0.5rem;
        }

        .title {
            font-family: 'Fraunces', Georgia, serif;
            font-size: clamp(2rem, 4vw, 3.2rem);
            margin: 0 0 1rem 0;
            line-height: 1.1;
        }

        .subtitle {
            font-size: 1.15rem;
            opacity: 0.9;
            margin-bottom: 1.5rem;
        }

        .location-tag {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 0.85rem;
            background: rgba(0, 0, 0, 0.25);
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            border-left: 3px solid var(--gold);
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            max-width: 1100px;
            margin: 2rem auto;
            padding: 0 1.5rem 3rem;
            width: 100%;
            box-sizing: border-box;
        }

        .card {
            background: var(--paper);
            color: var(--ink);
            border-radius: 12px;
            padding: 1.75rem;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }

        .card h3 {
            font-family: 'Fraunces', Georgia, serif;
            margin-top: 0;
            color: var(--clay);
            font-size: 1.3rem;
        }

        .card p {
            font-size: 0.92rem;
            line-height: 1.6;
            color: #554a40;
            margin-bottom: 0;
        }
    </style>
</head>
<body>
<header class="navbar">
    <div style="font-family: 'Fraunces', serif; font-size: 1.4rem; font-weight: 600; color: var(--gold);">
        Tsenan'i Vohitsoa
    </div>
    <nav class="nav-links">
        @if (Route::has('login'))
            @auth
                <a href="{{ url('/dashboard') }}">Tableau de bord</a>
            @else
                <a href="{{ route('login') }}">Connexion</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}">Inscription</a>
                @endif
            @endauth
        @endif
    </nav>
</header>

<main class="hero">
    <div class="hero-card">
        <div class="eyebrow">Bienvenue</div>
        <h1 class="title">Tsenan'i Vohitsoa</h1>
        <p class="subtitle">Gargotte & Épicerie — Prêt à prendre toutes les commandes.</p>
        <div class="location-tag">
            📍 Vohitsoakofafabe, Antsoatany, Antsirabe II — RN 7 (Code postal 111)
        </div>
    </div>
</main>

<section class="grid">
    <div class="card">
        <h3>Gargotte & Restauration</h3>
        <p>Découvrez nos plats locaux et services de restauration sur la Route Nationale 7.</p>
    </div>
    <div class="card">
        <h3>Épicerie</h3>
        <p>Large choix de produits de première nécessité et fournitures locales.</p>
    </div>
    <div class="card">
        <h3>Prise de Commandes</h3>
        <p>Service rapide et sur mesure pour répondre à vos besoins spécifiques.</p>
    </div>
</section>
</body>
</html>
