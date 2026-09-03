@extends('layouts.app')

@section('title', 'Tableau de bord')
@section('page-title', 'Dashboard')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <p class="text-secondary mb-1">Vue d'ensemble de votre activite</p>
        <h1 class="h2 fw-bold mb-0">Bonjour, {{ Str::before(auth()->user()->name, ' ') }}</h1>
    </div>
    <span class="badge text-bg-success">{{ $company->is_active ? 'Entreprise active' : 'Entreprise inactive' }}</span>
</div>

@if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif

{{-- KPIs Jour --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card pios-panel pios-metric-card metric-green border-0 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3"><span class="pios-metric-icon">↗</span><p class="pios-metric-label mb-0">Ventes aujourd'hui</p></div>
                <strong class="pios-metric-value">{{ number_format($todaySales, 0, ',', ' ') }} FCFA</strong>
                <small class="d-block text-secondary mt-1">{{ $todaySalesCount }} vente(s) &middot; Panier moyen : {{ number_format($todayAvgBasket, 0, ',', ' ') }} FCFA</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card pios-panel pios-metric-card metric-pink border-0 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3"><span class="pios-metric-icon">↘</span><p class="pios-metric-label mb-0">Depenses aujourd'hui</p></div>
                <strong class="pios-metric-value">{{ number_format($todayExpenses, 0, ',', ' ') }} FCFA</strong>
                <small class="d-block text-secondary mt-1">Benefice net : {{ number_format($todaySales - $todayExpenses, 0, ',', ' ') }} FCFA</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card pios-panel pios-metric-card metric-purple border-0 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3"><span class="pios-metric-icon">⌁</span><p class="pios-metric-label mb-0">CA du mois</p></div>
                <strong class="pios-metric-value">{{ number_format($monthSales, 0, ',', ' ') }} FCFA</strong>
                <small class="d-block text-secondary mt-1">Benefice brut : {{ number_format($monthGrossProfit, 0, ',', ' ') }} FCFA</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card pios-panel pios-metric-card metric-orange border-0 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3"><span class="pios-metric-icon">₣</span><p class="pios-metric-label mb-0">Benefice net du mois</p></div>
                <strong class="pios-metric-value {{ $monthNetProfit < 0 ? 'text-danger' : '' }}">{{ number_format($monthNetProfit, 0, ',', ' ') }} FCFA</strong>
                <small class="d-block text-secondary mt-1">Apres depenses : {{ number_format($monthExpenses, 0, ',', ' ') }} FCFA</small>
            </div>
        </div>
    </div>
</div>

{{-- KPIs Stock & Creances --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card pios-panel border-0 h-100">
            <div class="card-body">
                <p class="text-secondary mb-2">Produits en stock</p>
                <strong class="fs-3">{{ $totalProducts }}</strong>
                <small class="d-block text-secondary mt-1">Valeur : {{ number_format($stockValue, 0, ',', ' ') }} FCFA</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card pios-panel border-0 h-100">
            <div class="card-body">
                <p class="text-secondary mb-2">Stock faible</p>
                <strong class="fs-3 text-warning">{{ $lowStockCount }}</strong>
                <small class="d-block text-secondary mt-1">Ruptures : {{ $outOfStockCount }}</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card pios-panel border-0 h-100">
            <div class="card-body">
                <p class="text-secondary mb-2">Creances clients</p>
                <strong class="fs-3 text-danger">{{ number_format($totalCustomerDebt, 0, ',', ' ') }} FCFA</strong>
                <small class="d-block mt-1"><a href="{{ route('customers.index') }}">Voir les clients</a></small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card pios-panel border-0 h-100">
            <div class="card-body">
                <p class="text-secondary mb-2">Benefice brut mois</p>
                <strong class="fs-3 {{ $monthGrossProfit >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($monthGrossProfit, 0, ',', ' ') }} FCFA</strong>
                <small class="d-block text-secondary mt-1">CA - Achats</small>
            </div>
        </div>
    </div>
</div>

{{-- Actions rapides --}}
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="card pios-panel border-0">
            <div class="card-body">
                <h5 class="pios-section-title mb-3">Actions rapides</h5>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('sales.pos') }}" class="btn btn-primary"><i class="bi bi-cart-plus"></i> Nouvelle vente</a>
                    <a href="{{ route('products.create') }}" class="btn btn-outline-primary"><i class="bi bi-plus-lg"></i> Nouveau produit</a>
                    <a href="{{ route('stock.movements') }}" class="btn btn-outline-success"><i class="bi bi-arrow-left-right"></i> Mouvements stock</a>
                    <a href="{{ route('stock.low') }}" class="btn btn-outline-warning"><i class="bi bi-exclamation-triangle"></i> Alertes stock</a>
                    <a href="{{ route('cash_registers.index') }}" class="btn btn-outline-info"><i class="bi bi-cash-stack"></i> Caisse</a>
                    <a href="{{ route('expenses.create') }}" class="btn btn-outline-danger"><i class="bi bi-receipt"></i> Nouvelle depense</a>
                    <a href="{{ route('purchases.create') }}" class="btn btn-outline-secondary"><i class="bi bi-truck"></i> Commande fournisseur</a>
                    <a href="{{ route('statistics.index') }}" class="btn btn-outline-dark"><i class="bi bi-graph-up"></i> Statistiques</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Dernieres ventes --}}
    <div class="col-12 col-xl-7">
        <div class="card pios-panel border-0 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="pios-section-title mb-0">Dernieres ventes</h5>
                    <a href="{{ route('sales.index') }}">Tout voir</a>
                </div>
                @if($recentSales->isEmpty())
                    <p class="text-secondary">Aucune vente enregistree.</p>
                @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead><tr><th>Facture</th><th>Client</th><th>Montant</th><th>Mode</th><th>Date</th></tr></thead>
                        <tbody>
                            @foreach($recentSales as $sale)
                            <tr>
                                <td class="fw-semibold"><a href="{{ route('sales.show', $sale) }}">{{ $sale->invoice_number }}</a></td>
                                <td>{{ $sale->customer->name ?? 'Comptoir' }}</td>
                                <td class="fw-semibold">{{ number_format($sale->total, 0, ',', ' ') }} FCFA</td>
                                <td><span class="badge text-bg-secondary">{{ $sale->payment_method_label }}</span></td>
                                <td class="text-secondary">{{ $sale->created_at->format('d/m H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Top produits du mois --}}
    <div class="col-12 col-xl-5">
        <div class="card pios-panel border-0 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="pios-section-title mb-0">Top produits du mois</h5>
                    <a href="{{ route('statistics.index') }}">Details</a>
                </div>
                @if($topProducts->isEmpty())
                    <p class="text-secondary">Aucune donnee pour ce mois.</p>
                @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead><tr><th>Produit</th><th>Qte</th><th>CA</th></tr></thead>
                        <tbody>
                            @foreach($topProducts as $p)
                            <tr>
                                <td class="fw-semibold">{{ $p->name }}</td>
                                <td>{{ $p->qty_sold }}</td>
                                <td class="text-success fw-semibold">{{ number_format($p->revenue, 0, ',', ' ') }} FCFA</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Graphique + Activite --}}
<div class="row g-4 mt-1">
    <div class="col-12 col-xl-8">
        <div class="card pios-panel border-0 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <p class="text-secondary mb-1">Suivi operationnel</p>
                        <h2 class="h5 mb-0">Ventes des 7 derniers jours</h2>
                    </div>
                    <span class="badge text-bg-light">Journal d'activite</span>
                </div>
                <div class="pios-chart"><canvas id="activityChart" data-labels="{{ $activityLabels->toJson() }}"
                        data-values="{{ $activityValues->toJson() }}"
                        aria-label="Ventes des sept derniers jours"></canvas></div>
            </div>
        </div>
    </div>
    <div class="col-12 col-xl-4">
        <div class="card pios-panel border-0 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <p class="text-secondary mb-1">Activite recente</p>
                    <a href="{{ route('activity.index') }}">Tout voir</a>
                </div>
                @forelse ($recentActivity as $activity)
                    <div class="d-flex justify-content-between border-top py-2">
                        <small>{{ $activity->description }}</small>
                        <small class="text-secondary text-nowrap ms-2">{{ $activity->created_at->diffForHumans() }}</small>
                    </div>
                @empty
                    <p class="text-secondary mb-0">Aucune activite.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
