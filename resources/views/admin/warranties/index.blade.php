@extends('layouts.app')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div><p class="text-secondary mb-1">SAV</p><h1 class="h2 mb-0">Garanties et SAV</h1></div>
    <a href="{{ route('warranties.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Nouvelle garantie</a>
</div>
@if (session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
<div class="row g-3 mb-4">
    <div class="col-sm-4"><div class="card pios-panel border-0"><div class="card-body"><p class="text-secondary mb-2">Actives</p><strong class="fs-3 text-success">{{ $stats['activeCount'] }}</strong></div></div></div>
    <div class="col-sm-4"><div class="card pios-panel border-0"><div class="card-body"><p class="text-secondary mb-2">Expirent bientot (30j)</p><strong class="fs-3 text-warning">{{ $stats['expiringCount'] }}</strong></div></div></div>
    <div class="col-sm-4"><div class="card pios-panel border-0"><div class="card-body"><p class="text-secondary mb-2">Reparees</p><strong class="fs-3 text-primary">{{ $stats['repairedCount'] }}</strong></div></div></div>
</div>
<div class="card pios-panel border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th>Numero</th><th>Client</th><th>Produit</th><th>Expire le</th><th>Statut</th><th></th></tr></thead>
            <tbody>
                @forelse($warranties as $w)
                <tr>
                    <td class="fw-semibold">{{ $w->warranty_number }}</td>
                    <td>{{ $w->customer->name ?? '-' }}</td>
                    <td>{{ $w->product->name ?? '-' }}</td>
                    <td class="{{ $w->expiry_date->isPast() ? 'text-danger' : '' }}">{{ $w->expiry_date->format('d/m/Y') }}</td>
                    <td><span class="badge text-bg-{{ match($w->status){'active'=>'success','repaired'=>'primary','replaced'=>'info','expired'=>'danger','closed'=>'secondary',default=>'secondary'} }}">{{ ucfirst($w->status) }}</span></td>
                    <td><a href="{{ route('warranties.show', $w) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a></td>
                </tr>
                @empty<tr><td colspan="6" class="text-center text-secondary py-4">Aucune garantie</td></tr>@endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $warranties->links() }}</div>
</div>
@endsection
