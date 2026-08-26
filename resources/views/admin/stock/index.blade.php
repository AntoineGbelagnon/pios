@extends('layouts.app')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <p class="text-secondary mb-1">Gestion</p>
            <h1 class="h2 mb-0">Stock</h1>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-primary" href="{{ route('stock.movements') }}">Historique mouvements</a>
            <a class="btn btn-primary" href="{{ route('stock.movement.create') }}">Nouveau mouvement</a>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card pios-panel border-0 h-100">
                <div class="card-body">
                    <p class="text-secondary mb-2">Produits</p>
                    <strong class="fs-2">{{ $totalProducts }}</strong>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card pios-panel border-0 h-100">
                <div class="card-body">
                    <p class="text-secondary mb-2">Valeur du stock</p>
                    <strong class="fs-2">{{ number_format($totalStockValue, 0, ',', ' ') }} <small>FCFA</small></strong>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card pios-panel border-0 h-100">
                <div class="card-body">
                    <p class="text-secondary mb-2">Stock faible</p>
                    <strong class="fs-2 text-warning">{{ $lowStockCount }}</strong>
                    <a class="d-block mt-2" href="{{ route('stock.low') }}">Voir les alertes</a>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card pios-panel border-0 h-100">
                <div class="card-body">
                    <p class="text-secondary mb-2">En rupture</p>
                    <strong class="fs-2 text-danger">{{ $outOfStockCount }}</strong>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card pios-panel border-0 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('stock.index') }}" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <input type="text" class="form-control" name="search" placeholder="Rechercher..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="category_id">
                        <option value="">Toutes categories</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="stock_status">
                        <option value="">Tous stocks</option>
                        <option value="out" @selected(request('stock_status') === 'out')>Rupture</option>
                        <option value="low" @selected(request('stock_status') === 'low')>Stock faible</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button class="btn btn-outline-primary w-100" type="submit">Filtrer</button>
                </div>
                <div class="col-md-1">
                    <a class="btn btn-outline-secondary w-100" href="{{ route('stock.index') }}">Reinit.</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card pios-panel border-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>SKU</th>
                        <th>Produit</th>
                        <th>Categorie</th>
                        <th>Prix achat</th>
                        <th>Stock</th>
                        <th>Seuil</th>
                        <th>Valeur</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td><code>{{ $product->sku }}</code></td>
                            <td class="fw-semibold">{{ $product->name }}</td>
                            <td>{{ $product->category->name ?? '---' }}</td>
                            <td>{{ number_format($product->purchase_price, 0, ',', ' ') }} FCFA</td>
                            <td><strong>{{ $product->stock_quantity }}</strong> {{ $product->unit }}</td>
                            <td>{{ $product->alert_threshold }}</td>
                            <td>{{ number_format($product->stock_quantity * $product->purchase_price, 0, ',', ' ') }} FCFA</td>
                            <td>
                                <span class="badge text-bg-{{ $product->stock_status_color }}">{{ $product->stock_status_label }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-secondary py-4">Aucun produit.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $products->links() }}</div>
    </div>
@endsection
