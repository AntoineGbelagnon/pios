<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') · {{ config('app.name', 'PIOS') }}</title>
    @if (file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>

<body>
    <div class="pios-app-shell">
        <aside class="pios-sidebar" id="piosSidebar">
            <div class="pios-sidebar-brand">
                <a class="pios-brand text-decoration-none" href="{{ route('dashboard') }}">
                    <span class="pios-brand-mark" aria-hidden="true">P</span>
                    <span>PIOS<span class="text-primary">.</span></span>
                </a>
                <button class="pios-sidebar-close" type="button" data-sidebar-toggle aria-label="Fermer le menu">×</button>
            </div>

            <p class="pios-nav-label">Menu principal</p>
            <nav class="nav flex-column pios-nav" aria-label="Navigation principale">
                @can('view dashboard')
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><span class="pios-nav-icon">⌂</span>Tableau de bord</a>
                @endcan
                @can('manage sales')
                    <a class="nav-link {{ request()->routeIs('sales.pos') ? 'active' : '' }}" href="{{ route('sales.pos') }}"><span class="pios-nav-icon">＋</span>Nouvelle vente</a>
                    <a class="nav-link {{ request()->routeIs('sales.index', 'sales.show', 'sales.receipt') ? 'active' : '' }}" href="{{ route('sales.index') }}"><span class="pios-nav-icon">▤</span>Historique ventes</a>
                @endcan

                <p class="pios-nav-label mt-4">Catalogue & stock</p>
                @can('manage products')
                    <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}"><span class="pios-nav-icon">□</span>Produits</a>
                    <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}"><span class="pios-nav-icon">◇</span>Categories</a>
                    <a class="nav-link {{ request()->routeIs('brands.*') ? 'active' : '' }}" href="{{ route('brands.index') }}"><span class="pios-nav-icon">◉</span>Marques</a>
                @endcan
                @can('manage stocks')
                    <a class="nav-link {{ request()->routeIs('stock.*') ? 'active' : '' }}" href="{{ route('stock.index') }}"><span class="pios-nav-icon">▦</span>Stock & inventaire</a>
                @endcan
                @can('manage purchases')
                    <a class="nav-link {{ request()->routeIs('purchases.*') ? 'active' : '' }}" href="{{ route('purchases.index') }}"><span class="pios-nav-icon">⇣</span>Approvisionnements</a>
                @endcan

                <p class="pios-nav-label mt-4">Gestion</p>
                @can('manage customers')
                    <a class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}" href="{{ route('customers.index') }}"><span class="pios-nav-icon">♙</span>Clients</a>
                @endcan
                @can('manage invoices')
                    <a class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}" href="{{ route('suppliers.index') }}"><span class="pios-nav-icon">♜</span>Fournisseurs</a>
                @endcan
                @can('manage cash')
                    <a class="nav-link {{ request()->routeIs('cash_registers.*') ? 'active' : '' }}" href="{{ route('cash_registers.index') }}"><span class="pios-nav-icon">₣</span>Caisse</a>
                @endcan
                @can('manage expenses')
                    <a class="nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}" href="{{ route('expenses.index') }}"><span class="pios-nav-icon">↗</span>Depenses</a>
                @endcan
                @can('manage returns')
                    <a class="nav-link {{ request()->routeIs('returns.*') ? 'active' : '' }}" href="{{ route('returns.index') }}"><span class="pios-nav-icon">↶</span>Retours</a>
                @endcan
                @can('manage warranties')
                    <a class="nav-link {{ request()->routeIs('warranties.*') ? 'active' : '' }}" href="{{ route('warranties.index') }}"><span class="pios-nav-icon">✓</span>Garantie / SAV</a>
                @endcan
                @can('manage settings')
                    <a class="nav-link {{ request()->routeIs('statistics.*') ? 'active' : '' }}" href="{{ route('statistics.index') }}"><span class="pios-nav-icon">⌁</span>Statistiques</a>
                @endcan

                <p class="pios-nav-label mt-4">Administration</p>
                @can('manage settings')
                    <a class="nav-link {{ request()->routeIs('shops.*', 'warehouses.*') ? 'active' : '' }}" href="{{ route('shops.index') }}"><span class="pios-nav-icon">⌂</span>Boutiques</a>
                @endcan
                @can('manage users')
                    <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}"><span class="pios-nav-icon">♚</span>Utilisateurs</a>
                @endcan
                @can('manage settings')
                    <a class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.index') }}"><span class="pios-nav-icon">⚙</span>Parametres</a>
                @endcan
                @can('view audit log')
                    <a class="nav-link {{ request()->routeIs('activity.*') ? 'active' : '' }}" href="{{ route('activity.index') }}"><span class="pios-nav-icon">≡</span>Journal d'activite</a>
                @endcan
            </nav>

            <div class="pios-sidebar-footer">
                <div class="pios-user-mini">
                    <span class="pios-avatar">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                    <span class="overflow-hidden">
                        <strong class="d-block text-truncate">{{ auth()->user()->name }}</strong>
                        <small class="text-secondary d-block text-truncate">{{ auth()->user()->email }}</small>
                    </span>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-light btn-sm w-100" type="submit">Se deconnecter</button>
                </form>
            </div>
        </aside>

        <div class="pios-sidebar-backdrop" data-sidebar-toggle></div>

        <section class="pios-workspace">
            <header class="pios-topbar">
                <div class="d-flex align-items-center gap-3">
                    <button class="pios-icon-button" type="button" data-sidebar-toggle aria-label="Afficher le menu">☰</button>
                    <div>
                        <p class="pios-topbar-eyebrow mb-0">Espace de gestion</p>
                        <strong class="pios-topbar-title">@yield('page-title', 'Dashboard')</strong>
                    </div>
                </div>

                <div class="pios-topbar-search">
                    <span aria-hidden="true">⌕</span>
                    <input type="search" aria-label="Recherche globale" placeholder="Rechercher dans PIOS...">
                </div>

                <div class="pios-topbar-actions">
                    <a class="pios-icon-button position-relative" href="{{ route('notifications.index') }}" aria-label="Notifications">
                        ♧<span class="pios-notification-dot"></span>
                    </a>
                    <div class="pios-profile">
                        <span class="pios-avatar">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                        <span class="d-none d-md-block">
                            <strong class="d-block">{{ auth()->user()->name }}</strong>
                            <small>{{ auth()->user()->roles->first()?->name ?? 'Utilisateur' }}</small>
                        </span>
                    </div>
                </div>
            </header>

            <main class="pios-main">@yield('content')</main>
        </section>
    </div>
</body>

</html>
