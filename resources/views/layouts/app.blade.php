<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'PIOS') }}</title>
    @if (file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>

<body>
    <div class="container-fluid">
        <div class="row min-vh-100">
            <aside class="col-12 col-lg-2 p-4 p-lg-3 pios-sidebar">
                <a class="pios-brand text-decoration-none fs-4" href="{{ route('dashboard') }}">PIOS</a>
                <nav class="nav flex-column gap-1 mt-4" aria-label="Navigation principale">
                    @can('view dashboard')
                        <a class="nav-link" href="{{ route('dashboard') }}">Tableau de bord</a>
                    @endcan
                    @can('manage sales')
                        <a class="nav-link" href="{{ route('sales.pos') }}">Ventes (POS)</a>
                        <a class="nav-link ps-4" href="{{ route('sales.index') }}">Historique ventes</a>
                    @endcan
                    @can('manage products')
                        <a class="nav-link" href="{{ route('products.index') }}">Produits</a>
                        <a class="nav-link ps-4" href="{{ route('categories.index') }}">Categories</a>
                        <a class="nav-link ps-4" href="{{ route('brands.index') }}">Marques</a>
                    @endcan
                    @can('manage stocks')
                        <a class="nav-link" href="{{ route('stock.index') }}">Stock</a>
                        <a class="nav-link ps-4" href="{{ route('stock.movements') }}">Mouvements</a>
                        <a class="nav-link ps-4" href="{{ route('stock.low') }}">Alertes stock</a>
                        <a class="nav-link ps-4" href="{{ route('stock.inventory') }}">Inventaire physique</a>
                    @endcan
                    @can('manage purchases')
                        <a class="nav-link" href="{{ route('purchases.index') }}">Approvisionnements</a>
                    @endcan
                    @can('manage customers')
                        <a class="nav-link" href="{{ route('customers.index') }}">Clients</a>
                    @endcan
                    @can('manage invoices')
                        <a class="nav-link" href="{{ route('suppliers.index') }}">Fournisseurs</a>
                    @endcan
                    @can('manage cash')
                        <a class="nav-link" href="{{ route('cash_registers.index') }}">Caisse</a>
                    @endcan
                    @can('manage expenses')
                        <a class="nav-link" href="{{ route('expenses.index') }}">Depenses</a>
                    @endcan
                    @can('manage returns')
                        <a class="nav-link" href="{{ route('returns.index') }}">Retours</a>
                    @endcan
                    @can('manage warranties')
                        <a class="nav-link" href="{{ route('warranties.index') }}">Garantie/SAV</a>
                    @endcan
                    @can('manage settings')
                        <a class="nav-link" href="{{ route('statistics.index') }}">Statistiques</a>
                    @endcan
                    @can('manage settings')
                        <a class="nav-link" href="{{ route('shops.index') }}">Boutiques</a>
                    @endcan
                    @can('manage users')
                        <a class="nav-link" href="{{ route('users.index') }}">Utilisateurs</a>
                    @endcan
                    @can('manage settings')
                        <a class="nav-link" href="{{ route('settings.index') }}">Parametres</a>
                    @endcan
                    @can('view audit log')
                        <a class="nav-link" href="{{ route('activity.index') }}">Journal d'activite</a>
                    @endcan
                </nav>
            </aside>
            <main class="col-12 col-lg-10 p-4 p-lg-5">@yield('content')</main>
        </div>
    </div>
</body>

</html>
