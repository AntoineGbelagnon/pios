@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-xl-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <p class="text-secondary mb-1">Organisation</p>
                    <h1 class="h2 mb-0">{{ $warehouse->exists ? 'Modifier l’entrepôt' : 'Nouvel entrepôt' }}</h1>
                </div><a class="btn btn-outline-secondary" href="{{ route('warehouses.index') }}">Retour</a>
            </div>
            <div class="card pios-panel border-0">
                <div class="card-body p-4">
                    <form method="POST"
                        action="{{ $warehouse->exists ? route('warehouses.update', $warehouse) : route('warehouses.store') }}">
                        @csrf @if ($warehouse->exists)
                            @method('PUT')
                        @endif
                        <label class="form-label" for="name">
                            Nom</label><input class="form-control mb-3" id="name" name="name"
                            value="{{ old('name', $warehouse->name) }}" required><label class="form-label"
                            for="shop_id">Boutique associée</label><select class="form-select mb-3" id="shop_id"
                            name="shop_id">
                            <option value="">Entrepôt central</option>
                            @foreach ($shops as $shop)
                                <option value="{{ $shop->id }}" @selected(old('shop_id', $warehouse->shop_id) == $shop->id)>{{ $shop->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-check"><input class="form-check-input" type="checkbox" id="is_default"
                                name="is_default" value="1" @checked(old('is_default', $warehouse->is_default))><label
                                class="form-check-label" for="is_default">Définir comme entrepôt par défaut</label></div>
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
