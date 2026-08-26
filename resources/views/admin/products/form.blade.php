@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <p class="text-secondary mb-1">Produits</p>
                    <h1 class="h2 mb-0">{{ $product->exists ? 'Modifier le produit' : 'Nouveau produit' }}</h1>
                </div>
                <a class="btn btn-outline-secondary" href="{{ route('products.index') }}">Retour</a>
            </div>
            <form method="POST" action="{{ $product->exists ? route('products.update', $product) : route('products.store') }}">
                @csrf
                @if ($product->exists)
                    @method('PUT')
                @endif
                <div class="card pios-panel border-0 mb-4">
                    <div class="card-body p-4">
                        <h2 class="h5 mb-3">Informations generales</h2>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="name">Nom du produit *</label>
                                <input class="form-control" id="name" name="name" value="{{ old('name', $product->name) }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" for="sku">SKU</label>
                                <input class="form-control" id="sku" name="sku" value="{{ old('sku', $product->sku) }}" placeholder="Auto-genere si vide">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" for="barcode">Code-barres</label>
                                <input class="form-control" id="barcode" name="barcode" value="{{ old('barcode', $product->barcode) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="category_id">Categorie *</label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="">Selectionner...</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}" @selected(old('category_id', $product->category_id) == $cat->id)>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="brand_id">Marque</label>
                                <select class="form-select" id="brand_id" name="brand_id">
                                    <option value="">Aucune</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}" @selected(old('brand_id', $product->brand_id) == $brand->id)>{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="2">{{ old('description', $product->description) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card pios-panel border-0 h-100">
                            <div class="card-body p-4">
                                <h2 class="h5 mb-3">Prix</h2>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label" for="purchase_price">Prix d'achat (FCFA) *</label>
                                        <input class="form-control" type="number" step="0.01" id="purchase_price" name="purchase_price" value="{{ old('purchase_price', $product->purchase_price) }}" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" for="sale_price">Prix de vente (FCFA) *</label>
                                        <input class="form-control" type="number" step="0.01" id="sale_price" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" for="promo_price">Prix promotionnel (FCFA)</label>
                                        <input class="form-control" type="number" step="0.01" id="promo_price" name="promo_price" value="{{ old('promo_price', $product->promo_price) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card pios-panel border-0 h-100">
                            <div class="card-body p-4">
                                <h2 class="h5 mb-3">Stock</h2>
                                <div class="row g-3">
                                    <div class="col-6">
                                        <label class="form-label" for="stock_quantity">Quantite en stock *</label>
                                        <input class="form-control" type="number" id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label" for="alert_threshold">Seuil d'alerte *</label>
                                        <input class="form-control" type="number" id="alert_threshold" name="alert_threshold" value="{{ old('alert_threshold', $product->alert_threshold) }}" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label" for="unit">Unite</label>
                                        <input class="form-control" id="unit" name="unit" value="{{ old('unit', $product->unit ?: 'piece') }}">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label" for="warranty_months">Garantie (mois)</label>
                                        <input class="form-control" type="number" id="warranty_months" name="warranty_months" value="{{ old('warranty_months', $product->warranty_months) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card pios-panel border-0 mt-4">
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label" for="notes">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="2">{{ old('notes', $product->notes) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_serialized" name="is_serialized" value="1" @checked(old('is_serialized', $product->is_serialized))>
                                    <label class="form-check-label" for="is_serialized">Produit serialise (numero de serie)</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $product->is_active ?? true))>
                                    <label class="form-check-label" for="is_active">Produit actif</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger mt-4">{{ $errors->first() }}</div>
                @endif

                <button class="btn btn-primary mt-4" type="submit">Enregistrer le produit</button>
            </form>
        </div>
    </div>
@endsection
