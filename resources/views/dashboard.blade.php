@extends('layouts.app')

@section('title', 'Tableau de Bord Élevage')

@section('content')
<div class="dashboard-container">
    <!-- Header Section -->
    <header class="dashboard-header">
        <div class="header-content">
            <div class="brand-section">
                <div class="logo">A</div>
                <div class="header-text">
                    <h1>Tableau de Bord Élevage</h1>
                    <p class="header-subtitle">Gestion professionnelle de votre cheptel</p>
                </div>
            </div>

            <div class="header-actions">
                <button class="btn btn-outline">
                    <i class="bi bi-download"></i>
                    Export
                </button>
                <a href="{{ route('lapin.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i>
                    Nouvelle entrée
                </a>
            </div>
        </div>

        <div class="stats-overview">
            @php
                $statsData = [
                    ['icon' => 'bi-diagram-3', 'value' => $nbMales + $nbFemelles, 'label' => 'Total Lapins', 'type' => 'total', 'change' => '+8.2%', 'trend' => 'positive'],
                    ['icon' => 'bi-gender-male', 'value' => $nbMales, 'label' => 'Mâles', 'type' => 'male', 'change' => '+5.1%', 'trend' => 'positive'],
                    ['icon' => 'bi-gender-female', 'value' => $nbFemelles, 'label' => 'Femelles', 'type' => 'female', 'change' => '+12%', 'trend' => 'positive'],
                    ['icon' => 'bi-heart-pulse', 'value' => $nbSaillies, 'label' => 'Saillies', 'type' => 'breeding', 'change' => '-3.1%', 'trend' => 'negative'],
                    ['icon' => 'bi-stars', 'value' => $nbMisesBas, 'label' => 'Portées', 'type' => 'births', 'change' => '+15%', 'trend' => 'positive'],
                    ['icon' => 'bi-bell', 'value' => 3, 'label' => 'Alertes', 'type' => 'alerts', 'change' => '0%', 'trend' => 'neutral']
                ];
            @endphp

            @foreach($statsData as $stat)
            <div class="stat-item {{ $stat['type'] }}">
                <div class="stat-content">
                    <div class="stat-icon {{ $stat['type'] }}">
                        <i class="bi {{ $stat['icon'] }}"></i>
                    </div>
                    <div>
                        <div class="stat-value">{{ $stat['value'] }}</div>
                        <div class="stat-label">{{ $stat['label'] }}</div>
                        <div class="stat-change {{ $stat['trend'] }}">
                            <i class="bi bi-arrow-{{ $stat['trend'] === 'positive' ? 'up' : ($stat['trend'] === 'negative' ? 'down' : 'right') }}-short"></i>
                            {{ $stat['change'] }}
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </header>

    <!-- Main Content -->
    <main class="dashboard-content">
        <!-- Left Column -->
        <div class="main-column">
            <!-- Performance Cards -->
            <div class="performance-cards">
                @php
                    $performanceData = [
                        ['type' => 'male', 'icon' => 'bi-gender-male', 'value' => $nbMales, 'title' => 'Mâles Reproducteurs', 'progress' => 75, 'trend' => '+12%', 'trendType' => 'positive'],
                        ['type' => 'female', 'icon' => 'bi-gender-female', 'value' => $nbFemelles, 'title' => 'Femelles Reproductrices', 'progress' => 85, 'trend' => '+8%', 'trendType' => 'positive'],
                        ['type' => 'breeding', 'icon' => 'bi-heart-pulse', 'value' => $nbSaillies, 'title' => 'Saillies en Cours', 'progress' => 60, 'trend' => '-3%', 'trendType' => 'negative'],
                        ['type' => 'births', 'icon' => 'bi-stars', 'value' => $nbMisesBas, 'title' => 'Mises Bas Récentes', 'progress' => 90, 'trend' => '+15%', 'trendType' => 'positive']
                    ];
                @endphp

                @foreach($performanceData as $card)
                <div class="performance-card">
                    <div class="card-header">
                        <div class="card-title">{{ $card['title'] }}</div>
                        <div class="card-icon {{ $card['type'] }}">
                            <i class="bi {{ $card['icon'] }}"></i>
                        </div>
                    </div>
                    <div class="card-value">{{ $card['value'] }}</div>
                    <div class="card-progress">
                        <div class="progress-info">
                            <span>Progression</span>
                            <span>{{ $card['progress'] }}%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill {{ $card['type'] }}" style="width: {{ $card['progress'] }}%"></div>
                        </div>
                    </div>
                    <div class="card-trend {{ $card['trendType'] }}">
                        <i class="bi bi-arrow-{{ $card['trendType'] === 'positive' ? 'up' : 'down' }}-short"></i>
                        {{ $card['trend'] }} ce mois
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions-section">
                <div class="section-header">
                    <h3>Actions Rapides</h3>
                    <button class="btn btn-outline">Personnaliser</button>
                </div>
                <div class="quick-actions-grid">
                   @foreach([
                        ['url' => '/males', 'icon' => 'bi-gender-male', 'title' => 'Gérer les Mâles', 'desc' => 'Ajouter, modifier ou consulter', 'type' => 'male'],
                        ['url' => '/femelles', 'icon' => 'bi-gender-female', 'title' => 'Gérer les Femelles', 'desc' => 'Suivi reproduction', 'type' => 'female'],
                        ['url' => '/saillies', 'icon' => 'bi-heart-pulse', 'title' => 'Planifier Saillie', 'desc' => 'Nouveau croisement', 'type' => 'breeding'],
                        ['url' => route('naissances.index'), 'icon' => 'bi-calendar-plus', 'title' => 'Enregistrer Naissance', 'desc' => 'Nouvelle portée', 'type' => 'births']
                        ] as $action)
                        <a href="{{ $action['url'] }}" class="action-card">
                           <div class="action-icon">
                               <i class="bi {{ $action['icon'] }}"></i>
                            </div>
                            <h4>{{ $action['title'] }}</h4>
                            <p>{{ $action['desc'] }}</p>
                        </a>
                    @endforeach

                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="sidebar-column">
            <!-- Calendar Widget -->
            <div class="widget calendar-widget">
                <div class="widget-header">
                    <h3>Calendrier</h3>
                    <div class="calendar-nav">
                        <button class="nav-btn" id="prevMonth">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <span class="current-month" id="currentMonth">Août 2025</span>
                        <button class="nav-btn" id="nextMonth">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                </div>
                <div class="calendar-grid" id="calendarGrid">
                    <!-- Calendar will be generated by JavaScript -->
                </div>
                <div class="calendar-legend">
                    <div class="legend-item">
                        <div class="legend-color breeding"></div>
                        <span>Saillies prévues</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color births"></div>
                        <span>Naissances attendues</span>
                    </div>
                </div>
            </div>

            <!-- Activity Feed -->
            <div class="widget activity-widget">
                <div class="widget-header">
                    <h3>Activité Récente</h3>
                    <button class="btn btn-outline">Voir tout</button>
                </div>
                <div class="activity-feed">
                    @foreach([
                        ['type' => 'success', 'icon' => 'bi-check-circle-fill', 'title' => 'Mise bas enregistrée', 'desc' => 'Femelle #245 - 6 lapereaux', 'time' => 'Il y a 2 heures'],
                        ['type' => 'breeding', 'icon' => 'bi-heart-fill', 'title' => 'Saillie programmée', 'desc' => 'F#245 × M#112 - Prévue demain', 'time' => 'Hier, 15:30'],
                        ['type' => 'warning', 'icon' => 'bi-exclamation-triangle-fill', 'title' => 'Vaccination requise', 'desc' => '3 lapins nécessitent une vaccination', 'time' => '23 août 2025'],
                        ['type' => 'info', 'icon' => 'bi-info-circle-fill', 'title' => 'Rapport généré', 'desc' => 'Rapport mensuel disponible', 'time' => '20 août 2025']
                    ] as $activity)
                    <div class="activity-item">
                        <div class="activity-icon {{ $activity['type'] }}">
                            <i class="bi {{ $activity['icon'] }}"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">{{ $activity['title'] }}</div>
                            <div class="activity-description">{{ $activity['desc'] }}</div>
                            <div class="activity-time">
                                <i class="bi bi-clock"></i>
                                {{ $activity['time'] }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Alerts Widget -->
            <div class="widget alerts-widget">
                <div class="widget-header">
                    <h3>Alertes & Notifications</h3>
                    <span class="alert-count">3</span>
                </div>
                <div class="alerts-list">
                    @foreach([
                        ['level' => 'high', 'icon' => 'bi-exclamation-triangle-fill', 'title' => 'Vaccination urgente', 'time' => 'Dans 2 jours'],
                        ['level' => 'medium', 'icon' => 'bi-calendar-event', 'title' => 'Saillie à confirmer', 'time' => 'Demain'],
                        ['level' => 'low', 'icon' => 'bi-clipboard-data', 'title' => 'Rapport mensuel', 'time' => 'Fin de semaine']
                    ] as $alert)
                    <div class="alert-item {{ $alert['level'] }}">
                        <div class="alert-icon">
                            <i class="bi {{ $alert['icon'] }}"></i>
                        </div>
                        <div class="alert-content">
                            <div class="alert-title">{{ $alert['title'] }}</div>
                            <div class="alert-time">{{ $alert['time'] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </main>
</div>

<style>
.dashboard-container {
    max-width: 1600px;
    margin: 0 auto;
    padding: 24px;
}

/* Header Styles */
.dashboard-header {
    background: white;
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-md);
    margin-bottom: 32px;
    overflow: hidden;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 24px 32px;
    border-bottom: 1px solid var(--gray-200);
}

.brand-section {
    display: flex;
    align-items: center;
    gap: 16px;
}

.logo {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    border-radius: var(--border-radius);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
    font-weight: bold;
}

.header-text h1 {
    font-size: 24px;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: 4px;
}

.header-subtitle {
    font-size: 14px;
    color: var(--gray-500);
}

.header-actions {
    display: flex;
    gap: 12px;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    font-weight: 600;
    font-size: 14px;
    border-radius: var(--border-radius);
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: var(--transition);
}

.btn-primary {
    background: var(--primary);
    color: white;
}

.btn-primary:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.btn-outline {
    background: white;
    color: var(--gray-700);
    border: 1px solid var(--gray-300);
}

.btn-outline:hover {
    border-color: var(--primary);
    color: var(--primary);
}

/* Stats Overview */
.stats-overview {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    padding: 24px 32px;
}

.stat-item {
    background: white;
    padding: 20px;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-sm);
    border-left: 4px solid var(--primary);
    transition: var(--transition);
}

.stat-item:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-md);
}

