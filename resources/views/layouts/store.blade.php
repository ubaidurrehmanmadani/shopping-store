<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? (($siteSettings->get('site_name') ?? 'RushBite').' Fast Food') }}</title>
        <style>
            :root {
                --bg: #1b0f0b;
                --bg-soft: #2a1510;
                --paper: #f7efe5;
                --paper-soft: #fff9f2;
                --ink: #211712;
                --ink-soft: #6d574b;
                --brand: #f45d22;
                --brand-dark: #cf4412;
                --mustard: #ffcf44;
                --green: #2f7d4c;
                --line: rgba(33, 23, 18, 0.1);
                --shadow: 0 22px 50px rgba(15, 8, 6, 0.22);
                --radius-lg: 30px;
                --radius-md: 22px;
                --radius-sm: 16px;
            }

            * { box-sizing: border-box; }
            html { scroll-behavior: smooth; }
            body {
                margin: 0;
                font-family: "Trebuchet MS", "Segoe UI", sans-serif;
                color: var(--paper);
                background:
                    radial-gradient(circle at top right, rgba(244, 93, 34, 0.18), transparent 28%),
                    radial-gradient(circle at 20% 0%, rgba(255, 207, 68, 0.12), transparent 24%),
                    linear-gradient(180deg, #2e1710 0%, #170d09 100%);
            }

            a { color: inherit; text-decoration: none; }
            img { display: block; max-width: 100%; }
            button, input, textarea, select { font: inherit; }

            .shell { width: min(1180px, calc(100% - 32px)); margin: 0 auto; }
            .topbar {
                position: sticky;
                top: 0;
                z-index: 50;
                backdrop-filter: blur(18px);
                background: rgba(27, 15, 11, 0.78);
                border-bottom: 1px solid rgba(255,255,255,0.08);
            }
            .topbar-inner {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 18px;
                padding: 16px 0;
            }
            .brand {
                display: inline-flex;
                align-items: center;
                gap: 12px;
                font-weight: 800;
                letter-spacing: 0.12em;
                text-transform: uppercase;
            }
            .brand-mark {
                width: 42px;
                height: 42px;
                border-radius: 14px;
                display: grid;
                place-items: center;
                background: linear-gradient(135deg, var(--brand), #ff8f29);
                color: white;
                box-shadow: 0 14px 28px rgba(244, 93, 34, 0.35);
            }
            .nav, .actions {
                display: flex;
                align-items: center;
                gap: 12px;
                flex-wrap: wrap;
            }
            .nav a, .actions a, .actions button {
                border: 0;
                background: transparent;
                color: rgba(255,255,255,0.9);
                cursor: pointer;
                padding: 10px 14px;
                border-radius: 999px;
            }
            .nav a.active, .actions a.primary, .actions button.primary {
                background: rgba(255,255,255,0.12);
                color: white;
            }

            .hero {
                display: grid;
                grid-template-columns: 1.15fr 0.85fr;
                gap: 24px;
                align-items: stretch;
                padding: 34px 0 20px;
            }
            .hero-copy, .hero-side, .panel, .card, .empty, .auth-card {
                border-radius: var(--radius-lg);
                box-shadow: var(--shadow);
            }
            .hero-copy {
                padding: 40px;
                color: white;
                background:
                    radial-gradient(circle at top right, rgba(255, 207, 68, 0.25), transparent 28%),
                    linear-gradient(135deg, rgba(244, 93, 34, 0.96), rgba(120, 32, 8, 0.96));
            }
            .hero-side {
                overflow: hidden;
                min-height: 420px;
                background:
                    linear-gradient(180deg, rgba(255,255,255,0.04), rgba(255,255,255,0)),
                    #4b2416;
            }
            .hero-side img { width: 100%; height: 100%; object-fit: cover; }
            .eyebrow {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                border-radius: 999px;
                padding: 8px 12px;
                background: rgba(255,255,255,0.15);
                font-size: 0.78rem;
                font-weight: 700;
                letter-spacing: 0.12em;
                text-transform: uppercase;
            }
            h1, h2, h3 {
                margin: 0 0 12px;
                line-height: 1.02;
                color: inherit;
            }
            h1 { font-size: clamp(2.7rem, 6vw, 5.4rem); }
            h2 { font-size: clamp(2rem, 4vw, 3.2rem); }
            h3 { font-size: 1.32rem; }
            p {
                margin: 0;
                line-height: 1.65;
                color: inherit;
            }

            .section {
                padding: 22px 0 14px;
            }
            .section-head {
                display: flex;
                align-items: end;
                justify-content: space-between;
                gap: 18px;
                margin-bottom: 18px;
            }
            .stack { display: grid; gap: 16px; }
            .grid {
                display: grid;
                gap: 18px;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            }
            .split {
                display: grid;
                gap: 20px;
                grid-template-columns: 1.15fr 0.85fr;
            }
            .panel, .card, .empty, .auth-card {
                background: var(--paper);
                color: var(--ink);
                border: 1px solid rgba(255,255,255,0.55);
            }
            .panel { padding: 24px; }
            .card { overflow: hidden; }
            .card-body { padding: 20px; display: grid; gap: 14px; }
            .media {
                aspect-ratio: 1.15 / 1;
                background:
                    radial-gradient(circle at top left, rgba(255, 207, 68, 0.35), transparent 22%),
                    linear-gradient(135deg, #ffd38b, #f6b17d);
            }
            .media img { width: 100%; height: 100%; object-fit: cover; }
            .meta { color: var(--ink-soft); font-size: 0.94rem; }
            .badge {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                width: fit-content;
                border-radius: 999px;
                padding: 8px 12px;
                background: rgba(47, 125, 76, 0.1);
                color: var(--green);
                font-size: 0.82rem;
                font-weight: 700;
            }
            .price {
                font-size: 1.28rem;
                font-weight: 800;
                color: var(--brand-dark);
            }
            .strike {
                margin-left: 8px;
                color: #99857c;
                text-decoration: line-through;
                font-size: 0.94rem;
            }
            .button {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                border: 0;
                border-radius: 999px;
                padding: 13px 18px;
                background: var(--brand);
                color: white;
                cursor: pointer;
                font-weight: 700;
            }
            .button.secondary {
                background: transparent;
                color: var(--ink);
                border: 1px solid var(--line);
            }
            .button.light {
                background: rgba(255,255,255,0.16);
                color: white;
            }
            .actions-row {
                display: flex;
                gap: 12px;
                align-items: center;
                flex-wrap: wrap;
            }
            .quick-stats {
                display: grid;
                gap: 14px;
                grid-template-columns: repeat(3, 1fr);
                margin-top: 20px;
            }
            .quick-stat {
                padding: 16px;
                border-radius: var(--radius-md);
                background: rgba(255,255,255,0.14);
            }
            .quick-stat strong { display: block; font-size: 1.4rem; margin-bottom: 4px; }

            .flash, .errors {
                margin: 18px 0 0;
                padding: 16px 18px;
                border-radius: 20px;
            }
            .flash {
                background: rgba(47, 125, 76, 0.18);
                border: 1px solid rgba(47, 125, 76, 0.25);
                color: #e3f5e8;
            }
            .errors {
                background: rgba(244, 93, 34, 0.18);
                border: 1px solid rgba(244, 93, 34, 0.2);
                color: #ffd6c8;
            }
            form.inline { display: inline; }
            label { display: grid; gap: 8px; color: var(--ink); }
            input, textarea, select {
                width: 100%;
                border: 1px solid var(--line);
                background: var(--paper-soft);
                color: var(--ink);
                border-radius: var(--radius-sm);
                padding: 13px 14px;
            }
            textarea { min-height: 130px; resize: vertical; }
            table { width: 100%; border-collapse: collapse; }
            th, td {
                padding: 14px 10px;
                text-align: left;
                border-bottom: 1px solid rgba(33, 23, 18, 0.08);
                color: var(--ink);
            }
            footer {
                padding: 40px 0 70px;
                color: rgba(255,255,255,0.74);
            }
            .auth-card {
                width: min(560px, 100%);
                margin: 48px auto;
                padding: 28px;
            }
            .empty { padding: 32px; text-align: center; }
            .menu-cta {
                display: grid;
                gap: 14px;
                grid-template-columns: repeat(2, 1fr);
            }
            .menu-cta .panel {
                background: rgba(255,255,255,0.07);
                color: white;
                border-color: rgba(255,255,255,0.09);
            }

            @media (max-width: 980px) {
                .hero, .split, .menu-cta { grid-template-columns: 1fr; }
                .topbar-inner { flex-direction: column; align-items: flex-start; }
                .quick-stats { grid-template-columns: 1fr; }
                .section-head { align-items: flex-start; flex-direction: column; }
            }
        </style>
    </head>
    <body>
        <header class="topbar">
            <div class="shell topbar-inner">
                <a href="{{ route('store.home') }}" class="brand">
                    @if ($siteSettings->get('site_logo_path'))
                        <img src="{{ '/storage/'.$siteSettings->get('site_logo_path') }}" alt="{{ $siteSettings->get('site_name', 'RushBite') }}" style="width: 42px; height: 42px; border-radius: 14px; object-fit: cover;">
                    @else
                        <span class="brand-mark">RB</span>
                    @endif
                    <span>{{ $siteSettings->get('site_name', 'RushBite') }}</span>
                </a>
                <nav class="nav">
                    <a href="{{ route('store.home') }}" @class(['active' => request()->routeIs('store.home')])>Menu</a>
                    <a href="{{ route('store.cart.index') }}" @class(['active' => request()->routeIs('store.cart.*')])>Cart</a>
                    @auth
                        <a href="{{ route('store.orders.index') }}" @class(['active' => request()->routeIs('store.orders.*')])>Orders</a>
                        <a href="{{ route('store.profile.edit') }}" @class(['active' => request()->routeIs('store.profile.*')])>Profile</a>
                        @if (auth()->user()->is_admin)
                            <a href="{{ route('admin.dashboard') }}">Admin</a>
                        @endif
                    @endauth
                </nav>
                <div class="actions">
                    @auth
                        <span>{{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="primary">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}">Login</a>
                        <a href="{{ route('register') }}" class="primary">Sign up</a>
                    @endauth
                </div>
            </div>
        </header>

        <main class="shell">
            @if (session('success'))
                <div class="flash">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="errors">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="shell">
            <div>{{ $siteSettings->get('site_name', 'RushBite') }} {{ $siteSettings->get('site_tagline', 'fast food ordering, admin control, and mobile-ready API in one Laravel app.') }}</div>
            @if ($siteSettings->get('contact_phone') || $siteSettings->get('contact_email') || $siteSettings->get('contact_address'))
                <div style="margin-top: 8px;">
                    {{ $siteSettings->get('contact_phone') }}
                    @if ($siteSettings->get('contact_email'))
                        {{ $siteSettings->get('contact_phone') ? ' · ' : '' }}{{ $siteSettings->get('contact_email') }}
                    @endif
                    @if ($siteSettings->get('contact_address'))
                        {{ ($siteSettings->get('contact_phone') || $siteSettings->get('contact_email')) ? ' · ' : '' }}{{ $siteSettings->get('contact_address') }}
                    @endif
                </div>
            @endif
        </footer>
    </body>
</html>
