@extends('layouts.app')

@section('content')
    <h1>Tableau de bord</h1>
    <p>Bienvenue, {{ auth()->user()->name }}.</p>
    @can('manage users')
        <a class="btn btn-primary" href="{{ route('users.create') }}">Créer un utilisateur</a>
    @endcan
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Se déconnecter</button>
    </form>
@endsection