.stat-item.male { border-left-color: #3b82f6; }
.stat-item.female { border-left-color: #ec4899; }
.stat-item.breeding { border-left-color: var(--accent); }
.stat-item.births { border-left-color: var(--success); }
.stat-item.alerts { border-left-color: var(--danger); }

.stat-content {
    display: flex;
    align-items: center;
    gap: 16px;
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: white;
}

.stat-icon.total { background: var(--primary); }
.stat-icon.male { background: #3b82f6; }
.stat-icon.female { background: #ec4899; }
.stat-icon.breeding { background: var(--accent); }
.stat-icon.births { background: var(--success); }
.stat-icon.alerts { background: var(--danger); }

.stat-value {
    font-size: 28px;
    font-weight: 800;
    color: var(--gray-900);
    line-height: 1;
    margin-bottom: 4px;
}

.stat-label {
    font-size: 14px;
    color: var(--gray-600);
    font-weight: 500;
}

.stat-change {
    margin-top: 8px;
    font-size: 12px;
    display: flex;
    align-items: center;
    gap: 4px;
}

.positive { color: var(--success); }
.negative { color: var(--danger); }
.neutral { color: var(--gray-500); }

/* Main Content Layout */
.dashboard-content {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 24px;
}

/* Performance Cards */
.performance-cards {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    margin-bottom: 24px;
}

.performance-card {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-sm);
    padding: 20px;
    transition: var(--transition);
    position: relative;
    overflow: hidden;
}

.performance-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-md);
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}

.card-title {
    font-size: 16px;
    font-weight: 600;
    color: var(--gray-700);
}

.card-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    color: white;
}

