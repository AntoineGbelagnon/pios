@extends('layouts.guest')

@section('content')
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-12 col-sm-9 col-md-6 col-lg-4">
            <div class="text-center mb-4">
                <div class="pios-brand fs-3">PIOS</div>
                <h1 class="h2 mt-3">Bon retour</h1>
                <p class="text-secondary">Connectez-vous à votre espace de gestion.</p>
            </div>
            <div class="card pios-panel border-0">
                <div class="card-body p-4 p-lg-5">
                    <form method="POST" action="{{ route('login.store') }}">@csrf<label class="form-label"
                            for="email">Email</label><input class="form-control mb-3" type="email" id="email"
                            name="email" value="{{ old('email', $defaultEmail) }}" required autofocus><label class="form-label"
                            for="password">Mot de passe</label><input class="form-control mb-3" type="password"
                            id="password" name="password" required>
                        <div class="form-check mb-3"><input class="form-check-input" type="checkbox" id="remember"
                                name="remember"><label class="form-check-label" for="remember">Se souvenir de moi</label>
                        </div>
                        @if ($errors->any())
                            <div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>
                        @endif
                        <button class="btn btn-primary w-100" type="submit">Continuer</button>
                    </form>
                </div>
            </div>
            <p class="text-center text-secondary mt-4 mb-0">L’accès est créé par un administrateur depuis le dashboard.</p>
        </div>
    </div>
@endsection
