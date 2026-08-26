@extends('layouts.app')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <p class="text-secondary mb-1">Administration</p>
            <h1 class="h2 mb-0">Utilisateurs</h1>
        </div><a class="btn btn-primary" href="{{ route('users.create') }}">Nouvel utilisateur</a>
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
                        <th>Email</th>
                        <th>Statut</th>
                        <th>Créé le</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td class="fw-semibold">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td><span
                                    class="badge text-bg-{{ $user->is_active ? 'success' : 'secondary' }}">{{ $user->is_active ? 'Actif' : 'Inactif' }}</span>
                            </td>
                            <td>{{ $user->created_at->format('d/m/Y') }}</td>
                            <td class="text-end">
                                <form method="POST" action="{{ route('users.toggle', $user) }}">@csrf
                                    @method('PATCH')<button class="btn btn-sm btn-outline-secondary"
                                        type="submit">{{ $user->is_active ? 'Désactiver' : 'Activer' }}</button></form>
                            </td>
                    </tr>@empty<tr>
                            <td colspan="5" class="text-center text-secondary py-4">Aucun utilisateur.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $users->links() }}</div>
    </div>
@endsection
