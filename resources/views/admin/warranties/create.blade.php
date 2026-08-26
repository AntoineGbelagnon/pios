@extends('layouts.app')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div><p class="text-secondary mb-1">SAV</p><h1 class="h2 mb-0">Nouvelle garantie</h1></div>
    <a href="{{ route('warranties.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Retour</a>
</div>
@if ($errors->any())<div class="alert alert-danger">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card pios-panel border-0">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('warranties.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Client *</label>
                        <select name="customer_id" class="form-select" required>
                            <option value="">Choisir un client...</option>
                            @foreach(\App\Models\Customer::active()->orderBy('name')->get() as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Produit *</label>
                        <select name="product_id" class="form-select" required>
                            <option value="">Choisir un produit...</option>
                            @foreach(\App\Models\Product::active()->orderBy('name')->get() as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3"><label class="form-label">Numero de serie</label><input type="text" name="serial_number" class="form-control" maxlength="100" placeholder="Ex: SN-12345"></div>
                    <div class="mb-3"><label class="form-label">Date d'achat *</label><input type="date" name="purchase_date" class="form-control" value="{{ date('Y-m-d') }}" required></div>
                    <div class="mb-3"><label class="form-label">Duree (mois) *</label><input type="number" name="duration_months" class="form-control" value="12" min="1" required></div>
                    <div class="mb-3"><label class="form-label">Description du probleme</label><textarea name="problem_description" class="form-control" rows="3" maxlength="1000" placeholder="Decrivez le probleme..."></textarea></div>
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-check-lg"></i> Enregistrer</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
