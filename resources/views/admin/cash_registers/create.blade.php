@extends('layouts.app')
@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <p class="text-secondary mb-1">Caisse</p>
            <h1 class="h2 mb-0">Ouvrir la caisse</h1>
        </div>
        <a href="{{ route('cash_registers.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i>
            Retour</a>
    </div>
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card pios-panel border-0">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('cash_registers.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Boutique *</label>
                            <select name="shop_id" class="form-select" required>
                                <option value="">Choisir...</option>
                                @foreach ($shops as $shop)
                                    <option value="{{ $shop->id }}">{{ $shop->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Fonds initial (FCFA) *</label>
                            <input type="number" name="opening_amount" class="form-control" value="0" min="0"
                                step="100" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="opening_notes" class="form-control" rows="2"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-box-arrow-in-right"></i> Ouvrir
                            la caisse</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
