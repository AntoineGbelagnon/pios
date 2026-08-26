@extends('layouts.app')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div><p class="text-secondary mb-1">SAV</p><h1 class="h2 mb-0">{{ $warranty->warranty_number }}</h1></div>
    <a href="{{ route('warranties.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Retour</a>
</div>
@if (session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card pios-panel border-0">
            <div class="card-body">
                <h5 class="mb-3">Informations</h5>
                <div class="row g-3">
                    <div class="col-md-6"><strong>Client :</strong> {{ $warranty->customer->name ?? '-' }}</div>
                    <div class="col-md-6"><strong>Produit :</strong> {{ $warranty->product->name ?? '-' }}</div>
                    <div class="col-md-6"><strong>Serial :</strong> {{ $warranty->serial_number ?? '-' }}</div>
                    <div class="col-md-6"><strong>Achat :</strong> {{ $warranty->purchase_date->format('d/m/Y') }}</div>
                    <div class="col-md-6"><strong>Expire le :</strong> <span class="{{ $warranty->is_expired ? 'text-danger fw-bold' : '' }}">{{ $warranty->expiry_date->format('d/m/Y') }}</span></div>
                    <div class="col-md-6"><strong>Duree :</strong> {{ $warranty->duration_months }} mois</div>
                    @if($warranty->problem_description)<div class="col-12"><strong>Probleme :</strong> {{ $warranty->problem_description }}</div>@endif
                    @if($warranty->resolution_notes)<div class="col-12"><strong>Resolution :</strong> {{ $warranty->resolution_notes }}</div>@endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card pios-panel border-0"><div class="card-body">
            <h5 class="mb-3">Statut</h5>
            <div class="mb-3"><span class="badge text-bg-{{ match($warranty->status){'active'=>'success','repaired'=>'primary','replaced'=>'info','expired'=>'danger','closed'=>'secondary',default=>'secondary'} }} fs-6">{{ ucfirst($warranty->status) }}</span></div>
            @if($warranty->status === 'active')
            <form method="POST" action="{{ route('warranties.update', $warranty) }}">
                @csrf @method('PUT')
                <div class="mb-3"><label class="form-label">Nouveau statut *</label>
                    <select name="status" class="form-select" required>
                        <option value="repaired">Repare</option><option value="replaced">Remplace</option><option value="closed">Ferme</option>
                    </select></div>
                <div class="mb-3"><label class="form-label">Notes</label><textarea name="resolution_notes" class="form-control" rows="2"></textarea></div>
                <div class="mb-3"><label class="form-label">Cout reparation</label><input type="number" name="repair_cost" class="form-control" min="0" step="100"></div>
                <button type="submit" class="btn btn-primary w-100">Mettre a jour</button>
            </form>
            @endif
        </div></div>
    </div>
</div>
@endsection
