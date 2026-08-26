@extends('layouts.app')
@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <p class="text-secondary mb-1">Caisse</p>
            <h1 class="h2 mb-0">Registres de caisse</h1>
        </div>
        <a href="{{ route('cash_registers.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Ouvrir la
            caisse</a>
    </div>
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-4">
            <div class="card pios-panel border-0">
                <div class="card-body">
                    <p class="text-secondary mb-2">Caisses ouvertes</p><strong class="fs-2">{{ $openCount }}</strong>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="card pios-panel border-0">
                <div class="card-body">
                    <p class="text-secondary mb-2">Ventes du jour</p><strong
                        class="fs-2 text-success">{{ number_format($todaySales, 0, ',', ' ') }} FCFA</strong>
                </div>
            </div>
        </div>
    </div>
    <div class="card pios-panel border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Boutique</th>
                        <th>Caissier</th>
                        <th>Fonds</th>
                        <th>Ventes</th>
                        <th>Solde</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($registers as $r)
                        <tr>
                            <td>{{ $r->shop->name ?? '-' }}</td>
                            <td>{{ $r->cashier->name ?? '-' }}</td>
                            <td>{{ number_format($r->opening_amount, 0, ',', ' ') }} FCFA</td>
                            <td>{{ number_format($r->total_sales, 0, ',', ' ') }} FCFA</td>
                            <td>{{ number_format($r->balance, 0, ',', ' ') }} FCFA</td>
                            <td><span
                                    class="badge text-bg-{{ $r->status === 'open' ? 'success' : 'secondary' }}">{{ $r->status === 'open' ? 'Ouverte' : 'Fermee' }}</span>
                            </td>
                            <td>{{ $r->created_at->format('d/m/Y H:i') }}</td>
                            <td><a href="{{ route('cash_registers.show', $r) }}" class="btn btn-sm btn-outline-primary"><i
                                        class="bi bi-eye"></i></a></td>
                        </tr>
                    @empty<tr>
                            <td colspan="8" class="text-center text-secondary py-4">Aucun registre</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $registers->links() }}</div>
    </div>
@endsection
