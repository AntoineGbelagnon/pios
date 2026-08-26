@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-xl-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <p class="text-secondary mb-1">Produits</p>
                    <h1 class="h2 mb-0">{{ $brand->exists ? 'Modifier la marque' : 'Nouvelle marque' }}</h1>
                </div>
                <a class="btn btn-outline-secondary" href="{{ route('brands.index') }}">Retour</a>
            </div>
            <div class="card pios-panel border-0">
                <div class="card-body p-4">
                    <form method="POST" action="{{ $brand->exists ? route('brands.update', $brand) : route('brands.store') }}">
                        @csrf
                        @if ($brand->exists)
                            @method('PUT')
                        @endif
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label" for="name">Nom</label>
                                <input class="form-control" id="name" name="name" value="{{ old('name', $brand->name) }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $brand->description) }}</textarea>
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $brand->is_active ?? true))>
                                    <label class="form-check-label" for="is_active">Marque active</label>
                                </div>
                            </div>
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
