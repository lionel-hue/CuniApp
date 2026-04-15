@extends('layouts.cuniapp')

@section('title', 'Détails Naissance #{{ $naissance->id }}')

@section('content')
<div class="page-header">
    <div>
        <h2 class="page-title"><i class="bi bi-egg-fill"></i> Détails Naissance #{{ $naissance->id }}</h2>
        <div class="breadcrumb">
            <a href="{{ route('dashboard') }}">Tableau de bord</a>
            <span>/</span>
            <a href="{{ route('naissances.index') }}">Naissances</a>
            <span>/</span>
            <span>#{{ $naissance->id }}</span>
        </div>
    </div>
    <div style="display: flex; gap: 12px;">
        @if ($canVerifySex)
            <a href="{{ route('naissances.edit', $naissance) }}" class="btn-cuni primary">
                <i class="bi bi-pencil"></i> Vérifier le sexe
            </a>
        @else
            <button class="btn-cuni secondary" disabled title="Disponible dans {{ $daysUntilVerification }} jours">
                <i class="bi bi-lock"></i> Vérifier le sexe ({{ $daysUntilVerification }}j)
            </button>
        @endif
        <a href="{{ route('naissances.index') }}" class="btn-cuni secondary">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Info -->
    <div class="lg:col-span-2">
        <div class="cuni-card">
            <div class="card-header-custom">
                <h3 class="card-title"><i class="bi bi-info-circle"></i> Informations Principales</h3>
            </div>
            <div class="card-body">
                <div class="settings-grid">
                    <div class="form-group">
                        <label class="form-label">Femelle</label>
                        <div class="flex items-center gap-2">
                            <span class="font-semibold">{{ $naissance->femelle->nom ?? 'N/A' }}</span>
                            <span class="badge" style="background: rgba(59, 130, 246, 0.1); color: #3B82F6;">
                                {{ $naissance->femelle->code ?? '-' }}
                            </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Date de naissance</label>
                        <p>
                            @if ($naissance->miseBas?->date_mise_bas)
                                {{ \Carbon\Carbon::parse($naissance->miseBas->date_mise_bas)->format('d/m/Y') }}
                            @else
                                <span class="text-muted">Non définie</span>
                            @endif
                        </p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Âge de la portée</label>
                        <p class="fw-semibold" style="color: var(--primary);">
                            {{ $naissance->jours_depuis_naissance }} jours
                        </p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">État de santé (portée)</label>
                        <span class="badge" style="background: rgba(16, 185, 129, 0.1); color: #10B981;">
                            {{ $naissance->etat_sante }}
                        </span>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Vérification du sexe</label>
                        @if ($naissance->sex_verified)
                            <span class="badge" style="background: rgba(16, 185, 129, 0.1); color: #10B981;">
                                <i class="bi bi-check-circle"></i> Vérifié le
                                {{ $naissance->sex_verified_at?->format('d/m/Y') ?? 'Date non disponible' }}
                            </span>
                        @else
                            <span class="badge" style="background: rgba(245, 158, 11, 0.1); color: #F59E0B;">
                                <i class="bi bi-clock"></i> En attente ({{ $daysUntilVerification }} jours restants)
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- ✅ Lapereaux List -->
        <div class="cuni-card" style="margin-top: 24px;">
            <div class="card-header-custom">
                <h3 class="card-title">
                    <i class="bi bi-collection"></i> Liste des Lapereaux ({{ $naissance->total_lapereaux }})
                </h3>
            </div>
            <div class="card-body">
                <!-- Search & Filters -->
                <div style="display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap;">
                    <form method="GET" action="{{ route('naissances.show', $naissance) }}"
                        style="display: flex; gap: 12px; flex: 1; min-width: 300px;">
                        <input type="text" name="search_lapereau" class="form-control"
                            placeholder="Rechercher par nom ou code..." value="{{ request('search_lapereau') }}"
                            style="flex: 1;">
                        <button type="submit" class="btn-cuni primary">
                            <i class="bi bi-search"></i>
                        </button>
                        @if (request('search_lapereau'))
                            <a href="{{ route('naissances.show', $naissance) }}" class="btn-cuni secondary">
                                <i class="bi bi-x"></i>
                            </a>
                        @endif
                    </form>

                    <select name="filter_etat" class="form-select" style="width: auto;"
                        onchange="window.location.href=this.value">
                        <option value="{{ route('naissances.show', $naissance) }}">Tous les états</option>
                        <option value="{{ route('naissances.show', $naissance) }}?filter_etat=vivant"
                            {{ request('filter_etat') == 'vivant' ? 'selected' : '' }}>Vivants</option>
                        <option value="{{ route('naissances.show', $naissance) }}?filter_etat=mort"
                            {{ request('filter_etat') == 'mort' ? 'selected' : '' }}>Morts</option>
                        <option value="{{ route('naissances.show', $naissance) }}?filter_etat=vendu"
                            {{ request('filter_etat') == 'vendu' ? 'selected' : '' }}>Vendus</option>
                    </select>

                    <select name="filter_sex" class="form-select" style="width: auto;"
                        onchange="window.location.href=this.value">
                        <option value="{{ route('naissances.show', $naissance) }}">Tous les sexes</option>
                        <option value="{{ route('naissances.show', $naissance) }}?filter_sex=male"
                            {{ request('filter_sex') == 'male' ? 'selected' : '' }}>Mâles</option>
                        <option value="{{ route('naissances.show', $naissance) }}?filter_sex=female"
                            {{ request('filter_sex') == 'female' ? 'selected' : '' }}>Femelles</option>
                    </select>
                </div>

                @if ($lapereaux->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 lapins-table">
                            <thead class="bg-light">
                                <tr>
                                    <th>Code</th>
                                    <th>Nom</th>
                                    <th>Sexe</th>
                                    <th>Poids</th>
                                    <th>Santé</th>
                                    <th>État</th>
                                    <th>Vaccination</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($lapereaux as $lapereau)
                                    <tr>
                                        {{-- Code --}}
                                        <td style="font-family: 'JetBrains Mono', monospace; font-weight: 600;">
                                            {{ $lapereau->code }}
                                        </td>
                                        
                                        {{-- Nom --}}
                                        <td>{{ $lapereau->nom ?? '-' }}</td>
                                        
                                        {{-- Sexe --}}
                                        <td>
                                            @if ($lapereau->sex)
                                                <span class="badge" style="background: {{ $lapereau->sex === 'male' ? 'rgba(59, 130, 246, 0.1)' : 'rgba(236, 72, 153, 0.1)' }}; color: {{ $lapereau->sex === 'male' ? '#3B82F6' : '#EC4899' }};">
                                                    <i class="bi bi-gender-{{ $lapereau->sex }}"></i> {{ $lapereau->sex === 'male' ? 'Mâle' : 'Femelle' }}
                                                </span>
                                            @else
                                                <span class="badge" style="background: rgba(107, 114, 128, 0.1); color: #6B7D95;">
                                                    <i class="bi bi-question-circle"></i> À vérifier
                                                </span>
                                            @endif
                                        </td>
                                        
                                        {{-- Poids --}}
                                        <td>{{ $lapereau->poids_naissance ?? '-' }} g</td>
                                        
                                        {{-- Santé --}}
                                        <td>
                                            <span class="badge" style="background: rgba(16, 185, 129, 0.1); color: #10B981;">
                                                {{ $lapereau->etat_sante ?? 'Bon' }}
                                            </span>
                                        </td>
                                        
                                        {{-- État --}}
                                        <td>
                                            @if ($lapereau->etat === 'vivant')
                                                <span class="badge" style="background: rgba(16, 185, 129, 0.1); color: #10B981;">Vivant</span>
                                            @elseif($lapereau->etat === 'vendu')
                                                <span class="badge" style="background: rgba(245, 158, 11, 0.1); color: #F59E0B;">Vendu</span>
                                            @else
                                                <span class="badge" style="background: rgba(239, 68, 68, 0.1); color: #EF4444;">Mort</span>
                                            @endif
                                        </td>
                                        
                                        {{-- ✅ VACCINATION - Compact --}}
                                        <td>
                                            @php
                                                $vaccinations = $lapereau->vaccinations ?? collect();
                                                $totalVaccins = $vaccinations->count();
                                                if ($totalVaccins === 0 && !empty($lapereau->vaccin_date)) {
                                                    $totalVaccins = 1;
                                                }
                                            @endphp

                                            @if ($totalVaccins > 0)
                                                <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                                                    <span class="badge" style="background: rgba(16, 185, 129, 0.1); color: #10B981; padding: 4px 10px; font-size: 11px;">
                                                        <i class="bi bi-shield-check"></i>
                                                        {{ $vaccinations->first()?->nom_lisible ?? $lapereau->vaccin_nom }}
                                                    </span>
                                                    @if ($totalVaccins > 1)
                                                        <small style="color: var(--text-tertiary); font-size: 10px;">+{{ $totalVaccins - 1 }}</small>
                                                    @endif
                                                </div>
                                                <small style="color: var(--text-tertiary); font-size: 10px; display: block; margin-top: 2px;">
                                                    📅 {{ ($vaccinations->first()?->date_administration ?? $lapereau->vaccin_date)?->format('d/m/Y') }}
                                                </small>
                                            @else
                                                <span class="badge" style="background: rgba(107, 114, 128, 0.1); color: #6B7D95; padding: 4px 10px; font-size: 11px;">
                                                    <i class="bi bi-x-circle"></i> Non vacciné
                                                </span>
                                            @endif
                                        </td>
                                        
                                        {{-- ✅ ACTIONS - Horizontaux --}}
                                        <td style="white-space: nowrap;">
                                            <div style="display: flex; gap: 6px; align-items: center;">
                                                <a href="{{ route('naissances.edit', $naissance->id) }}"
                                                   class="btn-cuni sm secondary" 
                                                   title="Modifier la naissance"
                                                   style="padding: 6px 10px; font-size: 11px; display: inline-flex; align-items: center; gap: 4px;">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="{{ route('lapins.show', $lapereau->id) }}"
                                                   class="btn-cuni sm primary" 
                                                   title="Voir détails"
                                                   style="padding: 6px 10px; font-size: 11px; display: inline-flex; align-items: center; gap: 4px;">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($lapereaux->hasPages())
                        <div style="margin-top: 20px;">
                            {{ $lapereaux->appends(request()->query())->links('pagination.bootstrap-5-sm') }}
                        </div>
                    @endif
                @else
                    <p class="text-muted text-center" style="padding: 40px;">
                        <i class="bi bi-inbox" style="font-size: 48px; opacity: 0.5;"></i><br>
                        Aucun lapereau enregistré pour cette naissance.
                    </p>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div>
        <!-- Stats -->
        <div class="cuni-card">
            <div class="card-header-custom">
                <h3 class="card-title"><i class="bi bi-graph-up"></i> Statistiques</h3>
            </div>
            <div class="card-body">
                <div class="space-y-4">
                    <div>
                        <label class="text-sm text-gray-500">Total Lapereaux</label>
                        <p class="font-semibold" style="font-size: 1.5rem;">{{ $naissance->total_lapereaux }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Vivants</label>
                        <p class="font-semibold" style="font-size: 1.2rem; color: var(--accent-green);">
                            {{ $naissance->nb_vivant }}
                        </p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Morts</label>
                        <p class="font-semibold" style="font-size: 1.2rem; color: var(--accent-red);">
                            {{ $naissance->nb_mort_ne }}
                        </p>
                    </div>
                    <hr style="border-color: var(--surface-border);">
                    <div>
                        <label class="text-sm text-gray-500">Taux de survie</label>
                        <p class="font-semibold" style="font-size: 1.2rem; color: var(--primary);">
                            {{ $naissance->taux_survie }}%
                        </p>
                    </div>
                    @php
                        $vaccines = $naissance->lapereaux->filter(fn($l) => $l->vaccinations->count() > 0 || !empty($l->vaccin_date))->count();
                    @endphp
                    @if ($vaccines > 0)
                        <hr style="border-color: var(--surface-border);">
                        <div>
                            <label class="text-sm text-gray-500">Vaccinés</label>
                            <p class="font-semibold" style="font-size: 1.2rem; color: var(--accent-green);">
                                {{ $vaccines }}/{{ $naissance->total_lapereaux }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Parent Info -->
        <div class="cuni-card" style="margin-top: 24px;">
            <div class="card-header-custom">
                <h3 class="card-title"><i class="bi bi-heart"></i> Parents</h3>
            </div>
            <div class="card-body">
                <div class="space-y-3">
                    <div>
                        <label class="text-sm text-gray-500">Mère</label>
                        <p class="font-semibold">{{ $naissance->femelle->nom ?? 'N/A' }}</p>
                    </div>
                    @if ($naissance->saillie)
                        <div>
                            <label class="text-sm text-gray-500">Père</label>
                            <p class="font-semibold">{{ $naissance->saillie->male->nom ?? 'N/A' }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ✅ CSS --}}
@push('styles')
<style>
    /* ✅ TABLEAU - Alignement & espacement */
    .lapins-table th,
    .lapins-table td {
        padding: 16px 20px !important;
        vertical-align: middle !important;
    }
    
    .lapins-table thead th {
        font-weight: 600;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        color: var(--text-secondary);
        border-bottom-width: 2px;
    }
    
    .lapins-table tbody tr {
        transition: background 0.2s ease;
    }
    
    .lapins-table tbody tr:hover {
        background: var(--surface-alt) !important;
    }
    
    /* ✅ BADGES - Compacts */
    .lapins-table .badge {
        padding: 4px 10px;
        font-size: 11px;
        line-height: 1.3;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    
    /* ✅ VACCINATION - Layout compact */
    .lapins-table td:nth-child(7) {
        min-width: 140px;
    }
    
    /* ✅ ACTIONS - Boutons horizontaux */
    .lapins-table td:nth-child(8) {
        min-width: 100px;
        white-space: nowrap;
    }
    
    .lapins-table td:nth-child(8) .btn-cuni.sm {
        padding: 6px 10px;
        font-size: 11px;
        min-width: auto;
        height: auto;
    }
    
    /* ✅ RESPONSIVE */
    @media (max-width: 1200px) {
        .lapins-table th,
        .lapins-table td {
            padding: 12px 14px !important;
            font-size: 12px;
        }
        .lapins-table .badge {
            padding: 3px 8px;
            font-size: 10px;
        }
    }
    
    @media (max-width: 768px) {
        .lapins-table {
            font-size: 11px;
        }
        .lapins-table th,
        .lapins-table td {
            padding: 10px 12px !important;
        }
    }
</style>
@endpush
@endsection