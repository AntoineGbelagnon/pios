@extends('layouts.app')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div><p class="text-secondary mb-1">Systeme</p><h1 class="h2 mb-0">Notifications @if($unreadCount > 0)<span class="badge text-bg-danger">{{ $unreadCount }}</span>@endif</h1></div>
    <form method="POST" action="{{ route('notifications.markAllRead') }}">@csrf<button type="submit" class="btn btn-outline-primary btn-sm"><i class="bi bi-check-all"></i> Tout marquer lu</button></form>
</div>
@if (session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
<div class="card pios-panel border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th>Type</th><th>Titre</th><th>Message</th><th>Date</th><th></th></tr></thead>
            <tbody>
                @forelse($notifications as $n)
                <tr class="{{ !$n->is_read ? 'table-light' : '' }}">
                    <td><span class="badge text-bg-light">{{ ucfirst($n->type) }}</span></td>
                    <td class="fw-semibold">{{ $n->title }}</td>
                    <td>{{ Str::limit($n->message, 80) }}</td>
                    <td>{{ $n->created_at->diffForHumans() }}</td>
                    <td>
                        @if(!$n->is_read)
                        <form method="POST" action="{{ route('notifications.markRead', $n) }}" class="d-inline">@csrf<button class="btn btn-sm btn-outline-success"><i class="bi bi-check"></i></button></form>
                        @endif
                    </td>
                </tr>
                @empty<tr><td colspan="5" class="text-center text-secondary py-4">Aucune notification</td></tr>@endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $notifications->links() }}</div>
</div>
@endsection
