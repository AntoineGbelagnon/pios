@extends('layouts.app')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <p class="text-secondary mb-1">Stock</p>
            <h1 class="h2 mb-0">Alertes stock faible</h1>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('stock.index') }}">Retour au stock</a>
    </div>

    <div class="card pios-panel border-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>SKU</th>
                        <th>Produit</th>
                        <th>Categorie</th>
                        <th>Stock actuel</th>
                        <th>Seuil alerte</th>
                        <th>Ecart</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td><code>{{ $product->sku }}</code></td>
                            <td class="fw-semibold">{{ $product->name }}</td>
                            <td>{{ $product->category->name ?? '---' }}</td>
                            <td><strong class="text-danger">{{ $product->stock_quantity }}</strong> {{ $product->unit }}</td>
                            <td>{{ $product->alert_threshold }}</td>
                            <td>{{ $product->alert_threshold - $product->stock_quantity }}</td>
                            <td>
                                @if ($product->stock_quantity <= 0)
                                    <span class="badge text-bg-danger">Rupture</span>
                                @else
                                    <span class="badge text-bg-warning">Stock faible</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-secondary py-4">Aucune alerte de stock.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $products->links() }}</div>
    </div>
@endsection
