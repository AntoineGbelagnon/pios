@extends('layouts.app')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <p class="text-secondary mb-1">Commercial</p>
            <h1 class="h2 mb-0">Ventes</h1>
        </div>
        <a href="{{ route('sales.pos') }}" class="btn btn-primary">
            <i class="bi bi-cart-plus"></i> Nouvelle vente (POS)
        </a>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card pios-panel border-0 h-100">
                <div class="card-body">
                    <p class="text-secondary mb-2">Ventes aujourd'hui</p>
                    <strong class="fs-2">{{ $stats['todayCount'] }}</strong>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card pios-panel border-0 h-100">
                <div class="card-body">
                    <p class="text-secondary mb-2">CA aujourd'hui</p>
                    <strong class="fs-2 text-success">{{ number_format($stats['todayTotal'], 0, ',', ' ') }} FCFA</strong>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card pios-panel border-0 h-100">
                <div class="card-body">
                    <p class="text-secondary mb-2">Ventes ce mois</p>
                    <strong class="fs-2">{{ $stats['monthCount'] }}</strong>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card pios-panel border-0 h-100">
                <div class="card-body">
                    <p class="text-secondary mb-2">CA ce mois</p>
                    <strong class="fs-2 text-primary">{{ number_format($stats['monthTotal'], 0, ',', ' ') }} FCFA</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="card pios-panel border-0 mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Rechercher..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Tous statuts</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completee</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Annulee</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="payment_method" class="form-select">
                        <option value="">Tous modes</option>
                        <option value="cash" {{ request('payment_method') === 'cash' ? 'selected' : '' }}>Especes</option>
                        <option value="card" {{ request('payment_method') === 'card' ? 'selected' : '' }}>Carte</option>
                        <option value="mobile_money" {{ request('payment_method') === 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                        <option value="credit" {{ request('payment_method') === 'credit' ? 'selected' : '' }}>Credit</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-outline-primary w-100">Filtrer</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card pios-panel border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Facture</th>
                        <th>Client</th>
                        <th>Boutique</th>
                        <th>Vendeur</th>
                        <th>Total</th>
                        <th>Paiement</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sales as $sale)
                        <tr>
                            <td class="fw-semibold">{{ $sale->invoice_number }}</td>
                            <td>{{ $sale->customer->name ?? 'Client occasionnel' }}</td>
                            <td>{{ $sale->shop->name ?? '-' }}</td>
                            <td>{{ $sale->user->name ?? '-' }}</td>
                            <td class="fw-semibold">{{ number_format($sale->total, 0, ',', ' ') }} FCFA</td>
                            <td><span class="badge text-bg-light">{{ $sale->payment_method_label }}</span></td>
                            <td>
                                <span class="badge text-bg-{{ $sale->status === 'completed' ? 'success' : ($sale->status === 'cancelled' ? 'danger' : 'warning') }}">
                                    {{ $sale->status_label }}
                                </span>
                            </td>
                            <td>{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                            <td class="text-end">
                                <a href="{{ route('sales.show', $sale) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-secondary">Aucune vente trouvee</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $sales->links() }}</div>
    </div>
@endsection
