@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-xl-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <p class="text-secondary mb-1">Stock</p>
                    <h1 class="h2 mb-0">Nouveau mouvement de stock</h1>
                </div>
                <a class="btn btn-outline-secondary" href="{{ route('stock.movements') }}">Retour</a>
            </div>

            <div class="card pios-panel border-0">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('stock.movement.store') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label" for="product_id">Produit *</label>
                                <select class="form-select" id="product_id" name="product_id" required>
                                    <option value="">Selectionner un produit...</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}" @selected(old('product_id') == $product->id)>{{ $product->name }} ({{ $product->sku }}) - Stock: {{ $product->stock_quantity }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="type">Type de mouvement *</label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="entry" @selected(old('type') === 'entry')>Entree</option>
                                    <option value="exit" @selected(old('type') === 'exit')>Sortie</option>
                                    <option value="adjustment" @selected(old('type') === 'adjustment')>Ajustement</option>
                                    <option value="transfer" @selected(old('type') === 'transfer')>Transfert</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="quantity">Quantite *</label>
                                <input class="form-control" type="number" id="quantity" name="quantity" value="{{ old('quantity', 1) }}" min="1" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="unit_price">Prix unitaire (FCFA)</label>
                                <input class="form-control" type="number" step="0.01" id="unit_price" name="unit_price" value="{{ old('unit_price') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="warehouse_id">Entrepot</label>
                                <select class="form-select" id="warehouse_id" name="warehouse_id">
                                    <option value="">Entrepot principal</option>
                                    @foreach ($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" @selected(old('warehouse_id') == $warehouse->id)>{{ $warehouse->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="notes">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger mt-4">{{ $errors->first() }}</div>
                        @endif

                        <button class="btn btn-primary mt-4" type="submit">Enregistrer le mouvement</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