.card-icon.male { background: #3b82f6; }
.card-icon.female { background: #ec4899; }
.card-icon.breeding { background: var(--accent); }
.card-icon.births { background: var(--success); }

.card-value {
    font-size: 32px;
    font-weight: 800;
    color: var(--gray-900);
    margin-bottom: 8px;
}

.card-progress {
    margin-bottom: 16px;
}

.progress-info {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    font-size: 14px;
    color: var(--gray-600);
}

.progress-bar {
    height: 8px;
    background: var(--gray-200);
    border-radius: 4px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    border-radius: 4px;
    transition: width 1s ease;
}

.progress-fill.male { background: #3b82f6; }
.progress-fill.female { background: #ec4899; }
.progress-fill.breeding { background: var(--accent); }
.progress-fill.births { background: var(--success); }

.card-trend {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 14px;
    font-weight: 500;
}

/* Quick Actions */
.quick-actions-section {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-sm);
    padding: 24px;
    margin-bottom: 24px;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.section-header h3 {
    font-size: 18px;
    font-weight: 700;
    color: var(--gray-900);
}

.quick-actions-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
}

.action-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 20px;
    background: var(--gray-50);
    border-radius: var(--border-radius);
    text-decoration: none;
    color: inherit;
    transition: var(--transition);
    text-align: center;
}

.action-card:hover {
    background: var(--primary);
    color: white;
    transform: translateY(-4px);
    box-shadow: var(--shadow-md);
}

.action-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    margin-bottom: 12px;
    background: white;
    color: var(--primary);
}

.action-card:hover .action-icon {
    background: rgba(255, 255, 255, 0.2);
    color: white;
}

.action-card h4 {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 4px;
}

.action-card p {
    font-size: 14px;
    color: var(--gray-600);
}

.action-card:hover p {
    color: rgba(255, 255, 255, 0.9);
}

/* Sidebar Widgets */
.widget {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-sm);
    padding: 24px;
    margin-bottom: 24px;
}

