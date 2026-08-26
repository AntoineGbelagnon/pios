@extends('layouts.app')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div><p class="text-secondary mb-1">Approvisionnement</p><h1 class="h2 mb-0">Nouvelle commande</h1></div>
    <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Retour</a>
</div>
@if ($errors->any())<div class="alert alert-danger">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif
<form method="POST" action="{{ route('purchases.store') }}" id="purchaseForm">
    @csrf
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card pios-panel border-0 mb-4">
                <div class="card-body">
                    <h5 class="mb-3">Informations</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Reference</label>
                            <input type="text" name="reference" class="form-control" value="{{ $reference }}" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Fournisseur *</label>
                            <select name="supplier_id" class="form-select" required>
                                <option value="">Choisir...</option>
                                @foreach($suppliers as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Entrepot</label>
                            <select name="warehouse_id" class="form-select">
                                <option value="">Choisir...</option>
                                @foreach($warehouses as $w)<option value="{{ $w->id }}">{{ $w->name }}</option>@endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card pios-panel border-0 mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Articles</h5>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addItem()"><i class="bi bi-plus"></i> Ajouter</button>
                    </div>
                    <div id="items-container">
                        <div class="row g-2 align-items-end mb-2 item-row">
                            <div class="col-md-5">
                                <label class="form-label small">Produit *</label>
                                <select name="items[0][product_id]" class="form-select form-select-sm" required>
                                    <option value="">Choisir...</option>
                                    @foreach($products as $p)<option value="{{ $p->id }}">{{ $p->name }} (stock: {{ $p->stock_quantity }})</option>@endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">Qte *</label>
                                <input type="number" name="items[0][quantity]" class="form-control form-control-sm" value="1" min="1" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Prix unitaire *</label>
                                <input type="number" name="items[0][unit_price]" class="form-control form-control-sm" value="0" min="0" step="100" required>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.item-row').remove()"><i class="bi bi-trash"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card pios-panel border-0">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2" maxlength="1000"></textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Date livraison prevue</label>
                            <input type="date" name="expected_delivery_date" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card pios-panel border-0">
                <div class="card-body">
                    <h5 class="mb-3">Paiement</h5>
                    <div class="mb-3">
                        <label class="form-label">Mode de paiement *</label>
                        <select name="payment_method" class="form-select" required>
                            <option value="cash">Especes</option>
                            <option value="card">Carte</option>
                            <option value="mobile_money">Mobile Money</option>
                            <option value="bank_transfer">Virement</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Remise</label>
                        <input type="number" name="discount_amount" class="form-control" value="0" min="0" step="100">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">TVA</label>
                        <input type="number" name="tax_amount" class="form-control" value="0" min="0" step="100">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Montant paye *</label>
                        <input type="number" name="amount_paid" class="form-control" value="0" min="0" step="100" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-check-lg"></i> Enregistrer</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
@section('scripts')
<script>
let itemIndex = 1;
function addItem() {
    const container = document.getElementById('items-container');
    const first = container.querySelector('.item-row');
    const clone = first.cloneNode(true);
    clone.querySelectorAll('select, input').forEach(el => {
        el.name = el.name.replace(/\[\d+\]/, `[${itemIndex}]`);
        if (el.type !== 'hidden') el.value = el.tagName === 'SELECT' ? '' : '1';
    });
    container.appendChild(clone);
    itemIndex++;
}
</script>
@endsection
