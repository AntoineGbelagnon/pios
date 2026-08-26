@extends('layouts.app')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <p class="text-secondary mb-1">Gestion</p>
            <h1 class="h2 mb-0">{{ $supplier->exists ? 'Modifier le fournisseur' : 'Nouveau fournisseur' }}</h1>
        </div>
        <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
    </div>

    <form method="POST" action="{{ $supplier->exists ? route('suppliers.update', $supplier) : route('suppliers.store') }}">
        @csrf
        @if($supplier->exists)
            @method('PUT')
        @endif

        <div class="row">
            <div class="col-lg-8">
                <div class="card pios-panel border-0 mb-4">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0">Informations générales</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nom / Raison sociale <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $supplier->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="contact_name" class="form-label">Personne de contact</label>
                                <input type="text" class="form-control" id="contact_name" name="contact_name" value="{{ old('contact_name', $supplier->contact_name) }}">
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $supplier->email) }}">
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Téléphone</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $supplier->phone) }}">
                            </div>
                            <div class="col-md-6">
                                <label for="secondary_phone" class="form-label">Téléphone secondaire</label>
                                <input type="text" class="form-control" id="secondary_phone" name="secondary_phone" value="{{ old('secondary_phone', $supplier->secondary_phone) }}">
                            </div>
                            <div class="col-12">
                                <label for="address" class="form-label">Adresse</label>
                                <textarea class="form-control" id="address" name="address" rows="2">{{ old('address', $supplier->address) }}</textarea>
                            </div>
                            <div class="col-md-4">
                                <label for="city" class="form-label">Ville</label>
                                <input type="text" class="form-control" id="city" name="city" value="{{ old('city', $supplier->city) }}">
                            </div>
                            <div class="col-md-4">
                                <label for="country" class="form-label">Pays</label>
                                <input type="text" class="form-control" id="country" name="country" value="{{ old('country', $supplier->country) }}">
                            </div>
                            <div class="col-md-4">
                                <label for="tax_id" class="form-label">NIF / RCCM</label>
                                <input type="text" class="form-control" id="tax_id" name="tax_id" value="{{ old('tax_id', $supplier->tax_id) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card pios-panel border-0 mb-4">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0">Conditions de paiement</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="payment_terms" class="form-label">Conditions de paiement</label>
                                <input type="text" class="form-control" id="payment_terms" name="payment_terms" value="{{ old('payment_terms', $supplier->payment_terms) }}" placeholder="Ex: 30 jours">
                            </div>
                            <div class="col-md-6">
                                <label for="next_payment_date" class="form-label">Prochaine échéance</label>
                                <input type="date" class="form-control" id="next_payment_date" name="next_payment_date" value="{{ old('next_payment_date', $supplier->next_payment_date?->format('Y-m-d')) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card pios-panel border-0 mb-4">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0">Notes</h5>
                    </div>
                    <div class="card-body">
                        <textarea class="form-control" name="notes" rows="3" placeholder="Notes sur le fournisseur...">{{ old('notes', $supplier->notes) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card pios-panel border-0 mb-4">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0">Informations financières</h5>
                    </div>
                    <div class="card-body">
                        @if($supplier->exists)
                            <div class="mb-3">
                                <label class="form-label">Total achats</label>
                                <div class="form-control bg-light">{{ number_format($supplier->total_purchases, 0, ',', ' ') }} FCFA</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Dettes actuelles</label>
                                <div class="form-control bg-light {{ $supplier->total_debt > 0 ? 'text-danger' : 'text-success' }}">
                                    {{ number_format($supplier->total_debt, 0, ',', ' ') }} FCFA
                                </div>
                            </div>
                        @else
                            <p class="text-secondary mb-0">Les informations financières seront disponibles après la première transaction.</p>
                        @endif
                    </div>
                </div>

                <div class="card pios-panel border-0 mb-4">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0">Statut</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $supplier->is_active ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Fournisseur actif</label>
                        </div>
                    </div>
                </di
