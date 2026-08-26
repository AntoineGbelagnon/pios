@extends('layouts.guest')

@section('content')
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-12 col-sm-9 col-md-6 col-lg-4">
            <div class="text-center mb-4">
                <div class="pios-brand fs-3">PIOS</div>
                <h1 class="h2 mt-3">Verification de securite</h1>
                <p class="text-secondary">
                    Saisissez le code a six chiffres envoye a <strong>{{ $recipient }}</strong>.
                    Il reste valable {{ $expiresInMinutes }} minutes.
                </p>
            </div>

            <div class="card pios-panel border-0">
                <div class="card-body p-4 p-lg-5">
                    @if (session('status'))
                        <div class="alert alert-success" role="status">{{ session('status') }}</div>
                    @endif

                    <form method="POST" action="{{ route('two-factor.verify') }}">
                        @csrf
                        <label class="form-label" for="code">Code de securite</label>
                        <input class="form-control form-control-lg text-center mb-3" type="text" id="code"
                            name="code" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}"
                            maxlength="6" required autofocus>

                        @error('code')
                            <div class="alert alert-danger" role="alert">{{ $message }}</div>
                        @enderror

                        <button class="btn btn-primary w-100" type="submit">Verifier et se connecter</button>
                    </form>

                    <form method="POST" action="{{ route('two-factor.resend') }}" class="mt-3">
                        @csrf
                        <button class="btn btn-link w-100" type="submit">Renvoyer un code</button>
                    </form>

                    <a class="btn btn-link w-100" href="{{ route('login') }}">Recommencer la connexion</a>
                </div>
            </div>
        </div>
    </div>
@endsection
