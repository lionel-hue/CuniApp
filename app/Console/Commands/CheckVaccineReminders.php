<?php
// app/Console/Commands/CheckVaccineReminders.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Vaccination;
use App\Models\Notification;
use Carbon\Carbon;

class CheckVaccineReminders extends Command
{
    protected $signature = 'vaccines:reminders';
    protected $description = 'Rappels de vaccination : J-2, J-1 et Jour J';

    public function handle()
    {
        // ✅ Fenêtre EXACTE : du jour même (J-0) jusqu'à J+2 (donc rappelle J-2, J-1, J)
        // Exemple : Si on est le 15 avril → on notifie pour rappels du 15, 16, 17 avril
        $start = Carbon::today();           // Aujourd'hui (J-0)
        $end   = Carbon::today()->addDays(2); // Dans 2 jours (J+2)

        $reminders = Vaccination::whereBetween('rappel_prevu', [$start, $end])
            ->whereNotNull('rappel_prevu')
            ->where('rappel_notified', false) // ✅ Évite les doublons
            ->with(['lapereau.naissance.user'])
            ->get();

        $count = 0;

        foreach ($reminders as $reminder) {
            $lapin = $reminder->lapereau;
            $user  = $lapin?->naissance?->user;

            // ✅ Sécurité : skip si pas d'utilisateur
            if (!$user || !$lapin) {
                $this->warn(" Lapin #{$reminder->id} sans utilisateur associé");
                continue;
            }

            // ✅ Crée la notification avec TON modèle
            Notification::create([
                'user_id'    => $user->id,
                'type'       => 'warning',
                'title'      => ' Rappel de Vaccination',
                'message'    => "Le lapereau {$lapin->nom} ({$lapin->code}) a un rappel prévu le {$reminder->rappel_prevu->format('d/m/Y')}.",
                'action_url' => route('lapins.show', $lapin->id),
                'is_read'    => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // ✅ Marque comme notifié
            $reminder->update(['rappel_notified' => true]);
            $count++;
            
            $this->info(" Notification créée pour {$lapin->code} (rappel: {$reminder->rappel_prevu->format('d/m/Y')})");
        }

        $this->info(" {$count} notification(s) envoyée(s) pour rappels J-0 à J+2.");
        logger("Vaccine reminders: {$count} notifications sent");
    }
}