.widget-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 16px;
    border-bottom: 1px solid var(--gray-200);
}

.widget-header h3 {
    font-size: 18px;
    font-weight: 700;
    color: var(--gray-900);
}

/* Calendar Widget */
.calendar-nav {
    display: flex;
    align-items: center;
    gap: 12px;
}

.nav-btn {
    width: 32px;
    height: 32px;
    border: none;
    background: var(--gray-100);
    color: var(--gray-600);
    border-radius: var(--border-radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: var(--transition);
}

.nav-btn:hover {
    background: var(--primary);
    color: white;
}

.current-month {
    font-weight: 600;
    color: var(--gray-800);
    min-width: 120px;
    text-align: center;
}

.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 4px;
    margin-bottom: 16px;
}

.calendar-day {
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    color: var(--gray-600);
    border-radius: var(--border-radius-sm);
    position: relative;
    cursor: pointer;
    transition: var(--transition);
}

.calendar-day.header {
    font-weight: 600;
    color: var(--gray-800);
    background: var(--gray-100);
    cursor: default;
}

.calendar-day:hover:not(.header) {
    background: var(--gray-100);
}

.calendar-day.today {
    background: var(--primary);
    color: white;
    font-weight: 600;
}

.calendar-day.event {
    font-weight: 600;
}

.calendar-day.event::after {
    content: '';
    position: absolute;
    bottom: 4px;
    left: 50%;
    transform: translateX(-50%);
    width: 4px;
    height: 4px;
    border-radius: 50%;
    background: var(--primary);
}

.calendar-day.event.breeding::after {
    background: var(--accent);
}

.calendar-day.event.births::after {
    background: var(--success);
}

.calendar-legend {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    color: var(--gray-600);
}

.legend-color {
    width: 12px;
    height: 12px;
    border-radius: 2px;
}

.legend-color.breeding {
    background: var(--accent);
}

.legend-color.births {
    background: var(--success);
}

/* Activity Feed */
.activity-feed {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.activity-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 12px;
    background: var(--gray-50);
    border-radius: var(--border-radius);
    transition: var(--transition);
}

.activity-item:hover {
    background: var(--gray-100);
}

.activity-icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    color: white;
    flex-shrink: 0;
}

.activity-icon.success {
    background: var(--success);
}

.activity-icon.breeding {
    background: var(--accent);
}

.activity-icon.warning {
    background: var(--warning);
}

.activity-icon.info {
    background: var(--info);
}

.activity-content {
    flex: 1;
}

.activity-title {
    font-size: 14px;
    font-weight: 600;
    color: var(--gray-900);
    margin-bottom: 2px;
}

.activity-description {
    font-size: 13px;
    color: var(--gray-600);
    margin-bottom: 4px;
}

.activity-time {
    font-size: 12px;
    color: var(--gray-500);
    display: flex;
    align-items: center;
    gap: 4px;
}

