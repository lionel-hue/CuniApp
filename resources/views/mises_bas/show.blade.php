{{-- resources/views/mises_bas/show.blade.php --}}
@extends('layouts.cuniapp')
@section('title', 'Détails Mise Bas - CuniApp Élevage')

@push('styles')
    <style>
        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 24px;
            width: 100%;
            align-items: start;
        }

        .detail-main {
            min-width: 0;
        }

        .detail-sidebar {
            min-width: 0;
        }

        @media (max-width: 1024px) {
            .detail-grid {
                grid-template-columns: 1fr;
            }
        }

        .stat-box {
            text-align: center;
            padding: 20px 16px;
            background: var(--surface-alt);
            border-radius: var(--radius-lg);
            border: 1px solid var(--surface-border);
        }

        .stat-box .value {
            font-size: 28px;
            font-weight: 700;
            line-height: 1.2;
        }

        .stat-box .label {
            font-size: 12px;
            color: var(--text-tertiary);
            margin-top: 4px;
        }
    </style>
@endpush

@section('content')
    <div class="page-header">
        <div>
            <h2 class="page-title">
                <i class="bi bi-egg"></i> Détails de la Mise Bas
            </h2>
            <div class="breadcrumb">
                <a href="{{ route('dashboard') }}">Tableau de bord</a>
                <span>/</span>
                <a href="{{ route('mises-bas.index') }}">Mises Bas</a>
                <span>/</span>
                <span>#{{ $miseBas->id }}</span>
            </div>
        </div>
        <div style="display: flex; gap: 12px;">
            <a href="{{ route('mises-bas.edit', $miseBas) }}" class="btn-cuni primary">
                <i class="bi bi-pencil"></i> Modifier
            </a>
            <a href="{{ route('mises-bas.index') }}" class="btn-cuni secondary">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <div class="detail-grid">

        {{-- Left column --}}
        <div class="detail-main">

            {{-- Informations Principales --}}
            <div class="cuni-card">
                <div class="card-header-custom">
                    <h3 class="card-title"><i class="bi bi-info-circle"></i> Informations de la Mise Bas</h3>
                </div>
                <div class="card-body">
                    <div class="settings-grid">
                        <div class="form-group">
                            <label class="form-label">Femelle</label>
                            <p>
                                @if ($miseBas->femelle)
                                    <a href="{{ route('femelles.show', $miseBas->femelle->id) }}"
                                        style="color: var(--primary); text-decoration: none; font-weight: 600;">
                                        {{ $miseBas->femelle->nom }} ({{ $miseBas->femelle->code }})
                                    </a>
                                @else
                                    <span style="color: var(--text-tertiary);">Non définie</span>
                                @endif
                            </p>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Date de mise bas</label>
                            <p class="fw-semibold" style="font-size: 15px;">
                                {{ \Carbon\Carbon::parse($miseBas->date_mise_bas)->format('d/m/Y') }}
                            </p>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Jeunes vivants</label>
                            <p>
                                <span class="badge"
                                    style="background: rgba(16,185,129,0.1); color: var(--accent-green); font-size: 14px; padding: 5px 14px;">
                                    {{ $miseBas->nb_vivant }}
                                </span>
                            </p>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Morts-nés</label>
                            <p>
                                <span class="badge"
                                    style="background: rgba(239,68,68,0.1); color: var(--accent-red); font-size: 14px; padding: 5px 14px;">
                                    {{ $miseBas->nb_mort_ne ?? 0 }}
                                </span>
                            </p>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Total portée</label>
                            <p class="fw-semibold" style="font-size: 15px;">
                                {{ ($miseBas->nb_vivant ?? 0) + ($miseBas->nb_mort_ne ?? 0) }} lapereaux
                            </p>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Date de sevrage</label>
                            <p>
                                @if ($miseBas->date_sevrage)
                                    {{ \Carbon\Carbon::parse($miseBas->date_sevrage)->format('d/m/Y') }}
                                    @php
                                        $daysLeft = \Carbon\Carbon::parse($miseBas->date_sevrage)->diffInDays(
                                            now(),
                                            false,
                                        );
                                    @endphp
                                    @if ($daysLeft < 0)
                                        <span style="font-size: 11px; color: var(--accent-orange); margin-left: 8px;">
                                            dans {{ abs($daysLeft) }} jours
                                        </span>
                                    @elseif($daysLeft === 0)
                                        <span
                                            style="font-size: 11px; color: var(--accent-green); margin-left: 8px;">Aujourd'hui</span>
                                    @else
                                        <span style="font-size: 11px; color: var(--text-tertiary); margin-left: 8px;">
                                            il y a {{ $daysLeft }} jours
                                        </span>
                                    @endif
                                @else
                                    <span style="color: var(--text-tertiary);">Non planifié</span>
                                @endif
                            </p>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Poids moyen au sevrage</label>
                            <p>
                                @if ($miseBas->poids_moyen_sevrage)
                                    <strong>{{ number_format($miseBas->poids_moyen_sevrage, 2) }}</strong> kg / lapereau
                                @else
                                    <span style="color: var(--text-tertiary);">Non renseigné</span>
                                @endif
                            </p>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Enregistrée le</label>
                            <p style="color: var(--text-secondary); font-size: 13px;">
                                {{ $miseBas->created_at->format('d/m/Y à H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Statistiques de la portée --}}
            <div class="cuni-card" style="margin-top: 24px;">
                <div class="card-header-custom">
                    <h3 class="card-title"><i class="bi bi-bar-chart"></i> Statistiques de la portée</h3>
                </div>
                <div class="card-body">
                    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px;">
                        <div class="stat-box">
                            <div class="value" style="color: var(--accent-green);">{{ $miseBas->nb_vivant }}</div>
                            <div class="label">Vivants</div>
                        </div>
                        <div class="stat-box">
                            <div class="value" style="color: var(--accent-red);">{{ $miseBas->nb_mort_ne ?? 0 }}</div>
                            <div class="label">Morts-nés</div>
                        </div>
                        <div class="stat-box">
                            @php
                                $total = ($miseBas->nb_vivant ?? 0) + ($miseBas->nb_mort_ne ?? 0);
                                $survivalRate = $total > 0 ? round(($miseBas->nb_vivant / $total) * 100) : 0;
                            @endphp
                            <div class="value" style="color: var(--primary);">{{ $survivalRate }}%</div>
                            <div class="label">Taux survie</div>
                        </div>
                        <div class="stat-box">
                            <div class="value" style="color: var(--accent-orange);">
                                @php
                                    $ageDays = \Carbon\Carbon::parse($miseBas->date_mise_bas)->diffInDays(now());
                                    echo $ageDays > 60 ? '60+' : $ageDays;
                                @endphp
                            </div>
                            <div class="label">Jours d'âge</div>
                        </div>
                    </div>

                    {{-- Survie bar --}}
                    <div>
                        <div
                            style="display: flex; justify-content: space-between; font-size: 12px; color: var(--text-secondary); margin-bottom: 6px;">
                            <span>Taux de survie</span>
                            <span style="font-weight: 600;">{{ $survivalRate }}%</span>
                        </div>
                        <div style="height: 8px; background: var(--surface-border); border-radius: 4px; overflow: hidden;">
                            <div
                                style="height: 100%; width: {{ $survivalRate }}%; background: {{ $survivalRate >= 80 ? 'var(--accent-green)' : ($survivalRate >= 50 ? 'var(--accent-orange)' : 'var(--accent-red)') }}; border-radius: 4px; transition: width 1s ease;">
                            </div>
                        </div>
                    </div>

                    {{-- Naissances liées --}}
                    @if ($miseBas->naissances && $miseBas->naissances->count() > 0)
                        <div style="margin-top: 20px;">
                            <p
                                style="font-size: 13px; font-weight: 600; color: var(--text-secondary); margin-bottom: 12px;">
                                Naissances enregistrées ({{ $miseBas->naissances->count() }})
                            </p>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                @foreach ($miseBas->naissances as $naissance)
                                    <a href="{{ route('naissances.show', $naissance->id) }}"
                                        style="display: flex; align-items: center; justify-content: space-between; padding: 10px 14px; background: var(--surface-alt); border-radius: var(--radius); border: 1px solid var(--surface-border); text-decoration: none; color: inherit; transition: border-color 0.2s;"
                                        onmouseover="this.style.borderColor='var(--accent-green)'"
                                        onmouseout="this.style.borderColor='var(--surface-border)'">
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <i class="bi bi-egg-fill"
                                                style="color: var(--accent-green); font-size: 13px;"></i>
                                            <span
                                                style="font-size: 13px; font-weight: 500;">{{ $naissance->nb_vivant ?? 0 }}
                                                lapereaux vivants</span>
                                        </div>
                                        <span
                                            style="font-size: 12px; color: var(--text-tertiary);">{{ \Carbon\Carbon::parse($naissance->date_naissance ?? $naissance->created_at)->format('d/m/Y') }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        </div>{{-- /detail-main --}}

        {{-- Right sidebar --}}
        <div class="detail-sidebar">

            {{-- Résumé visuel --}}
            <div class="cuni-card" style="margin-bottom: 20px;">
                <div class="card-header-custom">
                    <h3 class="card-title"><i class="bi bi-egg"></i> Résumé portée</h3>
                </div>
                <div class="card-body" style="text-align: center; padding: 24px;">
                    <div
                        style="width: 72px; height: 72px; border-radius: 50%; background: rgba(245,158,11,0.1); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                        <i class="bi bi-egg" style="font-size: 32px; color: var(--accent-orange);"></i>
                    </div>
                    <div style="font-size: 36px; font-weight: 700; color: var(--accent-green);">{{ $miseBas->nb_vivant }}
                    </div>
                    <div style="font-size: 13px; color: var(--text-secondary); margin-bottom: 4px;">lapereaux vivants</div>
                    <div style="font-size: 12px; color: var(--text-tertiary);">
                        {{ \Carbon\Carbon::parse($miseBas->date_mise_bas)->format('d/m/Y') }}
                    </div>
                </div>
            </div>

            {{-- Actions rapides --}}
            <div class="cuni-card" style="margin-bottom: 20px;">
                <div class="card-header-custom">
                    <h3 class="card-title"><i class="bi bi-lightning"></i> Actions Rapides</h3>
                </div>
                <div class="card-body" style="display: flex; flex-direction: column; gap: 10px;">
                    <a href="{{ route('mises-bas.edit', $miseBas) }}" class="btn-cuni primary" style="width: 100%;">
                        <i class="bi bi-pencil"></i> Modifier la mise bas
                    </a>
                    @if ($miseBas->femelle)
                        <a href="{{ route('femelles.show', $miseBas->femelle->id) }}" class="btn-cuni secondary"
                            style="width: 100%;">
                            <i class="bi bi-arrow-down-right-square"></i> Voir la femelle
                        </a>
                        <a href="{{ route('saillies.create') }}?femelle_id={{ $miseBas->femelle->id }}"
                            class="btn-cuni secondary" style="width: 100%;">
                            <i class="bi bi-heart"></i> Nouvelle saillie
                        </a>
                    @endif
                    <a href="{{ route('naissances.index') }}" class="btn-cuni secondary" style="width: 100%;">
                        <i class="bi bi-egg-fill"></i> Voir les naissances
                    </a>
                </div>
            </div>

            {{-- Infos femelle liée --}}
            @if ($miseBas->femelle)
                <div class="cuni-card" style="margin-bottom: 20px;">
                    <div class="card-header-custom">
                        <h3 class="card-title"><i class="bi bi-arrow-down-right-square"></i> Femelle</h3>
                    </div>
                    <div class="card-body">
                        <div style="display: flex; flex-direction: column; gap: 10px; font-size: 13px;">
                            <div
                                style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid var(--surface-border);">
                                <span style="color: var(--text-secondary);">Nom</span>
                                <a href="{{ route('femelles.show', $miseBas->femelle->id) }}"
                                    style="font-weight: 600; color: var(--primary); text-decoration: none;">{{ $miseBas->femelle->nom }}</a>
                            </div>
                            <div
                                style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid var(--surface-border);">
                                <span style="color: var(--text-secondary);">Code</span>
                                <span
                                    style="font-weight: 600; font-family: 'JetBrains Mono', monospace;">{{ $miseBas->femelle->code }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding: 8px 0;">
                                <span style="color: var(--text-secondary);">État</span>
                                <span
                                    class="badge status-{{ strtolower($miseBas->femelle->etat) }}">{{ $miseBas->femelle->etat }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Métadonnées --}}
            <div class="cuni-card" style="margin-bottom: 20px;">
                <div class="card-header-custom">
                    <h3 class="card-title"><i class="bi bi-clock-history"></i> Métadonnées</h3>
                </div>
                <div class="card-body">
                    <div style="display: flex; flex-direction: column; gap: 12px; font-size: 13px;">
                        <div
                            style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid var(--surface-border);">
                            <span style="color: var(--text-secondary);">Créée le</span>
                            <span style="font-weight: 600;">{{ $miseBas->created_at->format('d/m/Y') }}</span>
                        </div>
                        <div
                            style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid var(--surface-border);">
                            <span style="color: var(--text-secondary);">Modifiée le</span>
                            <span style="font-weight: 600;">{{ $miseBas->updated_at->format('d/m/Y') }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0;">
                            <span style="color: var(--text-secondary);">À</span>
                            <span style="font-weight: 600;">{{ $miseBas->updated_at->format('H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Zone danger --}}
            <div class="cuni-card" style="border-left: 4px solid var(--accent-red);">
                <div class="card-body">
                    <p style="font-size: 13px; color: var(--text-secondary); margin-bottom: 12px;">
                        <i class="bi bi-exclamation-triangle" style="color: var(--accent-red);"></i>
                        <strong>Attention:</strong> La suppression supprime aussi toutes les naissances associées.
                    </p>
                    <form action="{{ route('mises-bas.destroy', $miseBas) }}" method="POST"
                        onsubmit="return confirm('Supprimer cette mise bas et ses naissances ? Action irréversible.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-cuni danger" style="width: 100%;">
                            <i class="bi bi-trash"></i> Supprimer cette mise bas
                        </button>
                    </form>
                </div>
            </div>

        </div>{{-- /detail-sidebar --}}
    </div>
@endsection
