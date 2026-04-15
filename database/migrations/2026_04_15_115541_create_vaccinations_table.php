<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vaccinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lapereau_id')->constrained('lapereaux')->onDelete('cascade');
            $table->enum('type', ['myxomatose', 'vhd', 'pasteurellose', 'coccidiose', 'autre']);
            $table->string('nom_personnalise')->nullable()->comment('Si type = autre');
            $table->date('date_administration');
            $table->unsignedTinyInteger('dose_numero')->default(1);
            $table->date('rappel_prevu')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('administered_by')->nullable()->constrained('users');
            $table->timestamps();
            
            // Index pour recherches fréquentes
            $table->index(['lapereau_id', 'date_administration']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vaccinations');
    }
};