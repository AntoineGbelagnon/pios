@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <p class="text-secondary mb-1">Administration</p>
        <h1 class="h2 mb-0">Paramètres de l’entreprise</h1>
    </div>
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    <div class="card pios-panel border-0">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('settings.update') }}">@csrf @method('PUT')<div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="company_name">Nom commercial</label><input
                            class="form-control" id="company_name" name="company_name"
                            value="{{ old('company_name', $company->name) }}" required></div>
                    <div class="col-md-6"><label class="form-label" for="legal_name">Raison sociale</label><input
                            class="form-control" id="legal_name" name="legal_name"
                            value="{{ old('legal_name', $company->legal_name) }}"></div>
                    <div class="col-md-6"><label class="form-label" for="email">Email</label><input class="form-control"
                            type="email" id="email" name="email" value="{{ old('email', $company->email) }}"></div>
                    <div class="col-md-6"><label class="form-label" for="phone">Téléphone</label><input
                            class="form-control" id="phone" name="phone" value="{{ old('phone', $company->phone) }}">
                    </div>
                    <div class="col-12"><label class="form-label" for="address">Adresse</label>
                        <textarea class="form-control" id="address" name="address" rows="3">{{ old('address', $company->address) }}</textarea>
                    </div>
                </div>
                @if ($errors->any())
                    <div class="alert alert-danger mt-4">{{ $errors->first() }}</div>
                @endif
                <button class="btn btn-primary mt-4" type="submit">Enregistrer les paramètres</button>
            </form>
        </div>
    </div>
@endsection
