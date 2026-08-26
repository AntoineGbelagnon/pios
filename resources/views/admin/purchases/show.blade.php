@extends('layouts.app')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div><p class="text-secondary mb-1">Approvisionnement</p><h1 class="h2 mb-0">{{ $purchase->reference }}</h1></div>
    <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Retour</a>
</div>
@if (session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card pios-panel border-0 mb-4">
            <div class="card-header bg-transparent"><h5 class="mb-0">Articles</h5></div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Produit</th><th>Qte cmd</th><th>Qte recue</th><th>Prix unit.</th><th>Total</th></tr></thead>
                    <tbody>
                        @foreach($purchase->items as $item)
                        <tr>
                            <td class="fw-semibold">{{ $item->product->name ?? 'N/A' }}</td>
                            <td>{{ $item->quantity_ordered }}</td>
                            <td class="{{ $item->quantity_received >= $item->quantity_ordered ? 'text-success' : 'text-warning' }}">{{ $item->quantity_received }}</td>
                            <td>{{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</td>
                            <td class="fw-semibold">{{ number_format($item->total, 0, ',', ' ') }} FCFA</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @if(in_array($purchase->status, ['ordered', 'partial']))
        <div class="card pios-panel border-0">
            <div class="card-body"><h5 class="mb-3">Receptionner</h5>
                <form method="POST" action="{{ route('purchases.receive', $purchase) }}">
                    @csrf @method('PATCH')
                    @foreach($purchase->items as $item)
                    <div class="row g-2 align-items-center mb-2">
                        <div class="col-5">{{ $item->product->name ?? 'N/A' }} (reste: {{ $item->quantity_ordered - $item->quantity_received }})</div>
                        <div class="col-4"><input type="number" name="items[{{ $loop->index }}][quantity_received]" class="form-control form-control-sm" value="0" min="0" max="{{ $item->quantity_ordered - $item->quantity_received }}"></div>
                        <div class="col-3"><input type="hidden" name="items[{{ $loop->index }}][purchase_item_id]" value="{{ $item->id }}"></div>
                    </div>
                    @endforeach
                    <button type="submit" class="btn btn-success mt-2"><i class="bi bi-check-lg"></i> Valider reception</button>
                </form>
            </div>
        </div>
        @endif
    </div>
    <div class="col-lg-4">
        <div class="card pios-panel border-0"><div class="card-body">
            <h5 class="mb-3">Resume</h5>
            <div class="mb-2"><strong>Fournisseur :</strong> {{ $purchase->supplier->name ?? '-' }}</div>
            <div class="mb-2"><strong>Statut :</strong> <span class="badge text-bg-primary">{{ $purchase->status_label }}</span></div>
            <div class="mb-2"><strong>Sous-total :</strong> {{ number_format($purchase->subtotal, 0, ',', ' ') }} FCFA</div>
            @if($purchase->discount_amount > 0)<div class="mb-2"><strong>Remise :</strong> -{{ number_format($purchase->discount_amount, 0, ',', ' ') }} FCFA</div>@endif
            <hr><div class="mb-2"><strong>Total :</strong> <span class="fs-5">{{ number_format($purchase->total, 0, ',', ' ') }} FCFA</span></div>
            <div class="mb-2"><strong>Paye :</strong> {{ number_format($purchase->amount_paid, 0, ',', ' ') }} FCFA</div>
            @if($purchase->credit_amount > 0)<div class="mb-2 text-danger"><strong>Reste :</strong> {{ number_format($purchase->credit_amount, 0, ',', ' ') }} FCFA</div>@endif
        </div></div>
    </div>
</div>
@endsection
