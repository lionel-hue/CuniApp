@extends('layouts.cuniapp')
@section('title', 'Historique des Activités - CuniApp Élevage')

@section('content')
    <div class="page-header">
        <div>
            <h2 class="page-title">
                <i class="bi bi-clock-history"></i> Historique des Activités
            </h2>
            <div class="breadcrumb">
                <a href="{{ route('dashboard') }}">Tableau de bord</a>
                <span>/</span>
                <span>Activités</span>
            </div>
        </div>
        <a href="{{ route('dashboard') }}" class="btn-cuni secondary">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
    </div>

    {{-- Stats rapides --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="cuni-card">
            <div class="card-body p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total activités</p>
                        <p class="text-2xl font-bold mt-1">{{ $total }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-full flex items-center justify-center"
                        style="background: rgba(59, 130, 246, 0.1)">
                        <i class="bi bi-list-ul text-blue-500 text-lg"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="cuni-card">
            <div class="card-body p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Naissances</p>
                        <p class="text-2xl font-bold mt-1 text-green-600">
                            {{ $activities->where('title', 'Naissance enregistrée')->count() }}
                        </p>
                    </div>
                    <div class="w-10 h-10 rounded-full flex items-center justify-center"
                        style="background: rgba(16, 185, 129, 0.1)">
                        <i class="bi bi-egg-fill text-green-500 text-lg"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="cuni-card">
            <div class="card-body p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Saillies</p>
                        <p class="text-2xl font-bold mt-1 text-purple-600">
                            {{ $activities->where('title', 'Saillie programmée')->count() }}
                        </p>
                    </div>
                    <div class="w-10 h-10 rounded-full flex items-center justify-center"
                        style="background: rgba(139, 92, 246, 0.1)">
                        <i class="bi bi-heart text-purple-500 text-lg"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="cuni-card">
            <div class="card-body p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Ventes</p>
                        <p class="text-2xl font-bold mt-1 text-blue-600">
                            {{ $activities->where('title', 'Vente enregistrée')->count() }}
                        </p>
                    </div>
                    <div class="w-10 h-10 rounded-full flex items-center justify-center"
                        style="background: rgba(59, 130, 246, 0.1)">
                        <i class="bi bi-cart text-blue-500 text-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtres par type --}}
    <div class="cuni-card mb-6">
        <div class="card-body">
            <div class="flex gap-2 flex-wrap">
                <a href="?type=" class="btn-cuni sm {{ !request('type') ? 'primary' : 'secondary' }}">Tous</a>
                <a href="?type=green" class="btn-cuni sm {{ request('type') === 'green' ? 'primary' : 'secondary' }}">
                    <i class="bi bi-egg-fill"></i> Mises bas
                </a>
                <a href="?type=purple" class="btn-cuni sm {{ request('type') === 'purple' ? 'primary' : 'secondary' }}">
                    <i class="bi bi-heart"></i> Saillies
                </a>
                <a href="?type=blue" class="btn-cuni sm {{ request('type') === 'blue' ? 'primary' : 'secondary' }}">
                    <i class="bi bi-cart"></i> Ventes
                </a>
                <a href="?type=orange" class="btn-cuni sm {{ request('type') === 'orange' ? 'primary' : 'secondary' }}">
                    <i class="bi bi-exclamation-triangle"></i> Alertes
                </a>
            </div>
        </div>
    </div>

    {{-- Liste des activités --}}
    <div class="cuni-card">
        <div class="card-header-custom">
            <h3 class="card-title">
                <i class="bi bi-list-check"></i> Toutes les activités
            </h3>
            <span class="text-sm text-gray-500">
                Page {{ $currentPage }} sur {{ $lastPage }}
            </span>
        </div>
        <div class="card-body">
            <div class="timeline" style="max-height: none;">
                @forelse($activities as $activity)
                    <a href="{{ $activity['url'] }}" class="timeline-item" style="text-decoration: none; color: inherit;">
                        <div class="timeline-dot {{ $activity['type'] }}"></div>
                        <div class="timeline-content" style="flex: 1;">
                            <div class="timeline-title">
                                @if (isset($activity['icon']))
                                    <i class="bi {{ $activity['icon'] }}" style="margin-right: 6px;"></i>
                                @endif
                                {{ $activity['title'] }}
                            </div>
                            <div class="timeline-desc">{{ $activity['desc'] }}</div>
                            <div class="timeline-time">
                                <i class="bi bi-clock"></i> {{ $activity['time'] }}
                                @if ($activity['date'])
                                    <span class="ml-2 text-gray-400">
                                        ({{ \Carbon\Carbon::parse($activity['date'])->format('d/m/Y H:i') }})
                                    </span>
                                @endif
                            </div>
                        </div>
                        <i class="bi bi-chevron-right text-gray-400"></i>
                    </a>
                @empty
                    <div class="text-center py-12 text-gray-500">
                        <i class="bi bi-inbox text-4xl mb-4 opacity-40"></i>
                        <p class="text-lg">Aucune activité enregistrée</p>
                        <a href="{{ route('saillies.create') }}" class="btn-cuni primary mt-4">
                            <i class="bi bi-plus-lg"></i> Enregistrer une première activité
                        </a>
                    </div>
                @endforelse
            </div>

            {{-- Pagination simple --}}
            @if ($lastPage > 1)
                <div class="flex justify-center mt-6 gap-2">
                    @if ($currentPage > 1)
                        <a href="?page={{ $currentPage - 1 }}{{ request('type') ? '&type=' . request('type') : '' }}"
                            class="btn-cuni secondary sm">
                            <i class="bi bi-chevron-left"></i> Précédent
                        </a>
                    @endif

                    @for ($i = 1; $i <= $lastPage; $i++)
                        <a href="?page={{ $i }}{{ request('type') ? '&type=' . request('type') : '' }}"
                            class="btn-cuni sm {{ $i == $currentPage ? 'primary' : 'secondary' }}"
                            style="min-width: 40px; justify-content: center;">
                            {{ $i }}
                        </a>
                    @endfor

                    @if ($currentPage < $lastPage)
                        <a href="?page={{ $currentPage + 1 }}{{ request('type') ? '&type=' . request('type') : '' }}"
                            class="btn-cuni secondary sm">
                            Suivant <i class="bi bi-chevron-right"></i>
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <style>
        .timeline {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .timeline-item {
            display: flex;
            gap: 12px;
            position: relative;
            padding: 12px;
            border-radius: var(--radius);
            transition: background 0.2s;
        }

        .timeline-item:hover {
            background: var(--surface-alt);
        }

        .timeline-item:not(:last-child)::before {
            content: '';
            position: absolute;
            left: 17px;
            top: 32px;
            width: 1px;
            height: calc(100% + 4px);
            background: var(--surface-border);
        }

        .timeline-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            flex-shrink: 0;
            margin-top: 8px;
        }

        .timeline-dot.green {
            background: var(--accent-green);
        }

        .timeline-dot.purple {
            background: var(--accent-purple);
        }

        .timeline-dot.orange {
            background: var(--accent-orange);
        }

        .timeline-dot.blue {
            background: #3B82F6;
        }

        .timeline-title {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 4px;
            color: var(--text-primary);
        }

        .timeline-desc {
            font-size: 13px;
            color: var(--text-secondary);
            margin-bottom: 4px;
        }

        .timeline-time {
            font-size: 12px;
            color: var(--text-tertiary);
            display: flex;
            align-items: center;
            gap: 4px;
        }
    </style>
@endsection
