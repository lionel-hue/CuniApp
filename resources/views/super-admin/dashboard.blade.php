{{-- resources/views/super-admin/dashboard.blade.php --}}
@extends('layouts.cuniapp')
@section('title', 'Super Admin - Tableau de Bord')
@section('content')
    <div class="page-header">
        <h2 class="page-title">
            <i class="bi bi-star-fill" style="color: var(--accent-orange);"></i>
            Administration Super Admin
        </h2>
    </div>

    {{-- Stats Grid - More Compact & Modern --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="cuni-card stats-card-compact">
            <div class="card-body p-3">
                <div class="flex items-center gap-3">
                    <div class="stats-icon-small" style="background: rgba(59, 130, 246, 0.1);">
                        <i class="bi bi-building text-blue-500"></i>
                    </div>
                    <div>
                        <p class="stats-label-small">Entreprises</p>
                        <p class="stats-value-small">{{ number_format($stats['total_firms']) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="cuni-card stats-card-compact">
            <div class="card-body p-3">
                <div class="flex items-center gap-3">
                    <div class="stats-icon-small" style="background: rgba(16, 185, 129, 0.1);">
                        <i class="bi bi-cash-stack text-green-500"></i>
                    </div>
                    <div>
                        <p class="stats-label-small">Revenus (Mois)</p>
                        <p class="stats-value-small text-green-600">{{ number_format($stats['total_revenue_month'], 0, ',', ' ') }} <small class="text-xs">FCFA</small></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="cuni-card stats-card-compact">
            <div class="card-body p-3">
                <div class="flex items-center gap-3">
                    <div class="stats-icon-small" style="background: rgba(139, 92, 246, 0.1);">
                        <i class="bi bi-check-circle text-purple-500"></i>
                    </div>
                    <div>
                        <p class="stats-label-small">Abonnements Actifs</p>
                        <p class="stats-value-small">{{ number_format($stats['active_subscriptions']) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="cuni-card stats-card-compact">
            <div class="card-body p-3">
                <div class="flex items-center gap-3">
                    <div class="stats-icon-small" style="background: rgba(245, 158, 11, 0.1);">
                        <i class="bi bi-clock text-amber-500"></i>
                    </div>
                    <div>
                        <p class="stats-label-small">Expire Bientôt (7j)</p>
                        <p class="stats-value-small text-amber-600">{{ $stats['expiring_soon'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .stats-card-compact {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border: 1px solid var(--surface-border);
        }
        .stats-card-compact:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
        .stats-icon-small {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }
        .stats-label-small {
            font-size: 0.75rem;
            color: var(--text-tertiary);
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.025em;
            font-weight: 600;
        }
        .stats-value-small {
            font-size: 1.15rem;
            font-weight: 700;
            margin: 0;
            color: var(--text-primary);
            line-height: 1.2;
        }
        .chart-placeholder {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 250px;
            background: var(--surface-alt);
            border-radius: var(--radius-lg);
            color: var(--text-tertiary);
            text-align: center;
            padding: 24px;
            border: 1px dashed var(--surface-border);
        }
        .chart-placeholder i {
            font-size: 2.5rem;
            margin-bottom: 12px;
            opacity: 0.2;
            color: var(--primary);
        }
    </style>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {{-- Signup Evolution Chart - Compact --}}
        <div class="lg:col-span-2">
            <div class="cuni-card h-full">
                <div class="card-header-custom flex items-center justify-between py-3">
                    <h3 class="card-title text-sm"><i class="bi bi-graph-up-arrow"></i> Évolution des Inscriptions (30 jours)</h3>
                    <span class="badge secondary sm">{{ array_sum($signupCounts) }} total</span>
                </div>
                <div class="card-body p-4">
                    <div style="height: 250px; position: relative;">
                        <canvas id="signupChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Active Users / Quick Stats --}}
        <div>
            <div class="cuni-card h-full">
                <div class="card-header-custom py-3">
                    <h3 class="card-title text-sm"><i class="bi bi-activity"></i> Activité Globale</h3>
                </div>
                <div class="card-body p-4">
                    <div class="flex flex-col gap-4">
                        <div class="flex items-center justify-between p-3 rounded-xl bg-blue-50 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-800/30">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-blue-500 text-white flex items-center justify-center">
                                    <i class="bi bi-people"></i>
                                </div>
                                <span class="text-sm font-medium">Actifs (24h)</span>
                            </div>
                            <span class="text-lg font-bold text-blue-600">{{ $activeUsers24h }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between p-3 rounded-xl bg-purple-50 dark:bg-purple-900/10 border border-purple-100 dark:border-purple-800/30">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-purple-500 text-white flex items-center justify-center">
                                    <i class="bi bi-person"></i>
                                </div>
                                <span class="text-sm font-medium">Total Comptes</span>
                            </div>
                            <span class="text-lg font-bold text-purple-600">{{ $stats['total_users'] }}</span>
                        </div>

                        <div class="flex items-center justify-between p-3 rounded-xl bg-green-50 dark:bg-green-900/10 border border-green-100 dark:border-green-800/30">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-green-500 text-white flex items-center justify-center">
                                    <i class="bi bi-check-all"></i>
                                </div>
                                <span class="text-sm font-medium">Entreprises Actives</span>
                            </div>
                            <span class="text-lg font-bold text-green-600">{{ $stats['active_firms'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Top Firms Leaderboard --}}
    <div class="cuni-card mb-6">
        <div class="card-header-custom">
            <h3 class="card-title">
                <i class="bi bi-trophy"></i> Top 5 Entreprises (Par Revenus)
            </h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Rang</th>
                            <th>Entreprise</th>
                            <th>Administrateur</th>
                            <th>Abonnement</th>
                            <th>Revenus</th>
                            <th>Ventes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($topFirms as $index => $firm)
                            <tr>
                                <td>
                                    @if ($index === 0)
                                        <span class="badge" style="background: #FFD700; color: #000;">🥇 1</span>
                                    @elseif($index === 1)
                                        <span class="badge" style="background: #C0C0C0; color: #000;">🥈 2</span>
                                    @elseif($index === 2)
                                        <span class="badge" style="background: #CD7F32; color: #000;">🥉 3</span>
                                    @else
                                        <span class="badge">{{ $index + 1 }}</span>
                                    @endif
                                </td>
                                <td class="fw-semibold">{{ $firm->name }}</td>
                                <td>{{ $firm->owner->name ?? 'N/A' }}</td>
                                <td>
                                    @if ($firm->activeSubscription)
                                        <span class="badge" style="background: rgba(16, 185, 129, 0.1); color: #10B981;">
                                            {{ $firm->activeSubscription->plan->name ?? 'N/A' }}
                                        </span>
                                    @else
                                        <span class="badge" style="background: rgba(107, 114, 128, 0.1); color: #6B7280;">
                                            Aucun
                                        </span>
                                    @endif
                                </td>
                                <td class="fw-bold" style="color: var(--primary);">
                                    {{ number_format($firm->total_revenue ?? 0, 0, ',', ' ') }} FCFA
                                </td>
                                <td>{{ $firm->total_sales ?? 0 }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Recent Signups --}}
    <div class="cuni-card">
        <div class="card-header-custom">
            <h3 class="card-title">
                <i class="bi bi-person-plus"></i> Inscriptions Récentes (7 jours)
            </h3>
        </div>
        <div class="card-body">
            @if ($recentSignups->count() > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Entreprise</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentSignups as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->firm->name ?? 'N/A' }}</td>
                                    <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('admin.subscriptions.show', $user->id) }}"
                                            class="btn-cuni sm secondary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-center text-gray-500 py-8">Aucune inscription récente</p>
            @endif
        </div>
    </div>
    @push('scripts')
        <script>
            // Cleanup: Ensure Chart is initialized correctly after script loading fix in layout
            document.addEventListener('DOMContentLoaded', function() {
                const ctxSignup = document.getElementById('signupChart');
                if (ctxSignup) {
                    const signupCounts = @json($signupCounts);
                    const isSignupEmpty = signupCounts.length === 0 || signupCounts.every(val => val === 0);

                    if (isSignupEmpty) {
                        const container = ctxSignup.parentElement;
                        ctxSignup.style.display = 'none';
                        const placeholder = document.createElement('div');
                        placeholder.className = 'chart-placeholder';
                        placeholder.innerHTML = `
                        <i class="bi bi-graph-up-arrow"></i>
                        <p>Aucune inscription récente à afficher sur les 30 derniers jours.</p>
                    `;
                        container.appendChild(placeholder);
                    } else {
                        new Chart(ctxSignup.getContext('2d'), {
                            type: 'line',
                            data: {
                                labels: @json($signupLabels),
                                datasets: [{
                                    label: 'Nouvelles entreprises',
                                    data: signupCounts,
                                    borderColor: '#3B82F6',
                                    backgroundColor: 'rgba(59, 130, 246, 0.05)',
                                    borderWidth: 3,
                                    tension: 0.4,
                                    fill: true,
                                    pointRadius: 2,
                                    pointHoverRadius: 6,
                                    pointBackgroundColor: '#3B82F6'
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: false
                                    },
                                    tooltip: {
                                        backgroundColor: '#1E293B',
                                        padding: 12,
                                        titleFont: { size: 14, weight: 'bold' },
                                        bodyFont: { size: 13 },
                                        cornerRadius: 8,
                                        displayColors: false
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        grid: {
                                            display: true,
                                            color: 'rgba(0,0,0,0.03)'
                                        },
                                        ticks: {
                                            stepSize: 1,
                                            font: { size: 11 }
                                        }
                                    },
                                    x: {
                                        grid: { display: false },
                                        ticks: {
                                            maxRotation: 0,
                                            autoSkip: true,
                                            maxTicksLimit: 10,
                                            font: { size: 11 }
                                        }
                                    }
                                }
                            }
                        });
                    }
                }
            });
        </script>
    @endpush
@endsection
