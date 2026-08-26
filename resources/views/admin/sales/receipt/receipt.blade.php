<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recu {{ $sale->invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Courier New', monospace; font-size: 12px; width: 80mm; margin: 0 auto; padding: 2mm; color: #000; background: #fff; }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .line { border-top: 1px dashed #000; margin: 3mm 0; }
        .line-double { border-top: 2px solid #000; margin: 3mm 0; }
        table { width: 100%; border-collapse: collapse; }
        table td { padding: 1mm 0; vertical-align: top; }
        .col-name { width: 50%; }
        .col-qty { width: 12%; text-align: center; }
        .col-price { width: 18%; text-align: right; }
        .col-total { width: 20%; text-align: right; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .small { font-size: 10px; }
        .totals td { padding: 1mm 0; }
        .footer { margin-top: 5mm; text-align: center; font-size: 10px; }
        @media print {
            body { width: 80mm; margin: 0; padding: 2mm; }
            .no-print { display: none; }
        }
        @media screen {
            body { border: 1px solid #ccc; margin-top: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
            .print-btn { display: block; text-align: center; margin: 10px auto; }
        }
    </style>
</head>
<body>

<div class="no-print print-btn" style="margin-bottom:10px;">
    <button onclick="window.print()" style="padding:8px 20px;font-size:14px;cursor:pointer;">🖨️ Imprimer le recu</button>
    <button onclick="window.close()" style="padding:8px 20px;font-size:14px;cursor:pointer;margin-left:10px;">Fermer</button>
</div>

{{-- En-tete --}}
<div class="center">
    @if($company->logo)
        <img src="{{ asset('storage/' . $company->logo) }}" alt="Logo" style="max-width:50mm;max-height:15mm;margin-bottom:2mm;">
    @endif
    <div class="bold" style="font-size:14px;">{{ $company->legal_name ?: $company->name }}</div>
    @if($company->address)<div class="small">{{ $company->address }}</div>@endif
    @if($company->phone)<div class="small">Tel: {{ $company->phone }}</div>@endif
    @if($company->tax_id)<div class="small">NIF: {{ $company->tax_id }}</div>@endif
</div>

<div class="line-double"></div>

{{-- Infos recu --}}
<div class="center bold" style="font-size:14px;">RECU DE VENTE</div>
<div class="line"></div>

<table>
    <tr><td class="small">N° Recu</td><td class="text-right bold">{{ $sale->invoice_number }}</td></tr>
    <tr><td class="small">Date</td><td class="text-right">{{ $sale->created_at->format('d/m/Y H:i') }}</td></tr>
    <tr><td class="small">Vendeur</td><td class="text-right">{{ $sale->user->name }}</td></tr>
    @if($sale->shop)<tr><td class="small">Boutique</td><td class="text-right">{{ $sale->shop->name }}</td></tr>@endif
    @if($sale->customer)<tr><td class="small">Client</td><td class="text-right">{{ $sale->customer->name }}</td></tr>@endif
</table>

<div class="line"></div>

{{-- Articles --}}
<table>
    <thead>
        <tr>
            <td class="bold small col-name">DESIGNATION</td>
            <td class="bold small col-qty">QTE</td>
            <td class="bold small col-price">PRIX</td>
            <td class="bold small col-total">TOTAL</td>
        </tr>
    </thead>
    <tbody>
        @foreach($sale->items as $item)
        <tr>
            <td class="small">
                {{ $item->product_name }}
                @if($item->discount_amount > 0)
                    <br><span class="text-secondary">(-{{ number_format($item->discount_amount, 0, ',', ' ') }})</span>
                @endif
            </td>
            <td class="col-qty">{{ $item->quantity }}</td>
            <td class="col-price">{{ number_format($item->unit_price, 0, ',', ' ') }}</td>
            <td class="col-total">{{ number_format($item->total, 0, ',', ' ') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="line"></div>

{{-- Totaux --}}
<table class="totals">
    <tr><td class="small">Sous-total</td><td class="text-right">{{ number_format($sale->subtotal, 0, ',', ' ') }} FCFA</td></tr>
    @if($sale->discount_amount > 0)
        <tr><td class="small text-danger">Remise</td><td class="text-right text-danger">-{{ number_format($sale->discount_amount, 0, ',', ' ') }} FCFA</td></tr>
    @endif
    @if($sale->tax_amount > 0)
        <tr><td class="small">TVA ({{ $sale->tax_percent }}%)</td><td class="text-right">{{ number_format($sale->tax_amount, 0, ',', ' ') }} FCFA</td></tr>
    @endif
</table>

<div class="line-double"></div>

<table class="totals">
    <tr><td class="bold" style="font-size:14px;">TOTAL A PAYER</td><td class="text-right bold" style="font-size:14px;">{{ number_format($sale->total, 0, ',', ' ') }} FCFA</td></tr>
    <tr><td class="small">Mode de paiement</td><td class="text-right">{{ $sale->payment_method_label }}</td></tr>
    <tr><td class="small">Montant paye</td><td class="text-right">{{ number_format($sale->amount_paid, 0, ',', ' ') }} FCFA</td></tr>
    @if($sale->change_amount > 0)
        <tr><td class="small bold">Monnaie rendue</td><td class="text-right bold">{{ number_format($sale->change_amount, 0, ',', ' ') }} FCFA</td></tr>
    @endif
    @if($sale->credit_amount > 0)
        <tr><td class="small text-danger">Reste a payer (credit)</td><td class="text-right text-danger">{{ number_format($sale->credit_amount, 0, ',', ' ') }} FCFA</td></tr>
    @endif
</table>

{{-- Details paiement mixte --}}
@if($sale->payment_method === 'mixed' && $sale->payment_details)
<div class="line"></div>
<div class="small bold">DETAILS PAIEMENT MIXTE :</div>
@foreach($sale->payment_details as $detail)
    <div class="small">
        @if(isset($detail['method']))
            {{ match($detail['method'] ?? '') { 'cash' => 'Especes', 'card' => 'Carte', 'mobile_money' => 'Mobile Money', 'bank_transfer' => 'Virement', default => $detail['method'] ?? '' } }} :
            {{ number_format($detail['amount'] ?? 0, 0, ',', ' ') }} FCFA
        @endif
    </div>
@endforeach
@endif

<div class="line"></div>

{{-- Garantie --}}
@php $hasWarranty = $sale->items->contains(function($item) { return $item->product && $item->product->warranty_months > 0; }); @endphp
@if($hasWarranty)
<div class="small">
    <div class="bold">GARANTIE :</div>
    @foreach($sale->items as $item)
        @if($item->product && $item->product->warranty_months > 0)
            <div>{{ $item->product_name }} : {{ $item->product->warranty_months }} mois</div>
        @endif
    @endforeach
</div>
<div class="line"></div>
@endif

{{-- Pied de page --}}
<div class="footer">
    <div class="bold">Merci de votre achat !</div>
    <div class="small" style="margin-top:2mm;">Conservez ce recu pour toute</div>
    <div class="small">reclamation ou retour.</div>
    @if($company->email)<div class="small" style="margin-top:2mm;">{{ $company->email }}</div>@endif
    <div class="small text-secondary" style="margin-top:3mm;">--- {{ config('app.name', 'PIOS') }} ---</div>
</div>

</body>
</html>
