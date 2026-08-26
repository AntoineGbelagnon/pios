@extends('layouts.app')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div><p class="text-secondary mb-1">Stock</p><h1 class="h2 mb-0">Inventaire physique</h1></div>
    <a href="{{ route('stock.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Retour</a>
</div>

@if (session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif

<div class="alert alert-info">
    <i class="bi bi-info-circle"></i> Saisissez la quantite reellement comptee pour chaque produit. Les ecarts seront automatiquement corriges dans le stock.
</div>

<div class="card pios-panel border-0 mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-5"><input type="text" name="search" class="form-control" placeholder="Rechercher un produit..." value="{{ request('search') }}"></div>
            <div class="col-md-3">
                <select name="category_id" class="form-select">
                    <option value="">Toutes categories</option>
                    @foreach($categories as $cat)<option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>{{ $cat->name }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-2"><button type="submit" class="btn btn-outline-primary w-100">Filtrer</button></div>
        </form>
    </div>
</div>

<form method="POST" action="{{ route('stock.inventory.store') }}" id="inventoryForm">
    @csrf
    <div class="card pios-panel border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>SKU</th>
                        <th>Categorie</th>
                        <th class="text-center">Stock systeme</th>
                        <th class="text-center" style="width:150px;">Qte comptee</th>
                        <th class="text-center">Ecart</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $i => $product)
                    <tr>
                        <td class="fw-semibold">{{ $product->name }}</td>
                        <td class="text-secondary">{{ $product->sku }}</td>
                        <td>{{ $product->category->name ?? '-' }}</td>
                        <td class="text-center">
                            <span class="badge text-bg-secondary">{{ $product->stock_quantity }}</span>
                        </td>
                        <td>
                            <input type="hidden" name="items[{{ $i }}][product_id]" value="{{ $product->id }}">
                            <input type="number" name="items[{{ $i }}][counted_quantity]" class="form-control form-control-sm text-center counted-input"
                                   value="{{ $product->stock_quantity }}" min="0" data-expected="{{ $product->stock_quantity }}">
                        </td>
                        <td class="text-center">
                            <span class="diff-badge" data-idx="{{ $i }}">0</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-secondary py-4">Aucun produit</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($products->isNotEmpty())
        <div class="card-footer d-flex justify-content-between align-items-center">
            <div>
                <strong id="summaryCorrections">0</strong> correction(s) detectee(s)
                &middot; <strong id="summaryTotalItems">{{ $products->count() }}</strong> produit(s)
            </div>
            <button type="submit" class="btn btn-warning" onclick="return confirm('Valider l\\'inventaire ? Les ecarts seront corriges.')">
                <i class="bi bi-check-lg"></i> Valider l'inventaire
            </button>
        </div>
        @endif
    </div>
</form>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var inputs = document.querySelectorAll('.counted-input');
    function updateDiffs() {
        var corrections = 0;
        inputs.forEach(function(input) {
            var expected = parseInt(input.dataset.expected);
            var counted = parseInt(input.value) || 0;
            var diff = counted - expected;
            var badge = input.closest('tr').querySelector('.diff-badge');
            if (diff === 0) {
                badge.textContent = '0';
                badge.className = 'diff-badge badge text-bg-secondary';
            } else {
                badge.textContent = (diff > 0 ? '+' : '') + diff;
                badge.className = 'diff-badge badge text-bg-' + (diff > 0 ? 'success' : 'danger');
                corrections++;
            }
        });
        document.getElementById('summaryCorrections').textContent = corrections;
    }
    inputs.forEach(function(input) { input.addEventListener('input', updateDiffs); });
});
</script>
@endsection
