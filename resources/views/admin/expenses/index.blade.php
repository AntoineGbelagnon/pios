@extends('layouts.app')
@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <p class="text-secondary mb-1">Finance</p>
            <h1 class="h2 mb-0">Depenses</h1>
        </div>
        <a href="{{ route('expenses.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Nouvelle depense</a>
    </div>
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="card pios-panel border-0">
                <div class="card-body">
                    <p class="text-secondary mb-2">Aujourd'hui</p><strong
                        class="fs-3 text-danger">{{ number_format($stats['today'], 0, ',', ' ') }} FCFA</strong>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card pios-panel border-0">
                <div class="card-body">
                    <p class="text-secondary mb-2">Ce mois</p><strong
                        class="fs-3 text-danger">{{ number_format($stats['month'], 0, ',', ' ') }} FCFA</strong>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card pios-panel border-0">
                <div class="card-body">
                    <p class="text-secondary mb-2">Total depenses</p><strong class="fs-3">{{ $stats['count'] }}</strong>
                </div>
            </div>
        </div>
    </div>
    <div class="card pios-panel border-0 mb-4">
        <div class="card-body">
            <form method="GET" class="row g-2">
                <div class="col-md-3"><input type="text" name="search" class="form-control" placeholder="Rechercher..."
                        value="{{ request('search') }}"></div>
                <div class="col-md-2"><select name="category" class="form-select">
                        <option value="">Toutes categories</option>
                        @foreach ($categories as $c)
                            <option value="{{ $c }}" @selected(request('category') === $c)>{{ ucfirst($c) }}</option>
                        @endforeach
                    </select></div>
                <div class="col-md-2"><input type="date" name="date_from" class="form-control"
                        value="{{ request('date_from') }}"></div>
                <div class="col-md-2"><input type="date" name="date_to" class="form-control"
                        value="{{ request('date_to') }}"></div>
                <div class="col-md-1"><button type="submit" class="btn btn-outline-primary w-100">Filtrer</button></div>
            </form>
        </div>
    </div>
    <div class="card pios-panel border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Categorie</th>
                        <th>Description</th>
                        <th>Montant</th>
                        <th>Paiement</th>
                        <th>Utilisateur</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $e)
                        <tr>
                            <td>{{ $e->expense_date->format('d/m/Y') }}</td>
                            <td><span class="badge text-bg-light">{{ ucfirst($e->category) }}</span></td>
                            <td>{{ $e->description }}</td>
                            <td class="fw-semibold text-danger">{{ number_format($e->amount, 0, ',', ' ') }} FCFA</td>
                            <td>{{ ucfirst($e->payment_method) }}</td>
                            <td>{{ $e->user->name ?? '-' }}</td>
                            <td>
                                <form method="POST" action="{{ route('expenses.destroy', $e) }}" class="d-inline"
                                    onsubmit="return confirm('Supprimer ?')">@csrf @method('DELETE')<button
                                        class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>
                            </td>
                        </tr>
                    @empty<tr>
                            <td colspan="7" class="text-center text-secondary py-4">Aucune depense</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $expenses->links() }}</div>
    </div>
@endsection
