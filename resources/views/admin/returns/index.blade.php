@extends('layouts.app')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div><p class="text-secondary mb-1">Retours</p><h1 class="h2 mb-0">Retours et remboursements</h1></div>
</div>
@if (session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
<div class="row g-3 mb-4">
    <div class="col-sm-4"><div class="card pios-panel border-0"><div class="card-body"><p class="text-secondary mb-2">Total rembourse</p><strong class="fs-3 text-danger">{{ number_format($stats['totalRefunded'], 0, ',', ' ') }} FCFA</strong></div></div></div>
    <div class="col-sm-4"><div class="card pios-panel border-0"><div class="card-body"><p class="text-secondary mb-2">En attente</p><strong class="fs-3 text-warning">{{ $stats['pendingCount'] }}</strong></div></div></div>
    <div class="col-sm-4"><div class="card pios-panel border-0"><div class="card-body"><p class="text-secondary mb-2">Ce mois</p><strong class="fs-3 text-danger">{{ number_format($stats['monthRefunded'], 0, ',', ' ') }} FCFA</strong></div></div></div>
</div>
<div class="card pios-panel border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th>Numero</th><th>Vente</th><th>Client</th><th>Montant</th><th>Statut</th><th>Date</th><th></th></tr></thead>
            <tbody>
                @forelse($returns as $r)
                <tr>
                    <td class="fw-semibold">{{ $r->return_number }}</td>
                    <td>{{ $r->sale->invoice_number ?? '-' }}</td>
                    <td>{{ $r->customer->name ?? '-' }}</td>
                    <td class="text-danger fw-semibold">{{ number_format($r->total_refund, 0, ',', ' ') }} FCFA</td>
                    <td><span class="badge text-bg-{{ match($r->status){'completed'=>'success','pending'=>'warning','rejected'=>'danger',default=>'secondary'} }}">{{ ucfirst($r->status) }}</span></td>
                    <td>{{ $r->created_at->format('d/m/Y') }}</td>
                    <td><a href="{{ route('returns.show', $r) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a></td>
                </tr>
                @empty<tr><td colspan="7" class="text-center text-secondary py-4">Aucun retour</td></tr>@endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $returns->links() }}</div>
</div>
@endsection
