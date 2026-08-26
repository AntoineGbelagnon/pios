@extends('layouts.app')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <p class="text-secondary mb-1">Organisation</p>
            <h1 class="h2 mb-0">Boutiques</h1>
        </div><a class="btn btn-primary" href="{{ route('shops.create') }}">Nouvelle boutique</a>
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
                        <th>Code</th>
                        <th>Ville</th>
                        <th>Statut</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($shops as $shop)
                        <tr>
                            <td class="fw-semibold">{{ $shop->name }}</td>
                            <td><code>{{ $shop->code }}</code></td>
                            <td>{{ $shop->city ?: '—' }}</td>
                            <td><span
                                    class="badge text-bg-{{ $shop->is_active ? 'success' : 'secondary' }}">{{ $shop->is_active ? 'Active' : 'Inactive' }}</span>
                            </td>
                            <td class="text-end"><a class="btn btn-sm btn-outline-primary"
                                    href="{{ route('shops.edit', $shop) }}">Modifier</a>
                                <form class="d-inline" method="POST" action="{{ route('shops.toggle', $shop) }}">@csrf
                                    @method('PATCH')<button class="btn btn-sm btn-outline-secondary"
                                        type="submit">{{ $shop->is_active ? 'Désactiver' : 'Activer' }}</button></form>
                            </td>
                    </tr>@empty<tr>
                            <td colspan="5" class="text-center text-secondary py-4">Aucune boutique.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $shops->links() }}</div>
    </div>
@endsection
