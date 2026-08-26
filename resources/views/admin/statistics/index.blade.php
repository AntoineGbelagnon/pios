@extends('layouts.app')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div><p class="text-secondary mb-1">Rapports</p><h1 class="h2 mb-0">Statistiques</h1></div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('statistics.export.sales', ['period' => $period]) }}" class="btn btn-outline-success btn-sm"><i class="bi bi-download"></i> Export ventes CSV</a>
        <a href="{{ route('statistics.export.products') }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-download"></i> Export produits CSV</a>
        <a href="{{ route('statistics.export.customers') }}" class="btn btn-outline-info btn-sm"><i class="bi bi-download"></i> Export clients CSV</a>
    </div>
</div>

{{-- Filtre periode --}}
<div class="mb-4">
    <div class="d-flex gap-2 flex-wrap">
        @foreach(['today' => 'Aujourd\'hui', 'week' => 'Cette semaine', 'month' => 'Ce mois', 'year' => 'Cette annee'] as $key => $label)
            <a href="?period={{ $key }}" class="btn btn-sm {{ $period === $key ? 'btn-primary' : 'btn-outline-secondary' }}">{{ $label }}</a>
        @endforeach
    </div>
</div>

{{-- KPIs Principaux --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card pios-panel border-0"><div class="card-body">
            <p class="text-secondary mb-2">Chiffre d'affaires</p>
            <strong class="fs-3 text-success">{{ number_format($totalSales, 0, ',', ' ') }} FCFA</strong>
            <small class="d-block text-secondary">{{ $salesCount }} vente(s)</small>
        </div></div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card pios-panel border-0"><div class="card-body">
            <p class="text-secondary mb-2">Cout d'achat</p>
            <strong class="fs-3 text-warning">{{ number_format($totalCost, 0, ',', ' ') }} FCFA</strong>
            <small class="d-block text-secondary">Marge brute : {{ number_format($grossProfit, 0, ',', ' ') }} FCFA</small>
        </div></div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card pios-panel border-0"><div class="card-body">
            <p class="text-secondary mb-2">Depenses</p>
            <strong class="fs-3 text-danger">{{ number_format($totalExpenses, 0, ',', ' ') }} FCFA</strong>
            <small class="d-block text-secondary">Benefice net : {{ number_format($netProfit, 0, ',', ' ') }} FCFA</small>
        </div></div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card pios-panel border-0"><div class="card-body">
            <p class="text-secondary mb-2">Panier moyen</p>
            <strong class="fs-3">{{ number_format($avgBasket, 0, ',', ' ') }} FCFA</strong>
            <small class="d-block text-secondary">{{ $salesCount }} transaction(s)</small>
        </div></div>
    </div>
</div>

<div class="row g-4 mb-4">
    {{-- Top produits --}}
    <div class="col-12 col-xl-6">
        <div class="card pios-panel border-0 h-100">
            <div class="card-header bg-transparent"><h5 class="mb-0">Top 10 produits</h5></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead><tr><th>#</th><th>Produit</th><th>Qte</th><th>CA</th></tr></thead>
                    <tbody>
                        @forelse($topProducts as $i => $p)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td class="fw-semibold">{{ $p->name }}</td>
                            <td>{{ $p->total_qty_sold }}</td>
                            <td class="text-success fw-semibold">{{ number_format($p->total_revenue, 0, ',', ' ') }} FCFA</td>
                        </tr>
                        @empty<tr><td colspan="4" class="text-center text-secondary">Aucune donnee</td></tr>@endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Top categories --}}
    <div class="col-12 col-xl-6">
        <div class="card pios-panel border-0 h-100">
            <div class="card-header bg-transparent"><h5 class="mb-0">Top categories</h5></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead><tr><th>#</th><th>Categorie</th><th>Qte</th><th>CA</th></tr></thead>
                    <tbody>
                        @forelse($topCategories as $i => $cat)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td class="fw-semibold">{{ $cat->name }}</td>
                            <td>{{ $cat->qty_sold ?? 0 }}</td>
                            <td class="text-success fw-semibold">{{ number_format($cat->revenue ?? 0, 0, ',', ' ') }} FCFA</td>
                        </tr>
                        @empty<tr><td colspan="4" class="text-center text-secondary">Aucune donnee</td></tr>@endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    {{-- Ventes par vendeur --}}
    <div class="col-12 col-xl-6">
        <div class="card pios-panel border-0 h-100">
            <div class="card-header bg-transparent"><h5 class="mb-0">Ventes par vendeur</h5></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead><tr><th>Vendeur</th><th>Ventes</th><th>CA total</th></tr></thead>
                    <tbody>
                        @forelse($salesByVendor as $v)
                        <tr>
                            <td class="fw-semibold">{{ $v->name }}</td>
                            <td>{{ $v->sales_count }}</td>
                            <td class="text-success fw-semibold">{{ number_format($v->total_sales ?? 0, 0, ',', ' ') }} FCFA</td>
                        </tr>
                        @empty<tr><td colspan="3" class="text-center text-secondary">Aucune donnee</td></tr>@endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Ventes par mode de paiement --}}
    <div class="col-12 col-xl-6">
        <div class="card pios-panel border-0 h-100">
            <div class="card-header bg-transparent"><h5 class="mb-0">Ventes par mode de paiement</h5></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead><tr><th>Mode</th><th>Nb</th><th>Total</th></tr></thead>
                    <tbody>
                        @forelse($salesByPayment as $sp)
                        <tr>
                            <td class="fw-semibold">{{ match($sp->payment_method){'cash'=>'Especes','card'=>'Carte','mobile_money'=>'Mobile Money','credit'=>'Credit','mixed'=>'Mixte',default=>$sp->payment_method} }}</td>
                            <td>{{ $sp->count }}</td>
                            <td class="text-success fw-semibold">{{ number_format($sp->total, 0, ',', ' ') }} FCFA</td>
                        </tr>
                        @empty<tr><td colspan="3" class="text-center text-secondary">Aucune donnee</td></tr>@endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Graphique evolution ventes --}}
<div class="card pios-panel border-0 mb-4">
    <div class="card-header bg-transparent"><h5 class="mb-0">Evolution des ventes</h5></div>
    <div class="card-body">
        @if($salesByDay->isEmpty())
            <p class="text-secondary text-center py-4">Pas de donnees pour cette periode.</p>
        @else
        <div class="pios-chart"><canvas id="salesChart" data-labels="{{ $salesByDay->pluck('date')->values()->toJson() }}" data-values="{{ $salesByDay->pluck('total')->values()->toJson() }}"></canvas></div>
        @endif
    </div>
</div>

{{-- Top clients --}}
<div class="card pios-panel border-0">
    <div class="card-header bg-transparent"><h5 class="mb-0">Meilleurs clients</h5></div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th>#</th><th>Client</th><th>Total depense</th></tr></thead>
            <tbody>
                @forelse($topCustomers as $i => $c)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td class="fw-semibold">{{ $c->name }}</td>
                    <td class="text-success fw-semibold">{{ number_format($c->total_spent ?? 0, 0, ',', ' ') }} FCFA</td>
                </tr>
                @empty<tr><td colspan="3" class="text-center text-secondary">Aucun client</td></tr>@endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
