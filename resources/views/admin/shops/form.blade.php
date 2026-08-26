@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-xl-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <p class="text-secondary mb-1">Organisation</p>
                    <h1 class="h2 mb-0">{{ $shop->exists ? 'Modifier la boutique' : 'Nouvelle boutique' }}</h1>
                </div><a class="btn btn-outline-secondary" href="{{ route('shops.index') }}">Retour</a>
            </div>
            <div class="card pios-panel border-0">
                <div class="card-body p-4">
                    <form method="POST" action="{{ $shop->exists ? route('shops.update', $shop) : route('shops.store') }}">
                        @csrf @if ($shop->exists)
                            @method('PUT')
                        @endif
                        <div class="row g-3">
                            <div class="col-md-7"><label class="form-label" for="name">Nom</label><input
                                    class="form-control" id="name" name="name"
                                    value="{{ old('name', $shop->name) }}" required></div>
                            <div class="col-md-5"><label class="form-label" for="code">Code</label><input
                                    class="form-control" id="code" name="code"
                                    value="{{ old('code', $shop->code) }}" required></div>
                            <div class="col-md-7"><label class="form-label" for="address">Adresse</label><input
                                    class="form-control" id="address" name="address"
                                    value="{{ old('address', $shop->address) }}"></div>
                            <div class="col-md-5"><label class="form-label" for="city">Ville</label><input
                                    class="form-control" id="city" name="city"
                                    value="{{ old('city', $shop->city) }}"></div>
                            <div class="col-md-6"><label class="form-label" for="phone">Téléphone</label><input
                                    class="form-control" id="phone" name="phone"
                                    value="{{ old('phone', $shop->phone) }}"></div>
                        </div>
                        @if ($errors->any())
                            <div class="alert alert-danger mt-4">{{ $errors->first() }}</div>
                        @endif
                        <button class="btn btn-primary mt-4" type="submit">Enregistrer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
