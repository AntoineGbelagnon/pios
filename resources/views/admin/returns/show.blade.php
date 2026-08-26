@extends('layouts.app')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div><p class="text-secondary mb-1">Retour</p><h1 class="h2 mb-0">{{ $return->return_number }}</h1></div>
    <a href="{{ route('returns.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Retour</a>
</div>
@if (session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card pios-panel border-0">
            <div class="card-header bg-transparent"><h5 class="mb-0">Articles retournes</h5></div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Produit</th><th>Qte</th><th>Prix unit.</th><th>Total</th></tr></thead>
                    <tbody>
                        @foreach($return->items as $item)
                        <tr><td class="fw-semibold">{{ $item->product->name ?? 'N/A' }}</td><td>{{ $item->quantity }}</td><td>{{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</td><td>{{ number_format($item->total, 0, ',', ' ') }} FCFA</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card pios-panel border-0"><div class="card-body">
            <h5 class="mb-3">Details</h5>
            <div class="mb-2"><strong>Vente :</strong> {{ $return->sale->invoice_number ?? '-' }}</div>
            <div class="mb-2"><strong>Client :</strong> {{ $return->customer->name ?? '-' }}</div>
            <div class="mb-2"><strong>Motif :</strong> {{ $return->reason }}</div>
            <div class="mb-2"><strong>Remboursement :</strong> <span class="text-danger fw-bold">{{ number_format($return->total_refund, 0, ',', ' ') }} FCFA</span></div>
            <div class="mb-2"><strong>Mode :</strong> {{ ucfirst($return->refund_method) }}</div>
            <div class="mb-2"><strong>Statut :</strong> <span class="badge text-bg-{{ match($return->status){'completed'=>'success','pending'=>'warning',default=>'secondary'} }}">{{ ucfirst($return->status) }}</span></div>
        </div></div>
    </div>
</div>
@endsection
