@extends('layouts.app')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <p class="text-secondary mb-1">Gestion</p>
            <h1 class="h2 mb-0">Produits</h1>
        </div>
        <a class="btn btn-primary" href="{{ route('products.create') }}">Nouveau produit</a>
    </div>
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    {{-- Filters --}}
    <div class="card pios-panel border-0 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('products.index') }}" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <input type="text" class="form-control" name="search" placeholder="Rechercher (nom, SKU, code-barres)..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="category_id">
                        <option value="">Toutes catégories</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="brand_id">
                        <option value="">Toutes marques</option>
                        @foreach ($brands as $brand)
                            <option value="{{ $brand->id }}" @selected(request('brand_id') == $brand->id)>{{ $brand->name }}</option>
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
                    <a class="btn btn-outline-secondary w-100" href="{{ route('products.index') }}">Réinit.</a>
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
                        <th>Catégorie</th>
                        <th>Marque</th>
                        <th>Prix vente</th>
                        <th>Stock</th>
                        <th>Statut</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td><code>{{ $product->sku }}</code></td>
                            <td class="fw-semibold">{{ $product->name }}</td>
                            <td>{{ $product->category->name ?? '—' }}</td>
                            <td>{{ $product->brand->name ?? '—' }}</td>
                            <td>{{ number_format($product->sale_price, 0, ',', ' ') }} FCFA</td>
                            <td>
                                <span class="badge text-bg-{{ $product->stock_status_color }}">{{ $product->stock_quantity }}</span>
                            </td>
                            <td>
                                <span class="badge text-bg-{{ $product->is_active ? 'success' : 'secondary' }}">
                                    {{ $product->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('products.edit', $product) }}">Modifier</a>
                                <form class="d-inline" method="POST" action="{{ route('products.destroy', $product) }}">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit" onclick="return confirm('Supprimer ce produit ?')">Supprimer</button>
                                </form>
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
