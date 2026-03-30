<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'Admin Panel' }}</title>
        @stack('styles')
        <style>
            :root {
                --bg: #0e1720;
                --panel: #111f2a;
                --panel-soft: #172835;
                --ink: #edf3f7;
                --muted: #90a5b4;
                --line: rgba(255,255,255,0.09);
                --brand: #f08a4b;
                --accent: #4ec4a5;
                --danger: #ff8f85;
                --shadow: 0 18px 40px rgba(0,0,0,0.28);
                --radius: 22px;
            }

            * { box-sizing: border-box; }
            body {
                margin: 0;
                background:
                    radial-gradient(circle at top right, rgba(240, 138, 75, 0.15), transparent 24%),
                    radial-gradient(circle at bottom left, rgba(78, 196, 165, 0.12), transparent 22%),
                    var(--bg);
                color: var(--ink);
                font-family: "Trebuchet MS", "Segoe UI", sans-serif;
            }
            a { color: inherit; text-decoration: none; }
            .shell { width: min(1240px, calc(100% - 32px)); margin: 0 auto; }
            .layout { display: grid; grid-template-columns: 260px 1fr; gap: 20px; min-height: 100vh; padding: 18px 0 30px; }
            .sidebar, .panel, .card {
                background: linear-gradient(180deg, rgba(23, 40, 53, 0.98), rgba(17, 31, 42, 0.96));
                border: 1px solid var(--line);
                border-radius: var(--radius);
                box-shadow: var(--shadow);
            }
            .sidebar { padding: 24px 18px; position: sticky; top: 18px; height: calc(100vh - 36px); }
            .brand { font-size: 1.1rem; text-transform: uppercase; letter-spacing: 0.14em; color: var(--brand); margin-bottom: 20px; }
            .side-nav { display: grid; gap: 10px; }
            .side-nav a {
                padding: 12px 14px;
                border-radius: 16px;
                color: var(--muted);
                border: 1px solid transparent;
            }
            .side-nav a.active, .side-nav a:hover {
                color: var(--ink);
                background: rgba(255,255,255,0.04);
                border-color: var(--line);
            }
            .content { padding-top: 4px; }
            .topbar { display: flex; justify-content: space-between; gap: 18px; align-items: center; margin-bottom: 20px; }
            .panel { padding: 22px; }
            .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; }
            .card { padding: 18px; }
            h1, h2, h3 { margin: 0 0 10px; }
            h1 { font-size: clamp(2rem, 4vw, 3.2rem); }
            p { margin: 0; color: var(--muted); line-height: 1.6; }
            .stat { font-size: 2rem; font-weight: 700; margin-top: 10px; }
            .button {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                border-radius: 999px;
                padding: 11px 16px;
                border: 1px solid transparent;
                background: var(--brand);
                color: #fff;
                cursor: pointer;
                font: inherit;
            }
            .button.secondary {
                background: transparent;
                border-color: var(--line);
                color: var(--ink);
            }
            .button.danger {
                background: rgba(255, 143, 133, 0.14);
                border-color: rgba(255, 143, 133, 0.2);
                color: var(--danger);
            }
            .header-actions, .toolbar { display: flex; gap: 12px; align-items: center; flex-wrap: wrap; }
            .flash, .errors {
                padding: 14px 16px;
                border-radius: 16px;
                margin-bottom: 16px;
            }
            .flash { background: rgba(78, 196, 165, 0.13); color: #9de5d1; }
            .errors { background: rgba(255, 143, 133, 0.13); color: #ffc5bf; }
            table { width: 100%; border-collapse: collapse; }
            th, td { padding: 14px 12px; border-bottom: 1px solid var(--line); text-align: left; vertical-align: top; }
            .meta { color: var(--muted); font-size: 0.92rem; }
            .badge {
                display: inline-block;
                padding: 6px 10px;
                border-radius: 999px;
                background: rgba(255,255,255,0.06);
                color: var(--ink);
                font-size: 0.8rem;
            }
            .badge.good { background: rgba(78, 196, 165, 0.18); color: #a9f0de; }
            .badge.warn { background: rgba(240, 138, 75, 0.18); color: #ffd0b1; }
            label { display: grid; gap: 8px; }
            input, textarea, select {
                width: 100%;
                padding: 13px 14px;
                border-radius: 16px;
                border: 1px solid var(--line);
                background: rgba(255,255,255,0.03);
                color: var(--ink);
                font: inherit;
            }
            textarea { min-height: 140px; resize: vertical; }
            .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
            .span-2 { grid-column: span 2; }
            .inline { display: inline; }
            @media (max-width: 980px) {
                .layout { grid-template-columns: 1fr; }
                .sidebar { position: static; height: auto; }
                .form-grid { grid-template-columns: 1fr; }
                .span-2 { grid-column: auto; }
            }
        </style>
    </head>
    <body>
        <div class="shell layout">
            <aside class="sidebar">
                <div class="brand">{{ $siteSettings->get('site_name', 'RushBite') }} Admin</div>
                <div class="side-nav">
                    <a href="{{ route('admin.dashboard') }}" @class(['active' => request()->routeIs('admin.dashboard')])>Dashboard</a>
                    <a href="{{ route('admin.categories.index') }}" @class(['active' => request()->routeIs('admin.categories.*')])>Categories</a>
                    <a href="{{ route('admin.products.index') }}" @class(['active' => request()->routeIs('admin.products.*')])>Products</a>
                    <a href="{{ route('admin.orders.index') }}" @class(['active' => request()->routeIs('admin.orders.*')])>Orders</a>
                    <a href="{{ route('admin.settings.edit') }}" @class(['active' => request()->routeIs('admin.settings.*')])>Settings</a>
                    <a href="{{ route('store.home') }}">Storefront</a>
                </div>
            </aside>
            <div class="content">
                <div class="topbar panel">
                    <div>
                        <div class="meta">Signed in as {{ auth()->user()->name }}</div>
                        <h2>{{ $title ?? 'Dashboard' }}</h2>
                    </div>
                    <div class="header-actions">
                        <a href="{{ route('admin.products.create') }}" class="button">New Product</a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="button secondary">Logout</button>
                        </form>
                    </div>
                </div>

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
            </div>
        </div>
        @stack('scripts')
    </body>
</html>
