@extends('layouts.app')
@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <p class="text-secondary mb-1">Finance</p>
            <h1 class="h2 mb-0">Nouvelle depense</h1>
        </div>
        <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Retour</a>
    </div>
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card pios-panel border-0">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('expenses.store') }}">
                        @csrf
                        <div class="mb-3"><label class="form-label">Categorie *</label>
                            <select name="category" class="form-select" required>
                                <option value="">Choisir...</option>
                                @foreach ($categories as $c)
                                    <option value="{{ $c }}">{{ ucfirst($c) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3"><label class="form-label">Montant (FCFA) *</label><input type="number"
                                name="amount" class="form-control" min="0.01" step="100" required></div>
                        <div class="mb-3"><label class="form-label">Description *</label><input type="text"
                                name="description" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label">Date *</label><input type="date"
                                name="expense_date" class="form-control" value="{{ date('Y-m-d') }}" required></div>
                        <div class="mb-3"><label class="form-label">Mode de paiement *</label>
                            <select name="payment_method" class="form-select" required>
                                <option value="cash">Especes</option>
                                <option value="card">Carte</option>
                                <option value="mobile_money">Mobile Money</option>
                                <option value="bank_transfer">Virement</option>
                            </select>
                        </div>
                        @if ($cashRegister)
                            <input type="hidden" name="cash_register_id" value="{{ $cashRegister->id }}">
                        @endif
                        <div class="mb-3"><label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Enregistrer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
