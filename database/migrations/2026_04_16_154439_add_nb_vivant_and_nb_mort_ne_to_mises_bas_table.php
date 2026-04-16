<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mises_bas', function (Blueprint $table) {
            // ✅ Ajout des colonnes avec valeurs par défaut
            $table->integer('nb_vivant')->default(0)->after('date_sevrage');
            $table->integer('nb_mort_ne')->default(0)->after('nb_vivant');
        });
    }

    public function down(): void
    {
        Schema::table('mises_bas', function (Blueprint $table) {
            $table->dropColumn(['nb_vivant', 'nb_mort_ne']);
        });
    }
};