/* Alerts Widget */
.alert-count {
    background: var(--danger);
    color: white;
    font-size: 12px;
    font-weight: 600;
    padding: 4px 8px;
    border-radius: 12px;
    min-width: 24px;
    text-align: center;
}

.alerts-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.alert-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    border-radius: var(--border-radius);
    background: var(--gray-50);
    transition: var(--transition);
}

.alert-item:hover {
    background: var(--gray-100);
}

.alert-item.high {
    border-left: 4px solid var(--danger);
}

.alert-item.medium {
    border-left: 4px solid var(--warning);
}

.alert-item.low {
    border-left: 4px solid var(--info);
}

.alert-icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    flex-shrink: 0;
    color: white;
}

.alert-item.high .alert-icon {
    background: var(--danger);
}

.alert-item.medium .alert-icon {
    background: var(--warning);
}

.alert-item.low .alert-icon {
    background: var(--info);
}

.alert-content {
    flex: 1;
}

.alert-title {
    font-size: 14px;
    font-weight: 600;
    color: var(--gray-900);
    margin-bottom: 2px;
}

.alert-time {
    font-size: 12px;
    color: var(--gray-500);
}

/* Responsive Design */
@media (max-width: 1200px) {
    .dashboard-content {
        grid-template-columns: 1fr;
    }
    
    .performance-cards {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .dashboard-container {
        padding: 16px;
    }
    
    .header-content {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
        padding: 20px;
    }
    
    .header-actions {
        width: 100%;
        justify-content: space-between;
    }
    
    .stats-overview {
        grid-template-columns: 1fr;
        padding: 20px;
    }
    
    .quick-actions-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    .header-text h1 {
        font-size: 20px;
    }
    
    .stat-value {
        font-size: 24px;
    }
    
    .card-value {
        font-size: 28px;
    }
    
    .btn {
        padding: 8px 16px;
        font-size: 13px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Calendar functionality
    const calendarGrid = document.getElementById('calendarGrid');
    const currentMonthSpan = document.getElementById('currentMonth');
    const prevMonthBtn = document.getElementById('prevMonth');
    const nextMonthBtn = document.getElementById('nextMonth');
    
    let currentDate = new Date();
    const months = [
        'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
        'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
    ];
    const weekdays = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
    
    function renderCalendar(date) {
        calendarGrid.innerHTML = '';
        
        // Add weekday headers
        weekdays.forEach(day => {
            const dayElement = document.createElement('div');
            dayElement.className = 'calendar-day header';
            dayElement.textContent = day;
            calendarGrid.appendChild(dayElement);
        });
        
        const year = date.getFullYear();
        const month = date.getMonth();
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const today = new Date();
        
        // Adjust for Monday as first day of week
        const startDay = firstDay === 0 ? 6 : firstDay - 1;
        
        // Set month and year title
        currentMonthSpan.textContent = `${months[month]} ${year}`;

        // Add empty cells for days before first day of month
        for (let i = 0; i < startDay; i++) {
            const emptyDay = document.createElement('div');
            calendarGrid.appendChild(emptyDay);
        }

        // Add days of month
        for (let day = 1; day <= daysInMonth; day++) {
            const dayElement = document.createElement('div');
            dayElement.className = 'calendar-day';
            dayElement.textContent = day;

            // Highlight today
            if (day === today.getDate() && month === today.getMonth() && year === today.getFullYear()) {
                dayElement.classList.add('today');
            }

            // Add event indicators
            if ([5, 12, 18].includes(day)) {
                dayElement.classList.add('event', 'breeding');
            }
            if ([8, 15, 22].includes(day)) {
                dayElement.classList.add('event', 'births');
            }

            calendarGrid.appendChild(dayElement);
        }
    }

    // Event listeners for month navigation
    prevMonthBtn.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar(currentDate);
    });

    nextMonthBtn.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar(currentDate);
    });

    // Initial render
    renderCalendar(currentDate);

    // Animate progress bars on load
    setTimeout(() => {
        document.querySelectorAll('.progress-fill').forEach(bar => {
            const width = bar.style.width;
            bar.style.width = '0%';
            setTimeout(() => {
                bar.style.width = width;
            }, 100);
        });
    }, 500);

    // Add hover animations to cards
    document.querySelectorAll('.stat-item, .performance-card, .action-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-4px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});
</script>
@endsection