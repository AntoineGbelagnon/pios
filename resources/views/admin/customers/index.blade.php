@extends('layouts.app')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <p class="text-secondary mb-1">Gestion</p>
            <h1 class="h2 mb-0">Clients</h1>
        </div>
        <a href="{{ route('customers.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Nouveau client
        </a>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card pios-panel border-0 h-100">
                <div class="card-body">
                    <p class="text-secondary mb-2">Total clients</p>
                    <strong class="fs-2">{{ $stats['total'] }}</strong>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card pios-panel border-0 h-100">
                <div class="card-body">
                    <p class="text-secondary mb-2">Clients actifs</p>
                    <strong class="fs-2 text-success">{{ $stats['active'] }}</strong>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card pios-panel border-0 h-100">
                <div class="card-body">
                    <p class="text-secondary mb-2">Professionnels</p>
                    <strong class="fs-2 text-primary">{{ $stats['professionals'] }}</strong>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card pios-panel border-0 h-100">
                <div class="card-body">
                    <p class="text-secondary mb-2">Dettes totales</p>
                    <strong class="fs-2 text-danger">{{ number_format($stats['totalDebt'], 0, ',', ' ') }} FCFA</strong>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card pios-panel border-0 mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Rechercher un client..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="customer_type" class="form-select">
                        <option value="">Tous les types</option>
                        <option value="individual" {{ request('customer_type') === 'individual' ? 'selected' : '' }}>Particulier</option>
                        <option value="professional" {{ request('customer_type') === 'professional' ? 'selected' : '' }}>Professionnel</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="is_active" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Actif</option>
                        <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactif</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">Filtrer</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Customers Table --}}
    <div class="card pios-panel border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Contact</th>
                        <th>Type</th>
                        <th>Crédit</th>
                        <th>Dettes</th>
                        <th>Statut</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($customers as $customer)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $customer->name }}</div>
                                @if($customer->company_name)
                                    <small class="text-secondary">{{ $customer->company_name }}</small>
                                @endif
                            </td>
                            <td>
                                @if($customer->phone)
                                    <div><i class="bi bi-telephone"></i> {{ $customer->phone }}</div>
                                @endif
                                @if($customer->email)
                                    <small class="text-secondary">{{ $customer->email }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $customer->is_professional ? 'text-bg-primary' : 'text-bg-secondary' }}">
                                    {{ $customer->is_professional ? 'Pro' : 'Particulier' }}
                                </span>
                            </td>
                            <td>{{ number_format($customer->credit_limit, 0, ',', ' ') }} FCFA</td>
                            <td>
                                @if($customer->total_debt > 0)
                                    <span class="text-danger fw-semibold">{{ number_format($customer->total_debt, 0, ',', ' ') }} FCFA</span>
                                @else
                                    <span class="text-success">Aucune</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $customer->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                    {{ $customer->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('customers.edit', $customer) }}" class="btn btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('customers.toggle', $customer) }}" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-outline-{{ $customer->is_active ? 'warning' : 'success' }}" title="{{ $customer->is_active ? 'Désactiver' : 'Activer' }}">
                                            <i class="bi bi-{{ $customer->is_active ? 'pause' : 'play' }}"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('customers.destroy', $customer) }}" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce client ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-secondary">
                                <i class="bi bi-people fs-1 d-block mb-2"></i>
     
