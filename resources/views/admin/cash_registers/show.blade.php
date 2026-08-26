@extends('layouts.app')
@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <p class="text-secondary mb-1">Caisse</p>
            <h1 class="h2 mb-0">Detail du registre #{{ $register->id }}</h1>
        </div>
        <a href="{{ route('cash_registers.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i>
            Retour</a>
    </div>
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="row g-3 mb-4">
                <div class="col-sm-4">
                    <div class="card pios-panel border-0">
                        <div class="card-body">
                            <p class="text-secondary mb-1">Fonds initial</p><strong
                                class="fs-5">{{ number_format($register->opening_amount, 0, ',', ' ') }} FCFA</strong>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card pios-panel border-0">
                        <div class="card-body">
                            <p class="text-secondary mb-1">Ventes totales</p><strong
                                class="fs-5 text-success">{{ number_format($register->total_sales, 0, ',', ' ') }}
                                FCFA</strong>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card pios-panel border-0">
                        <div class="card-body">
                            <p class="text-secondary mb-1">Depenses</p><strong
                                class="fs-5 text-danger">{{ number_format($register->total_expenses, 0, ',', ' ') }}
                                FCFA</strong>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card pios-panel border-0 mb-4">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">Ventes du jour</h5>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Facture</th>
                                <th>Client</th>
                                <th>Montant</th>
                                <th>Heure</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sales as $sale)
                                <tr>
                                    <td>{{ $sale->invoice_number }}</td>
                                    <td>{{ $sale->customer->name ?? 'Occasionnel' }}</td>
                                    <td>{{ number_format($sale->total, 0, ',', ' ') }} FCFA</td>
                                    <td>{{ $sale->created_at->format('H:i') }}</td>
                                </tr>
                            @empty<tr>
                                    <td colspan="4" class="text-center text-secondary">Aucune vente</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($register->status === 'open')
                <div class="card pios-panel border-0">
                    <div class="card-body">
                        <h5 class="mb-3">Cloturer la caisse</h5>
                        <form method="POST" action="{{ route('cash_registers.close', $register) }}">
                            @csrf @method('PATCH')
                            <div class="mb-3"><label class="form-label">Montant reel en caisse (FCFA) *</label><input
                                    type="number" name="closing_amount" class="form-control" min="0" required></div>
                            <div class="mb-3"><label class="form-label">Notes</label>
                                <textarea name="closing_notes" class="form-control" rows="2"></textarea>
                            </div>
                            <button type="submit" class="btn btn-warning w-100"
                                onclick="return confirm('Confirmer la cloture ?')"><i class="bi bi-lock"></i>
                                Cloturer</button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
        <div class="col-lg-4">
            <div class="card pios-panel border-0 mb-4">
                <div class="card-body">
                    <h5 class="mb-3">Resume</h5>
                    <div class="mb-2"><strong>Boutique :</strong> {{ $register->shop->name ?? '-' }}</div>
                    <div class="mb-2"><strong>Caissier :</strong> {{ $register->cashier->name ?? '-' }}</div>
                    <div class="mb-2"><strong>Ouverture :</strong> {{ $register->created_at->format('d/m/Y H:i') }}</div>
                    <div class="mb-2"><strong>Statut :</strong> <span
                            class="badge text-bg-{{ $register->status === 'open' ? 'success' : 'secondary' }}">{{ $register->status === 'open' ? 'Ouverte' : 'Fermee' }}</span>
                    </div>
                    @if ($register->status === 'closed')
                        <hr>
                        <div class="mb-2"><strong>Montant attendu :</strong>
                            {{ number_format($expectedAmount, 0, ',', ' ') }} FCFA</div>
                        <div class="mb-2"><strong>Montant reel :</strong>
                            {{ number_format($register->closing_amount, 0, ',', ' ') }} FCFA</div>
                        <div class="mb-2"><strong>Ecart :</strong> <span
                                class="{{ $register->difference != 0 ? 'text-danger' : 'text-success' }}">{{ number_format($register->difference, 0, ',', ' ') }}
                                FCFA</span></div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
