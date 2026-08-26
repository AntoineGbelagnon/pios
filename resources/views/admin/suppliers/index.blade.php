@extends('layouts.app')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <p class="text-secondary mb-1">Gestion</p>
            <h1 class="h2 mb-0">Fournisseurs</h1>
        </div>
        <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Nouveau fournisseur
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
                    <p class="text-secondary mb-2">Total fournisseurs</p>
                    <strong class="fs-2">{{ $stats['total'] }}</strong>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card pios-panel border-0 h-100">
                <div class="card-body">
                    <p class="text-secondary mb-2">Actifs</p>
                    <strong class="fs-2 text-success">{{ $stats['active'] }}</strong>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card pios-panel border-0 h-100">
                <div class="card-body">
                    <p class="text-secondary mb-2">Avec dettes</p>
                    <strong class="fs-2 text-warning">{{ $stats['withDebt'] }}</strong>
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
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="Rechercher un fournisseur..." value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
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

    {{-- Suppliers Table --}}
    <div class="card pios-panel border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Contact</th>
                        <th>Ville</th>
                        <th>Total achats</th>
                        <th>Dettes</th>
                        <th>Statut</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($suppliers as $supplier)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $supplier->name }}</div>
                                @if($supplier->contact_name)
                                    <small class="text-secondary">{{ $supplier->contact_name }}</small>
                                @endif
                            </td>
                            <td>
                                @if($supplier->phone)
                                    <div><i class="bi bi-telephone"></i> {{ $supplier->phone }}</div>
                                @endif
                                @if($supplier->email)
                                    <small class="text-secondary">{{ $supplier->email }}</small>
                                @endif
                            </td>
                            <td>{{ $supplier->city ?? '-' }}</td>
                            <td>{{ number_format($supplier->total_purchases, 0, ',', ' ') }} FCFA</td>
                            <td>
                                @if($supplier->total_debt > 0)
                                    <span class="text-danger fw-semibold">{{ number_format($supplier->total_debt, 0, ',', ' ') }} FCFA</span>
                                @else
                                    <span class="text-success">Aucune</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $supplier->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                    {{ $supplier->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('suppliers.toggle', $supplier) }}" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-outline-{{ $supplier->is_active ? 'warning' : 'success' }}">
                                            <i class="bi bi-{{ $supplier->is_active ? 'pause' : 'play' }}"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce fournisseur ?')">
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
                                <i class="bi bi-truck fs-1 d-block mb-2"></i>
                                Aucun fournisseur trouvé
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $suppliers->links() }}
        </div>
    </div>
@endsection
