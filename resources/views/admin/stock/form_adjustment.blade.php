@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-xl-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <p class="text-secondary mb-1">Stock</p>
                    <h1 class="h2 mb-0">Ajustement de stock</h1>
                </div>
                <a class="btn btn-outline-secondary" href="{{ route('stock.index') }}">Retour</a>
            </div>

            <div class="card pios-panel border-0">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('stock.adjustment.store') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label" for="product_id">Produit *</label>
                                <select class="form-select" id="product_id" name="product_id" required>
                                    <option value="">Selectionner un produit...</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}" @selected(old('product_id') == $product->id)>{{ $product->name }} ({{ $product->sku }}) - Stock actuel: {{ $product->stock_quantity }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="new_quantity">Nouvelle quantite *</label>
                                <input class="form-control" type="number" id="new_quantity" name="new_quantity" value="{{ old('new_quantity', 0) }}" min="0" required>
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
                                <label class="form-label" for="reason">Motif de l'ajustement *</label>
                                <textarea class="form-control" id="reason" name="reason" rows="2" required>{{ old('reason') }}</textarea>
                            </div>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger mt-4">{{ $errors->first() }}</div>
                        @endif

                        <button class="btn btn-warning mt-4" type="submit">Effectuer l'ajustement</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
