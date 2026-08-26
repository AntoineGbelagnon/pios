@extends('layouts.app')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <p class="text-secondary mb-1">Vente</p>
            <h1 class="h2 mb-0">{{ $sale->invoice_number }}</h1>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('sales.receipt', $sale) }}" class="btn btn-outline-primary" target="_blank">
                <i class="bi bi-printer"></i> Imprimer recu
            </a>
            <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
            @if($sale->status === 'completed')
                <form method="POST" action="{{ route('sales.cancel', $sale) }}" onsubmit="return confirm('Annuler cette vente ? Le stock sera restaure.')">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-outline-danger"><i class="bi bi-x-circle"></i> Annuler</button>
                </form>
            @endif
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card pios-panel border-0 mb-4">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">Articles vendus</h5>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th>Prix unitaire</th>
                                <th>Qte</th>
                                <th>Remise</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sale->items as $item)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $item->product_name }}</div>
                                        @if($item->product_sku)<small class="text-secondary">{{ $item->product_sku }}</small>@endif
                                    </td>
                                    <td>{{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ $item->discount_amount > 0 ? number_format($item->discount_amount, 0, ',', ' ') . ' FCFA' : '-' }}</td>
                                    <td class="text-end fw-semibold">{{ number_format($item->total, 0, ',', ' ') }} FCFA</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card pios-panel border-0 mb-4">
                <div class="card-body">
                    <h5 class="mb-3">Resume</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Sous-total</span>
                        <span>{{ number_format($sale->subtotal, 0, ',', ' ') }} FCFA</span>
                    </div>
                    @if($sale->discount_amount > 0)
                        <div class="d-flex justify-content-between mb-2 text-danger">
                            <span>Remise ({{ $sale->discount_percent }}%)</span>
                            <span>- {{ number_format($sale->discount_amount, 0, ',', ' ') }} FCFA</span>
                        </div>
                    @endif
                    @if($sale->tax_amount > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span>TVA ({{ $sale->tax_percent }}%)</span>
                            <span>+ {{ number_format($sale->tax_amount, 0, ',', ' ') }} FCFA</span>
                        </div>
                    @endif
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Total</strong>
                        <strong class="fs-5">{{ number_format($sale->total, 0, ',', ' ') }} FCFA</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Paye</span>
                        <span class="text-success">{{ number_format($sale->amount_paid, 0, ',', ' ') }} FCFA</span>
                    </div>
                    @if($sale->change_amount > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span>Monnaie rendue</span>
                            <span>{{ number_format($sale->change_amount, 0, ',', ' ') }} FCFA</span>
                        </div>
                    @endif
                    @if($sale->credit_amount > 0)
                        <div class="d-flex justify-content-between mb-2 text-danger">
                            <span>Reste a payer</span>
                            <span>{{ number_format($sale->credit_amount, 0, ',', ' ') }} FCFA</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card pios-panel border-0 mb-4">
                <div class="card-body">
                    <h5 class="mb-3">Details</h5>
                    <div class="mb-2"><strong>Client :</strong> {{ $sale->customer->name ?? 'Client occasionnel' }}</div>
                    <div class="mb-2"><strong>Boutique :</strong> {{ $sale->shop->name ?? '-' }}</div>
                    <div class="mb-2"><strong>Vendeur :</strong> {{ $sale->user->name ?? '-' }}</div>
                    <div class="mb-2"><strong>Mode de paiement :</strong> {{ $sale->payment_method_label }}</div>
                    <div class="mb-2"><strong>Date :</strong> {{ $sale->created_at->format('d/m/Y H:i') }}</div>
                    <div class="mb-2">
                        <strong>Statut :</strong>
                        <span class="badge text-bg-{{ $sale->status === 'completed' ? 'success' : ($sale->status === 'cancelled' ? 'danger' : 'warning') }}">
                            {{ $sale->status_label }}
                        </span>
                    </div>
                    @if($sale->notes)
                        <div class="mt-3"><strong>Notes :</strong><br>{{ $sale->notes }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
