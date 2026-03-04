<?php

namespace App\Http\Controllers;

use App\Models\Naissance;  
use App\Models\Saillie;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ActiviteController extends Controller
{
    public function index()
    {
        // Récupérer TOUTES les activités fusionnées
        
        // ✅ 1. Naissances (vert) → remplace MiseBas
        $naissances = Naissance::with('femelle')
            ->where('nb_vivant', '>', 0) // ✅ Seulement les naissances avec vivants
            ->latest('date_naissance')
            ->get()
            ->map(fn($n) => [
                'type' => 'green',
                'title' => 'Naissance enregistrée', // ✅ Titre cohérent
                'desc' => sprintf(
                    '%s (%d nés)',
                    $n->femelle?->nom ?? 'Inconnue',
                    $n->nb_vivant ?? 0
                ),
                'time' => Carbon::parse($n->created_at)->diffForHumans(),
                'date' => $n->created_at,
                'url' => route('naissances.show', $n->id),
                'icon' => 'bi-egg-fill',
            ]);

        // 2. Saillies (violet) → inchangé
        $saillies = Saillie::with('femelle', 'male')
            ->latest('date_saillie')
            ->get()
            ->map(fn($s) => [
                'type' => 'purple',
                'title' => 'Saillie programmée',
                'desc' => ($s->femelle?->nom ?? "F#{$s->femelle_id}") .
                    ' × ' .
                    ($s->male?->nom ?? "M#{$s->male_id}"),
                'time' => Carbon::parse($s->created_at)->diffForHumans(),
                'date' => $s->created_at,
                'url' => route('saillies.show', $s->id),
                'icon' => 'bi-heart',
            ]);

        // ✅ 3. Alertes : naissances incomplètes (orange) → basé sur Naissance
        $alertes = Naissance::whereNull('femelle_id')
            ->orWhereDoesntHave('femelle')
            ->latest('created_at')
            ->limit(10) // Limite pour ne pas surcharger
            ->get()
            ->map(fn($n) => [
                'type' => 'orange',
                'title' => '⚠️ Naissance incomplète',
                'desc' => "Naissance #{$n->id} sans femelle associée",
                'time' => Carbon::parse($n->created_at)->diffForHumans(),
                'date' => $n->created_at,
                'url' => route('naissances.edit', $n->id),
                'icon' => 'bi-exclamation-triangle',
            ]);

        // 4. Ventes (bleu) → inchangé
        $ventes = Sale::latest('created_at')
            ->get()
            ->map(fn($v) => [
                'type' => 'blue',
                'title' => 'Vente enregistrée',
                'desc' => number_format($v->total_amount, 0, ',', ' ') . ' FCFA',
                'time' => Carbon::parse($v->created_at)->diffForHumans(),
                'date' => $v->created_at,
                'url' => route('sales.show', $v->id),
                'icon' => 'bi-cart-check',
            ]);

        // Fusionner et trier par date décroissante
        $allActivities = collect([
            ...$naissances->toArray(),    // ✅ Naissances au lieu de MiseBas
            ...$saillies->toArray(),
            ...$alertes->toArray(),
            ...$ventes->toArray(),
        ])
            ->sortByDesc('date')
            ->values();

        // Pagination manuelle (20 par page)
        $perPage = 20;
        $currentPage = request()->get('page', 1);
        $paginatedActivities = $allActivities->forPage($currentPage, $perPage);

        return view('activites.index', [
            'activities' => $paginatedActivities,
            'total' => $allActivities->count(),
            'currentPage' => $currentPage,
            'perPage' => $perPage,
            'lastPage' => ceil($allActivities->count() / $perPage),
        ]);
    }
}