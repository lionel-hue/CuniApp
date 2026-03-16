@extends('layouts.cuniapp')

@section('title', 'Détail Facture #' . $invoice->invoice_number . ' - CuniApp Élevage')

@section('content')
    <div class="page-header">
        <div>
            <h2 class="page-title">
                <i class="bi bi-receipt"></i> Facture #{{ $invoice->invoice_number }}
            </h2>
            <div class="breadcrumb">
                <a href="{{ route('dashboard') }}">Tableau de bord</a>
                <span>/</span>
                <a href="{{ route('invoices.index') }}">Factures</a>
                <span>/</span>
                <span>Détail</span>
            </div>
        </div>

        <!-- Actions Header (Caché à l'impression) -->
        <div class="header-actions no-print">
            <a href="{{ route('invoices.download', $invoice) }}" class="btn-cuni primary">
                <i class="bi bi-download"></i> PDF
            </a>
            {{-- <a href="{{ route('invoices.print', $invoice) }}" target="_blank" class="btn-cuni secondary">
                <i class="bi bi-printer"></i> Imprimer
            </a> --}}
            <a href="{{ route('invoices.index') }}" class="btn-cuni light">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Colonne Gauche : Détails Facture -->
        <div class="lg:col-span-2">
            <!-- En-tête Facture -->
            <div class="cuni-card mb-6">
                <div class="card-body p-5">
                    <div class="flex justify-between items-start mb-6 flex-wrap gap-4">
                        <div>
                            <h3 class="text-2xl font-bold" style="color: var(--primary)">CuniApp Élevage</h3>
                            <p class="text-sm text-gray-500 mt-1">Solution de gestion cunicole intelligente</p>
                            <p class="text-sm text-gray-500">support@cuniapp.com</p>
                        </div>
                        <div class="text-right">
                            <h1 class="text-3xl font-bold text-gray-800">#{{ $invoice->invoice_number }}</h1>
                            <p class="text-sm text-gray-500 mt-1">Émise le : {{ $invoice->invoice_date->format('d/m/Y') }}
                            </p>

                            @if ($invoice->status === 'paid')
                                <span class="badge mt-2"
                                    style="background: rgba(16, 185, 129, 0.1); color: #10B981; padding: 6px 12px;">
                                    <i class="bi bi-check-circle-fill"></i> Payée
                                </span>
                            @elseif($invoice->status === 'pending')
                                <span class="badge mt-2"
                                    style="background: rgba(245, 158, 11, 0.1); color: #F59E0B; padding: 6px 12px;">
                                    <i class="bi bi-clock-fill"></i> En attente
                                </span>
                            @else
                                <span class="badge mt-2"
                                    style="background: rgba(107, 114, 128, 0.1); color: #6B7280; padding: 6px 12px;">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Info Client -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <div>
                            <h4 class="text-xs font-bold text-gray-400 uppercase mb-2">Facturé à</h4>
                            <p class="font-semibold text-gray-800">{{ $invoice->user->name ?? 'Client' }}</p>
                            <p class="text-sm text-gray-600">{{ $invoice->user->email ?? '' }}</p>
                            @if ($invoice->user->farm_name)
                                <p class="text-sm text-gray-600 mt-1"><i class="bi bi-shop"></i>
                                    {{ $invoice->user->farm_name }}</p>
                            @endif
                        </div>
                        <div class="text-right md:text-left">
                            <h4 class="text-xs font-bold text-gray-400 uppercase mb-2">Références</h4>
                            <p class="font-semibold text-gray-800">Type :
                                {{ ucfirst($invoice->invoice_type ?? 'Standard') }}</p>
                            @if ($invoice->reference)
                                <p class="text-sm text-gray-600">Réf : {{ $invoice->reference }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lignes de la facture -->
            <div class="cuni-card">
                <div class="card-header-custom">
                    <h3 class="card-title">Détail des prestations</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 text-uppercase text-muted fw-semibold small">Description</th>
                                    <th class="text-center text-uppercase text-muted fw-semibold small">Période / Qté</th>
                                    <th class="text-end pe-4 text-uppercase text-muted fw-semibold small">Montant</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- 
                                   NOTE: Si tu as une table 'invoice_items' et une relation 'items' dans ton modèle Invoice,
                                   décommente la boucle @foreach ci-dessous. 
                                   Sinon, le bloc @else affichera une ligne unique avec le total.
                                --}}
                                @if (isset($invoice->items) && $invoice->items->count() > 0)
                                    @foreach ($invoice->items as $item)
                                        <tr class="border-bottom border-light">
                                            <td class="ps-4">
                                                <p class="fw-semibold text-dark">{{ $item->description }}</p>
                                                @if ($item->details)
                                                    <small class="text-muted">{{ $item->details }}</small>
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $item->quantity ?? 1 }}</td>
                                            <td class="text-end pe-4 fw-bold" style="color: var(--primary)">
                                                {{ number_format($item->total, 0, ',', ' ') }} FCFA
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    {{-- Affichage par défaut si pas de détails ligne par ligne --}}
                                    <tr>
                                        <td class="ps-4">
                                            <p class="fw-semibold text-dark">
                                                Abonnement {{ ucfirst($invoice->invoice_type ?? 'CuniApp') }}
                                            </p>
                                            <small class="text-muted">
                                                Accès à la plateforme de gestion du
                                                {{ $invoice->start_date ? $invoice->start_date->format('d/m/Y') : '01/01/2026' }}
                                                au
                                                {{ $invoice->end_date ? $invoice->end_date->format('d/m/Y') : '31/12/2026' }}
                                            </small>
                                        </td>
                                        <td class="text-center">1</td>
                                        <td class="text-end pe-4 fw-bold" style="color: var(--primary)">
                                            {{ number_format($invoice->total_amount, 0, ',', ' ') }} FCFA
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Colonne Droite : Totaux -->
        <div class="lg:col-span-1">
            <div class="cuni-card sticky-top" style="top: 20px;">
                <div class="card-body p-5">
                    <h4 class="card-title mb-4">Récapitulatif</h4>

                    <div class="space-y-3">
                        {{-- Gestion sécurisée des sous-totaux (évite les erreurs si colonne absente) --}}
                        @php
                            $subtotal = $invoice->subtotal ?? $invoice->total_amount;
                            $tax = $invoice->tax_amount ?? 0;
                            $discount = $invoice->discount ?? 0;
                        @endphp

                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Sous-Total HT</span>
                            <span class="font-semibold">{{ number_format($subtotal, 0, ',', ' ') }} FCFA</span>
                        </div>

                        @if ($tax > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">TVA</span>
                                <span class="font-semibold">{{ number_format($tax, 0, ',', ' ') }} FCFA</span>
                            </div>
                        @endif

                        @if ($discount > 0)
                            <div class="flex justify-between text-sm text-green-600">
                                <span>Remise</span>
                                <span>- {{ number_format($discount, 0, ',', ' ') }} FCFA</span>
                            </div>
                        @endif

                        <div class="border-t border-gray-200 my-3 pt-3 flex justify-between items-center">
                            <span class="text-base font-bold text-gray-800">Net à Payer</span>
                            <span class="text-xl font-bold" style="color: var(--primary)">
                                {{ number_format($invoice->total_amount, 0, ',', ' ') }} FCFA
                            </span>
                        </div>
                    </div>

                    <!-- Statut Paiement -->
                    <div
                        class="mt-6 p-4 rounded-lg border {{ $invoice->status === 'paid' ? 'bg-green-50 border-green-100' : 'bg-amber-50 border-amber-100' }}">
                        @if ($invoice->status === 'paid')
                            <div class="flex items-center gap-2 mb-2">
                                <i class="bi bi-check-circle-fill text-green-600"></i>
                                <h5 class="text-sm font-bold text-green-800">Facture Acquittée</h5>
                            </div>
                            <p class="text-xs text-green-600">
                                @if ($invoice->payment_date)
                                    Payée le {{ $invoice->payment_date->format('d/m/Y à H:i') }}
                                @else
                                    Paiement enregistré le {{ $invoice->updated_at->format('d/m/Y') }}
                                @endif
                            </p>
                        @else
                            <div class="flex items-center gap-2 mb-2">
                                <i class="bi bi-exclamation-circle-fill text-amber-600"></i>
                                <h5 class="text-sm font-bold text-amber-800">En attente de paiement</h5>
                            </div>
                            <p class="text-xs text-amber-600">
                                Merci de procéder au règlement pour activer votre abonnement.
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

       
        
    @endsection
