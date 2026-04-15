@extends('layouts.cuniapp')

@section('title', 'Détails Lapereau #' . $lapereau->code)

@section('content')
<div class="page-header">
    <div>
        <h2 class="page-title"><i class="bi bi-collection"></i> Détails Lapereau</h2>
        <div class="breadcrumb">
            <a href="{{ route('dashboard') }}">Tableau de bord</a>
            <span>/</span>
            <a href="{{ route('naissances.index') }}">Naissances</a>
            <span>/</span>
            <a href="{{ route('naissances.show', $lapereau->naissance_id) }}">Portée #{{ $lapereau->naissance_id }}</a>
            <span>/</span>
            <span>{{ $lapereau->code }}</span>
        </div>
    </div>
    <div style="display: flex; gap: 12px;">
        <a href="{{ route('naissances.show', $lapereau->naissance_id) }}" class="btn-cuni secondary">
            <i class="bi bi-arrow-left"></i> Retour à la portée
        </a>
        @if($peutVerifierSexe)
        <a href="{{ route('naissances.edit', $lapereau->naissance_id) }}" class="btn-cuni primary">
            <i class="bi bi-pencil"></i> Modifier
        </a>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Info -->
    <div class="lg:col-span-2">
        
        {{-- ✅ Carte Informations Générales --}}
        <div class="cuni-card">
            <div class="card-header-custom">
                <h3 class="card-title"><i class="bi bi-info-circle"></i> Informations du Lapereau</h3>
            </div>
            <div class="card-body">
                <div class="settings-grid">
                    <div class="form-group">
                        <label class="form-label">Code</label>
                        <p class="fw-semibold" style="font-family: 'JetBrains Mono', monospace;">{{ $lapereau->code }}</p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nom</label>
                        <p>{{ $lapereau->nom ?? '-' }}</p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Sexe</label>
                        @if($lapereau->sex)
                            <span class="badge" style="background: {{ $lapereau->sex === 'male' ? 'rgba(59, 130, 246, 0.1)' : 'rgba(236, 72, 153, 0.1)' }}; color: {{ $lapereau->sex === 'male' ? '#3B82F6' : '#EC4899' }};">
                                <i class="bi bi-gender-{{ $lapereau->sex }}"></i> {{ $lapereau->sex === 'male' ? 'Mâle' : 'Femelle' }}
                            </span>
                        @else
                            <span class="badge" style="background: rgba(107, 114, 128, 0.1); color: #6B7D95;">
                                <i class="bi bi-question-circle"></i> À vérifier
                            </span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label class="form-label">État</label>
                        @if($lapereau->etat === 'vivant')
                            <span class="badge" style="background: rgba(16, 185, 129, 0.1); color: #10B981;">Vivant</span>
                        @elseif($lapereau->etat === 'vendu')
                            <span class="badge" style="background: rgba(245, 158, 11, 0.1); color: #F59E0B;">Vendu</span>
                        @else
                            <span class="badge" style="background: rgba(239, 68, 68, 0.1); color: #EF4444;">Mort</span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label class="form-label">Poids naissance</label>
                        <p>{{ $lapereau->poids_naissance ?? '-' }} g</p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Santé</label>
                        <span class="badge" style="background: rgba(16, 185, 129, 0.1); color: #10B981;">{{ $lapereau->etat_sante ?? 'Bon' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ✅ Section Vaccination (TOOLTIP FLOTTANT - PAS DE SCROLL) --}}
        @php
            $vaccinations = $lapereau->vaccinations ?? collect();
            $hasSimpleVaccin = !empty($lapereau->vaccin_date);
            $totalVaccins = $vaccinations->count() + ($hasSimpleVaccin ? 1 : 0);
        @endphp

        <div class="cuni-card" style="margin-top: 24px;">
            <div class="card-header-custom">
                <h3 class="card-title"><i class="bi bi-shield-check"></i> Vaccinations ({{ $totalVaccins }})</h3>
            </div>
            
            @if($totalVaccins > 0)
            <div class="card-body" style="padding: 0; overflow: visible !important;"> <!-- Force visible -->
                <div class="table-responsive" style="overflow: visible !important;"> <!-- Force visible -->
                    <table class="table table-bordered table-striped mb-0" style="border-color: var(--surface-border); font-size: 13px;">
                        <thead style="background: var(--surface-alt);">
                            <tr>
                                <th style="padding: 8px 12px; font-weight: 600; font-size: 12px; text-transform: uppercase; color: var(--text-secondary);">Vaccin</th>
                                <th style="padding: 8px 12px; font-weight: 600; font-size: 12px; text-transform: uppercase; color: var(--text-secondary);">Date</th>
                                <th style="padding: 8px 12px; font-weight: 600; font-size: 12px; text-transform: uppercase; color: var(--text-secondary); text-align: center;">Dose</th>
                                <th style="padding: 8px 12px; font-weight: 600; font-size: 12px; text-transform: uppercase; color: var(--text-secondary);">Rappel</th>
                                <th style="padding: 8px 12px; font-weight: 600; font-size: 12px; text-transform: uppercase; color: var(--text-secondary);">Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- ✅ Vaccins Multiples --}}
                            @forelse($vaccinations->sortByDesc('date_administration') as $vaccin)
                            <tr style="border-bottom: 1px solid var(--surface-border);">
                                <td style="padding: 8px 12px; vertical-align: middle;">
                                    <strong>{{ $vaccin->nom_lisible }}</strong>
                                    @if($vaccin->type === 'autre' && $vaccin->nom_personnalise)
                                        <br><small style="color: var(--text-tertiary); font-size: 11px;">{{ Str::limit($vaccin->nom_personnalise, 20) }}</small>
                                    @endif
                                </td>
                                <td style="padding: 8px 12px; vertical-align: middle; white-space: nowrap;">
                                    {{ $vaccin->date_administration?->format('d/m/Y') ?? '-' }}
                                </td>
                                <td style="padding: 8px 12px; vertical-align: middle; text-align: center;">
                                    <span class="badge" style="background: rgba(59,130,246,0.1); color: #3B82F6; font-size: 10px; padding: 2px 8px;">#{{ $vaccin->dose_numero ?? 1 }}</span>
                                </td>
                                <td style="padding: 8px 12px; vertical-align: middle; white-space: nowrap;">
                                    @if($vaccin->rappel_prevu)
                                        <span style="color: {{ $vaccin->rappel_prevu->isPast() ? 'var(--accent-red)' : 'var(--text-primary)' }}; font-size: 12px;">
                                            {{ $vaccin->rappel_prevu->format('d/m/Y') }}
                                            @if($vaccin->rappel_prevu->isPast()) <i class="bi bi-exclamation-circle text-danger" title="Rappel dépassé"></i> @endif
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                {{-- ✅ Notes avec Tooltip FLOTTANT (Z-Index Max + Pas de Scroll) --}}
                                <td style="padding: 8px 12px; vertical-align: middle; font-size: 12px; color: var(--text-secondary); max-width: 130px;">
                                    @if($vaccin->notes)
                                        @php $estLongue = strlen($vaccin->notes) > 25; @endphp
                                        <div class="note-hover-wrap">
                                            <span class="note-preview">
                                                {{ Str::limit($vaccin->notes, 25) }}
                                                @if($estLongue) <i class="bi bi-three-dots" style="font-size: 10px; color: var(--primary);"></i> @endif
                                            </span>
                                            <div class="note-hover-box">{{ $vaccin->notes }}</div>
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            @endforelse

                            {{-- ✅ Vaccin Simple (Ancien format) --}}
                            @if($hasSimpleVaccin)
                            <tr style="background: rgba(59, 130, 246, 0.05);">
                                <td style="padding: 8px 12px; vertical-align: middle;">
                                    <strong>{{ $lapereau->vaccin_nom }}</strong> <span class="badge bg-info" style="font-size: 9px; padding: 2px 6px;">Ancien</span>
                                </td>
                                <td style="padding: 8px 12px; vertical-align: middle; white-space: nowrap;">
                                    {{ $lapereau->vaccin_date?->format('d/m/Y') ?? '-' }}
                                </td>
                                <td style="padding: 8px 12px; vertical-align: middle; text-align: center;">
                                    <span class="badge" style="background: rgba(59,130,246,0.1); color: #3B82F6; font-size: 10px; padding: 2px 8px;">#{{ $lapereau->vaccin_dose_numero ?? 1 }}</span>
                                </td>
                                <td style="padding: 8px 12px; vertical-align: middle; white-space: nowrap;">
                                    @if($lapereau->vaccin_rappel_prevu)
                                        {{ $lapereau->vaccin_rappel_prevu->format('d/m/Y') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                {{-- ✅ Notes avec Tooltip FLOTTANT --}}
                                <td style="padding: 8px 12px; vertical-align: middle; font-size: 12px; color: var(--text-secondary); max-width: 130px;">
                                    @if($lapereau->vaccin_notes)
                                        @php $estLongue = strlen($lapereau->vaccin_notes) > 25; @endphp
                                        <div class="note-hover-wrap">
                                            <span class="note-preview">
                                                {{ Str::limit($lapereau->vaccin_notes, 25) }}
                                                @if($estLongue) <i class="bi bi-three-dots" style="font-size: 10px; color: var(--primary);"></i> @endif
                                            </span>
                                            <div class="note-hover-box">{{ $lapereau->vaccin_notes }}</div>
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            @else
            <div class="card-body text-center text-muted py-3" style="font-size: 13px;">
                <i class="bi bi-x-circle mb-1"></i><br>
                Aucun vaccin enregistré
            </div>
            @endif
        </div>

        {{-- ✅ Observations --}}
        @if($lapereau->observations)
        <div class="cuni-card" style="margin-top: 24px;">
            <div class="card-header-custom">
                <h3 class="card-title"><i class="bi bi-chat-left-text"></i> Observations</h3>
            </div>
            <div class="card-body">
                <p style="white-space: pre-wrap; margin: 0; font-size: 13px;">{{ $lapereau->observations }}</p>
            </div>
        </div>
        @endif
    </div>

    <!-- Sidebar: Naissance & Parents -->
    <div>
        <div class="cuni-card">
            <div class="card-header-custom">
                <h3 class="card-title"><i class="bi bi-egg-fill"></i> Naissance</h3>
            </div>
            <div class="card-body">
                <div class="space-y-3">
                    <div>
                        <label class="text-sm text-gray-500">Date de naissance</label>
                        <p class="font-semibold">{{ $lapereau->naissance?->miseBas?->date_mise_bas?->format('d/m/Y') ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Âge actuel</label>
                        <p class="font-semibold" style="color: var(--primary);">{{ $ageJours }} jours</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Mère</label>
                        <p class="font-semibold">{{ $lapereau->naissance?->miseBas?->femelle?->nom ?? 'N/A' }}</p>
                    </div>
                    @if($lapereau->naissance?->miseBas?->saillie?->male)
                    <div>
                        <label class="text-sm text-gray-500">Père</label>
                        <p class="font-semibold">{{ $lapereau->naissance->miseBas->saillie->male->nom ?? 'N/A' }}</p>
                    </div>
                    @endif
                    <hr style="border-color: var(--surface-border);">
                    <a href="{{ route('naissances.show', $lapereau->naissance_id) }}" class="btn-cuni secondary" style="width: 100%; font-size: 13px; padding: 6px 12px;">
                        <i class="bi bi-eye"></i> Voir la portée
                    </a>
                </div>
            </div>
        </div>

        @if(!$peutVerifierSexe && !$lapereau->sex)
        <div class="cuni-card" style="margin-top: 24px; border-left: 4px solid var(--accent-orange);">
            <div class="card-body py-2">
                <p style="margin: 0; font-size: 13px; color: var(--text-secondary);">
                    <i class="bi bi-info-circle"></i> Sexe vérifiable dans <strong>{{ max(0, 10 - $ageJours) }}j</strong>
                </p>
            </div>
        </div>
        @endif

        <!-- Stats rapides -->
        <div class="cuni-card" style="margin-top: 24px;">
            <div class="card-header-custom">
                <h3 class="card-title"><i class="bi bi-graph-up"></i> Stats</h3>
            </div>
            <div class="card-body py-2">
                <div class="space-y-2">
                    <div style="display: flex; justify-content: space-between; font-size: 13px;">
                        <span class="text-gray-500">Poids</span>
                        <span class="font-semibold">{{ $lapereau->poids_naissance ?? '-' }}g</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 13px;">
                        <span class="text-gray-500">Santé</span>
                        <span class="font-semibold">{{ $lapereau->etat_sante ?? 'Bon' }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 13px;">
                        <span class="text-gray-500">Vaccins</span>
                        <span class="font-semibold" style="color: var(--accent-green);">{{ $totalVaccins }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ✅ CSS FINAL (Suppression totale des scrollbars) --}}
@push('styles')
<style>
    /* 1️⃣ Force les conteneurs parents à ne jamais cacher ou scroller le tooltip */
    .card-body, .table-responsive, .cuni-card, .table {
        overflow: visible !important;
    }
    
    /* Wrapper stable */
    .note-hover-wrap { position: relative; display: inline-block; width: 100%; }
    
    /* Texte tronqué dans la cellule */
    .note-preview { 
        white-space: nowrap; 
        overflow: hidden; 
        text-overflow: ellipsis; 
        max-width: 110px; 
        display: inline-block; 
        vertical-align: middle; 
        border-bottom: 1px dashed var(--surface-border);
        padding-bottom: 1px;
        cursor: help;
    }
    
    /* ✅ BOÎTE TOOLTIP (Style "Pop-up" sans scroll) */
    .note-hover-box {
        display: none;
        position: absolute;
        top: 100%; /* Affiché juste en dessous */
        left: 0;
        margin-top: 6px;
        
        background: var(--surface, #ffffff);
        color: var(--text-primary, #111827);
        padding: 12px 16px;
        border: 1px solid var(--surface-border, #e5e7eb);
        border-radius: 8px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.25);
        font-size: 13px;
        line-height: 1.5;
        
        /* ✅ Configuration anti-scrollbar stricte */
        white-space: pre-wrap;      /* Conserve les sauts de ligne */
        word-wrap: break-word;      /* Coupe les mots longs */
        overflow: visible;          /* ✅ C'est la clé : autorise le contenu à sortir sans créer de barre */
        max-height: none;           /* ✅ Pas de limite de hauteur */
        height: auto;               /* ✅ Hauteur automatique */
        
        /* ✅ Dimensions flexibles */
        min-width: 200px;
        max-width: 80vw;            /* S'adapte à l'écran mais ne sort pas totalement */
        
        /* ✅ Toujours au premier plan */
        z-index: 999999;
        pointer-events: auto;
    }
    
    /* Flèche vers le haut */
    .note-hover-box::after {
        content: '';
        position: absolute;
        top: -6px;
        left: 24px;
        border: 6px solid transparent;
        border-bottom-color: var(--surface, #ffffff);
    }

    /* Affichage immédiat au survol */
    .note-hover-wrap:hover .note-hover-box { display: block; }
    
    /* Table compacte */
    .table-bordered td, .table-bordered th { border-color: var(--surface-border, #e5e7eb) !important; }
    
    /* Responsive */
    @media (max-width: 768px) {
        .table-responsive table { font-size: 12px; }
        .table-responsive th:nth-child(4),
        .table-responsive td:nth-child(4) { display: none; }
        .table-responsive td:nth-child(5) { max-width: 90px !important; }
        .note-preview { max-width: 80px !important; }
    }
</style>
@endpush
@endsection