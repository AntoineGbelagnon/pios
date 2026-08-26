@extends('layouts.app')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <p class="text-secondary mb-1">Stock</p>
            <h1 class="h2 mb-0">Historique des mouvements</h1>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-secondary" href="{{ route('stock.index') }}">Retour au stock</a>
            <a class="btn btn-primary" href="{{ route('stock.movement.create') }}">Nouveau mouvement</a>
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    {{-- Filters --}}
    <div class="card pios-panel border-0 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('stock.movements') }}" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <input type="text" class="form-control" name="search" placeholder="Rechercher produit..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="type">
                        <option value="">Tous types</option>
                        <option value="entry" @selected(request('type') === 'entree')>Entree</option>
                        <option value="exit" @selected(request('type') === 'sortie')>Sortie</option>
                        <option value="adjustment" @selected(request('type') === 'ajustement')>Ajustement</option>
                        <option value="transfer" @selected(request('type') === 'transfert')>Transfert</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button class="btn btn-outline-primary w-100" type="submit">Filtrer</button>
                </div>
                <div class="col-md-1">
                    <a class="btn btn-outline-secondary w-100" href="{{ route('stock.movements') }}">Reinit.</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card pios-panel border-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Produit</th>
                        <th>Type</th>
                        <th>Quantite</th>
                        <th>Prix unitaire</th>
                        <th>Reference</th>
                        <th>Effectue par</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($movements as $movement)
                        <tr>
                            <td>{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                            <td class="fw-semibold">{{ $movement->product->name ?? '---' }}</td>
                            <td><span class="badge text-bg-{{ $movement->type_color }}">{{ $movement->type_label }}</span></td>
                            <td><strong>{{ $movement->quantity }}</strong></td>
                            <td>{{ $movement->unit_price ? number_format($movement->unit_price, 0, ',', ' ') . ' FCFA' : '---' }}</td>
                            <td>{{ $movement->reference ?? '---' }}</td>
                            <td>{{ $movement->creator->name ?? '---' }}</td>
                            <td class="text-truncate" style="max-width: 200px;">{{ $movement->notes ?? '---' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-secondary py-4">Aucun mouvement.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $movements->links() }}</div>
    </div>
@endsection
