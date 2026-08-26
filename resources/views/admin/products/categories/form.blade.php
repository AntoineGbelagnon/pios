@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-xl-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <p class="text-secondary mb-1">Produits</p>
                    <h1 class="h2 mb-0">{{ $category->exists ? 'Modifier la catégorie' : 'Nouvelle catégorie' }}</h1>
                </div>
                <a class="btn btn-outline-secondary" href="{{ route('categories.index') }}">Retour</a>
            </div>
            <div class="card pios-panel border-0">
                <div class="card-body p-4">
                    <form method="POST" action="{{ $category->exists ? route('categories.update', $category) : route('categories.store') }}">
                        @csrf
                        @if ($category->exists)
                            @method('PUT')
                        @endif
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label" for="name">Nom</label>
                                <input class="form-control" id="name" name="name" value="{{ old('name', $category->name) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="parent_id">Catégorie parente</label>
                                <select class="form-select" id="parent_id" name="parent_id">
                                    <option value="">Aucune (racine)</option>
                                    @foreach ($parentCategories as $pc)
                                        <option value="{{ $pc->id }}" @selected(old('parent_id', $category->parent_id) == $pc->id)>{{ $pc->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $category->description) }}</textarea>
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $category->is_active ?? true))>
                                    <label class="form-check-label" for="is_active">Catégorie active</label>
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
