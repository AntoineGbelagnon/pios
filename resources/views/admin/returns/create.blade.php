@extends('layouts.app')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div><p class="text-secondary mb-1">Retour</p><h1 class="h2 mb-0">Nouveau retour</h1></div>
    <a href="{{ route('returns.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Retour</a>
</div>
@if ($errors->any())<div class="alert alert-danger">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card pios-panel border-0 mb-4">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">Vente : {{ $sale->invoice_number }}</h5>
                <small class="text-secondary">Client : {{ $sale->customer->name ?? 'Client comptoir' }} | Date : {{ $sale->created_at->format('d/m/Y') }}</small>
            </div>
            <form method="POST" action="{{ route('returns.store') }}">
                @csrf
                <input type="hidden" name="sale_id" value="{{ $sale->id }}">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>Produit</th><th>Qte achetee</th><th>Prix unit.</th><th>Total</th><th>Qte retour</th></tr></thead>
                        <tbody>
                            @foreach($sale->items as $item)
                            <tr>
                                <td class="fw-semibold">{{ $item->product->name ?? 'N/A' }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</td>
                                <td>{{ number_format($item->total, 0, ',', ' ') }} FCFA</td>
                                <td>
                                    <input type="hidden" name="items[{{ $loop->index }}][sale_item_id]" value="{{ $item->id }}">
                                    <input type="hidden" name="items[{{ $loop->index }}][product_id]" value="{{ $item->product_id }}">
                                    <input type="number" name="items[{{ $loop->index }}][quantity]" class="form-control form-control-sm" value="0" min="0" max="{{ $item->quantity }}" style="width:80px">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Motif *</label>
                            <input type="text" name="reason" class="form-control" required maxlength="500" placeholder="Ex: Produit defectueux...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mode de remboursement *</label>
                            <select name="refund_method" class="form-select" required>
                                <option value="cash">Especes</option>
                                <option value="card">Carte</option>
                                <option value="mobile_money">Mobile Money</option>
                                <option value="credit">Credit (bon de reduction)</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="2" maxlength="1000"></textarea>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <button type="submit" class="btn btn-danger"><i class="bi bi-arrow-return-left"></i> Enregistrer le retour</button>
                </div>
            </form>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card pios-panel border-0">
            <div class="card-body">
                <h5 class="mb-3">Resume de la vente</h5>
                <div class="mb-2"><strong>Facture :</strong> {{ $sale->invoice_number }}</div>
                <div class="mb-2"><strong>Total :</strong> {{ number_format($sale->total, 0, ',', ' ') }} FCFA</div>
                <div class="mb-2"><strong>Paye :</strong> {{ number_format($sale->amount_paid, 0, ',', ' ') }} FCFA</div>
                <div class="mb-2"><strong>Mode :</strong> {{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
