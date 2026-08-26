@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-xl-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <p class="text-secondary mb-1">Administration</p>
                    <h1 class="h2 mb-0">Nouvel utilisateur</h1>
                </div><a class="btn btn-outline-secondary" href="{{ route('dashboard') }}">Retour</a>
            </div>
            <div class="card pios-panel border-0">
                <div class="card-body p-4 p-lg-5">
                    <form method="POST" action="{{ route('users.store') }}">@csrf<div class="row g-3">
                            <div class="col-md-6"><label class="form-label" for="name">Nom</label><input
                                    class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                            </div>
                            <div class="col-md-6"><label class="form-label" for="email">Email</label><input
                                    class="form-control" type="email" id="email" name="email"
                                    value="{{ old('email') }}" required></div>
                            <div class="col-md-6"><label class="form-label" for="password">Mot de passe
                                    temporaire</label><input class="form-control" type="password" id="password"
                                    name="password" required></div>
                            <div class="col-md-6"><label class="form-label"
                                    for="password_confirmation">Confirmation</label><input class="form-control"
                                    type="password" id="password_confirmation" name="password_confirmation" required></div>
                            <div class="col-md-6"><label class="form-label" for="role">Rôle</label><select
                                    class="form-select" id="role" name="role" required>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @if ($errors->any())
                            <div class="alert alert-danger mt-4">{{ $errors->first() }}</div>
                        @endif
                        <button class="btn btn-primary mt-4" type="submit">Créer l’utilisateur</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
