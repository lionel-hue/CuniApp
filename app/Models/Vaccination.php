<?php
// app/Models/Vaccination.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vaccination extends Model
{
    protected $fillable = [
        'lapereau_id',
        'type',
        'nom_personnalise',
        'date_administration',
        'dose_numero',
        'rappel_prevu',
        'notes',
        'administered_by',
    ];

    protected $casts = [
        'date_administration' => 'date',
        'rappel_prevu' => 'date',
    ];

    public function lapereau(): BelongsTo
    {
        return $this->belongsTo(Lapereau::class);
    }

    public function administeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'administered_by');
    }

    // ✅ Accessor pour le nom lisible
    public function getNomLisibleAttribute(): string
    {
        if ($this->type === 'autre' && $this->nom_personnalise) {
            return $this->nom_personnalise;
        }
        
        return match ($this->type) {
            'myxomatose' => 'Myxomatose',
            'vhd' => 'VHD (Maladie hémorragique)',
            'pasteurellose' => 'Pasteurellose',
            'coccidiose' => 'Coccidiose',
            default => 'Vaccin',
        };
    }

    // ✅ Vérifie si un rappel est en attente
    public function getRappelEnAttenteAttribute(): bool
    {
        return $this->rappel_prevu && 
               $this->rappel_prevu->isFuture() && 
               $this->dose_numero < 3;
    }
}