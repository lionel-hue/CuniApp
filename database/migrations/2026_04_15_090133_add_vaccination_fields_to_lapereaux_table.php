<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('lapereaux', function (Blueprint $table) {
            // Type de vaccin principal
            if (!Schema::hasColumn('lapereaux', 'vaccin_type')) {
                $table->enum('vaccin_type', [
                    'myxomatose', 
                    'vhd',           // Maladie hémorragique virale
                    'pasteurellose', 
                    'coccidiose', 
                    'autre'
                ])->nullable()->after('etat_sante')
                ->comment('Type de vaccin administré');
            }
            
            // Nom personnalisé si "Autre"
            if (!Schema::hasColumn('lapereaux', 'vaccin_nom_autre')) {
                $table->string('vaccin_nom_autre', 100)->nullable()->after('vaccin_type')
                ->comment('Nom du vaccin si type = autre');
            }
            
            // Date d'administration
            if (!Schema::hasColumn('lapereaux', 'vaccin_date')) {
                $table->date('vaccin_date')->nullable()->after('vaccin_nom_autre')
                ->comment('Date de la vaccination');
            }
            
            // Numéro de dose (1ère, 2ème, rappel...)
            if (!Schema::hasColumn('lapereaux', 'vaccin_dose_numero')) {
                $table->unsignedTinyInteger('vaccin_dose_numero')->default(1)->after('vaccin_date')
                ->comment('Numéro de la dose (1, 2, 3...)');
            }
            
            // Date de rappel prévue
            if (!Schema::hasColumn('lapereaux', 'vaccin_rappel_prevu')) {
                $table->date('vaccin_rappel_prevu')->nullable()->after('vaccin_dose_numero')
                ->comment('Date prévue pour le rappel');
            }
            
            // Notes complémentaires
            if (!Schema::hasColumn('lapereaux', 'vaccin_notes')) {
                $table->text('vaccin_notes')->nullable()->after('vaccin_rappel_prevu')
                ->comment('Notes : lot, vétérinaire, réaction, etc.');
            }
            
            // Index pour recherches fréquentes
            $table->index('vaccin_type');
            $table->index('vaccin_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lapereaux', function (Blueprint $table) {
            // Supprimer les index
            $table->dropIndex(['vaccin_type']);
            $table->dropIndex(['vaccin_date']);
            
            // Supprimer les colonnes
            $table->dropColumn([
                'vaccin_type',
                'vaccin_nom_autre', 
                'vaccin_date',
                'vaccin_dose_numero',
                'vaccin_rappel_prevu',
                'vaccin_notes',
            ]);
        });
    }
};