@extends('layouts.app')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <p class="text-secondary mb-1">Gestion</p>
            <h1 class="h2 mb-0">{{ $customer->exists ? 'Modifier le client' : 'Nouveau client' }}</h1>
        </div>
        <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
    </div>

    <form method="POST" action="{{ $customer->exists ? route('customers.update', $customer) : route('customers.store') }}">
        @csrf
        @if($customer->exists)
            @method('PUT')
        @endif

        <div class="row">
            <div class="col-lg-8">
                <div class="card pios-panel border-0 mb-4">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0">Informations personnelles</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nom complet <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $customer->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="customer_type" class="form-label">Type de client <span class="text-danger">*</span></label>
                                <select class="form-select @error('customer_type') is-invalid @enderror" id="customer_type" name="customer_type" required>
                                    <option value="individual" {{ old('customer_type', $customer->customer_type) === 'individual' ? 'selected' : '' }}>Particulier</option>
                                    <option value="professional" {{ old('customer_type', $customer->customer_type) === 'professional' ? 'selected' : '' }}>Professionnel</option>
                                </select>
                                @error('customer_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $customer->email) }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Téléphone</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $customer->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="secondary_phone" class="form-label">Téléphone secondaire</label>
                                <input type="text" class="form-control @error('secondary_phone') is-invalid @enderror" id="secondary_phone" name="secondary_phone" value="{{ old('secondary_phone', $customer->secondary_phone) }}">
                            </div>
                            <div class="col-12">
                                <label for="address" class="form-label">Adresse</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="2">{{ old('address', $customer->address) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="city" class="form-label">Ville</label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" name="city" value="{{ old('city', $customer->city) }}">
                            </div>

                            {{-- Professional fields --}}
                            <div class="col-md-6" id="professional_fields" style="display: {{ old('customer_type', $customer->customer_type) === 'professional' ? 'block' : 'none' }}">
                                <label for="company_name" class="form-label">Raison sociale</label>
                                <input type="text" class="form-control" id="company_name" name="company_name" value="{{ old('company_name', $customer->company_name) }}">
                            </div>
                            <div class="col-md-6" id="tax_id_field" style="display: {{ old('customer_type', $customer->customer_type) === 'professional' ? 'block' : 'none' }}">
                                <label for="tax_id" class="form-label">NIF / RCCM</label>
                                <input type="text" class="form-control" id="tax_id" name="tax_id" value="{{ old('tax_id', $customer->tax_id) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card pios-panel border-0 mb-4">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0">Notes</h5>
                    </div>
                    <div class="card-body">
                        <textarea class="form-control" name="notes" rows="3" placeholder="Notes sur le client...">{{ old('notes', $customer->notes) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card pios-panel border-0 mb-4">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0">Crédit & Fidélité</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="credit_limit" class="form-label">Limite de crédit (FCFA)</label>
                            <input type="number" class="form-control" id="credit_limit" name="credit_limit" value="{{ old('credit_limit', $customer->credit_limit) }}" min="0" step="1000">
                        </div>
                        @if($customer->exists)
                            <div class="mb-3">
                                <label class="form-label">Dettes actuelles</label>
                                <div class="form-control bg-light">{{ number_format($customer->total_debt, 0, ',', ' ') }} FCFA</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Crédit disponible</label>
                                <div class="form-control bg-light {{ $customer->remaining_credit < 0 ? 'text-danger' : 'text-success' }}">
                                    {{ number_format($customer->remaining_credit, 0, ',', ' ') }} FCFA
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card pios-panel border-0 mb-4">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0">Statut</h5>
                    </div>
                    <div class="card-body">
                 
