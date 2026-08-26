@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <p class="text-secondary mb-1">Sécurité</p>
        <h1 class="h2 mb-0">Journal d’activité</h1>
    </div>
    <div class="card pios-panel border-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Description</th>
                        <th>Utilisateur</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activities as $activity)
                        <tr>
                            <td><span class="badge text-bg-light">{{ $activity->action ?: 'activité' }}</span></td>
                            <td>{{ $activity->description }}</td>
                            <td>{{ $activity->user_id ?: 'Système' }}</td>
                            <td>{{ $activity->created_at->format('d/m/Y H:i') }}</td>
                    </tr>@empty<tr>
                            <td colspan="4" class="text-center text-secondary py-4">Aucune activité.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $activities->links() }}</div>
    </div>
@endsection
