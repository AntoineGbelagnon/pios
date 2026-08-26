@extends('layouts.app')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <p class="text-secondary mb-1">Organisation</p>
            <h1 class="h2 mb-0">Entrepôts</h1>
        </div><a class="btn btn-primary" href="{{ route('warehouses.create') }}">Nouvel entrepôt</a>
    </div>
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    <div class="card pios-panel border-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Boutique</th>
                        <th>Par défaut</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($warehouses as $warehouse)
                        <tr>
                            <td class="fw-semibold">{{ $warehouse->name }}</td>
                            <td>{{ $warehouse->shop?->name ?: 'Central' }}</td>
                            <td>
                                @if ($warehouse->is_default)
                                <span class="badge text-bg-success">Oui</span>@else<span
                                        class="text-secondary">Non</span>
                                @endif
                            </td>
                            <td class="text-end"><a class="btn btn-sm btn-outline-primary"
                                    href="{{ route('warehouses.edit', $warehouse) }}">Modifier</a></td>
                    </tr>@empty<tr>
                            <td colspan="4" class="text-center text-secondary py-4">Aucun entrepôt.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $warehouses->links() }}</div>
    </div>
@endsection
