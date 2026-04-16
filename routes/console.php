<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule; // ✅ Ajouter cet import
use App\Console\Commands\CheckVaccineReminders; // ✅ Import de ta commande

// Commande inspire (existante)
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ✅ NOUVEAU : Planification des rappels de vaccination
Schedule::command(CheckVaccineReminders::class)
    ->dailyAt('08:00')           
    ->timezone('Africa/Porto-Novo') 
    ->appendOutputTo(storage_path('logs/vaccine-reminders.log')); // Log des exécutions

// ✅ Optionnel : Autres tâches planifiées futures
// Schedule::command('cache:prune-stale-tags')->hourly();
// Schedule::command('model:prune')->daily();