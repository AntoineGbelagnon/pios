@extends('layouts.app')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div><p class="text-secondary mb-1">Approvisionnement</p><h1 class="h2 mb-0">Commandes fournisseurs</h1></div>
    <a href="{{ route('purchases.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Nouvelle commande</a>
</div>
@if (session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3"><div class="card pios-panel border-0"><div class="card-body"><p class="text-secondary mb-2">En attente</p><strong class="fs-3 text-warning">{{ number_format($stats['totalPending'], 0, ',', ' ') }} FCFA</strong></div></div></div>
    <div class="col-sm-6 col-xl-3"><div class="card pios-panel border-0"><div class="card-body"><p class="text-secondary mb-2">Recues</p><strong class="fs-3 text-success">{{ number_format($stats['totalReceived'], 0, ',', ' ') }} FCFA</strong></div></div></div>
    <div class="col-sm-6 col-xl-3"><div class="card pios-panel border-0"><div class="card-body"><p class="text-secondary mb-2">Credit fournisseurs</p><strong class="fs-3 text-danger">{{ number_format($stats['totalCredit'], 0, ',', ' ') }} FCFA</strong></div></div></div>
    <div class="col-sm-6 col-xl-3"><div class="card pios-panel border-0"><div class="card-body"><p class="text-secondary mb-2">Total commandes</p><strong class="fs-3">{{ $stats['count'] }}</strong></div></div></div>
</div>
<div class="card pios-panel border-0 mb-4">
    <div class="card-body"><form method="GET" class="row g-2">
        <div class="col-md-5"><input type="text" name="search" class="form-control" placeholder="Rechercher..." value="{{ request('search') }}"></div>
        <div class="col-md-3"><select name="status" class="form-select"><option value="">Tous statuts</option>
            <option value="draft" @selected(request('status')==='draft')>Brouillon</option><option value="ordered" @selected(request('status')==='ordered')>Commande</option>
            <option value="partial" @selected(request('status')==='partial')>Partielle</option><option value="received" @selected(request('status')==='received')>Recue</option>
        </select></div>
        <div class="col-md-2"><button type="submit" class="btn btn-outline-primary w-100">Filtrer</button></div>
    </form></div>
</div>
<div class="card pios-panel border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th>Reference</th><th>Fournisseur</th><th>Total</th><th>Paye</th><th>Statut</th><th>Date</th><th></th></tr></thead>
            <tbody>
                @forelse($purchases as $p)
                <tr>
                    <td class="fw-semibold">{{ $p->reference }}</td>
                    <td>{{ $p->supplier->name ?? '-' }}</td>
                    <td>{{ number_format($p->total, 0, ',', ' ') }} FCFA</td>
                    <td>{{ number_format($p->amount_paid, 0, ',', ' ') }} FCFA</td>
                    <td><span class="badge text-bg-{{ match($p->status){'received'=>'success','ordered'=>'primary','partial'=>'warning','draft'=>'secondary',default=>'secondary'} }}">{{ $p->status_label }}</span></td>
                    <td>{{ $p->created_at->format('d/m/Y') }}</td>
                    <td><a href="{{ route('purchases.show', $p) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a></td>
                </tr>
                @empty<tr><td colspan="7" class="text-center text-secondary py-4">Aucune commande</td></tr>@endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $purchases->links() }}</div>
</div>
@endsection
