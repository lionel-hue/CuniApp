<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Lapereau extends Model
{
    use BelongsToUser;

    protected $table = 'lapereaux';

    protected $fillable = [
        'user_id',
        'firm_id',
        'naissance_id',
        'code',
        'nom',
        'sex',
        'etat',
        'poids_naissance',
        'etat_sante',
        'observations',
        'categorie',
        'alimentation_jour',
        'alimentation_semaine',
        // ✅ Champs vaccination simple (rétrocompatibilité)
        'vaccin_type',
        'vaccin_nom_autre',
        'vaccin_date',
        'vaccin_dose_numero',
        'vaccin_rappel_prevu',
        'vaccin_notes',
    ];

    protected $casts = [
        'sex' => 'string',
        'etat' => 'string',
        'etat_sante' => 'string',
        'poids_naissance' => 'decimal:2',
        'vaccin_date' => 'date',
        'vaccin_rappel_prevu' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function naissance(): BelongsTo
    {
        return $this->belongsTo(Naissance::class);
    }

    public function miseBas(): HasOneThrough
    {
        return $this->hasOneThrough(MiseBas::class, Naissance::class, 'id', 'id', 'naissance_id', 'mise_bas_id');
    }

    public function femelle(): HasOneThrough
    {
        return $this->hasOneThrough(Femelle::class, MiseBas::class, 'id', 'id', 'naissance_id', 'femelle_id');
    }

    public function saillie(): HasOneThrough
    {
        return $this->hasOneThrough(Saillie::class, MiseBas::class, 'id', 'id', 'naissance_id', 'saillie_id');
    }

    // Relation pour les ventes
    public function sales()
    {
        return $this->morphMany(SaleRabbit::class, 'rabbit', 'rabbit_type', 'rabbit_id')
            ->where('rabbit_type', 'lapereau');
    }

    /*
    |--------------------------------------------------------------------------
    | BOOT & CODE GENERATION
    |--------------------------------------------------------------------------
    */

    public static function boot()
    {
        parent::boot();
        static::creating(function ($lapereau) {
            if (empty($lapereau->code) && !self::isSeeding()) {
                $lapereau->code = self::generateUniqueCode();
            }
        });
    }

    public static function generateUniqueCode(): string
    {
        $year = date('Y');
        $prefix = "LAP-{$year}-";
        $lastLapereau = self::where('code', 'LIKE', "{$prefix}%")
            ->orderBy('code', 'desc')
            ->first();

        if ($lastLapereau) {
            $lastNumber = intval(substr($lastLapereau->code, -4));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public static function isCodeUnique(string $code, ?int $excludeId = null): bool
    {
        $query = self::where('code', $code);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        return !$query->exists();
    }

    public static function isSeeding(): bool
    {
        return app()->runningInConsole() &&
            (debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3)[2]['function'] ?? '') === 'seed';
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS - ÂGE & DATES
    |--------------------------------------------------------------------------
    */

    public function getDateNaissanceAttribute(): ?\Carbon\Carbon
    {
        return $this->naissance?->miseBas?->date_mise_bas;
    }

    public function getAgeSemainesAttribute(): int
    {
        if (!$this->date_naissance)
            return 0;
        return floor($this->date_naissance->diffInDays(now()) / 7);
    }

    public function getAgeJoursAttribute(): int
    {
        if (!$this->date_naissance)
            return 0;
        return $this->date_naissance->diffInDays(now());
    }


    /**
     * Formatage de la date du dernier vaccin pour l'affichage
     */
    public function getVaccinDateFormattedAttribute(): ?string
    {
        $lastVaccin = $this->vaccinations()->first();
        if ($lastVaccin) {
            return $lastVaccin->date_administration?->format('d/m/Y');
        }
        return $this->vaccin_date?->format('d/m/Y');
    }

    /**
     * Vérifie si un rappel est prévu (sur le dernier vaccin)
     */
    public function getHasPendingReminderAttribute(): bool
    {
        $lastVaccin = $this->vaccinations()->first();
        if ($lastVaccin) {
            return $lastVaccin->rappel_prevu &&
                $lastVaccin->rappel_prevu->isFuture() &&
                $lastVaccin->dose_numero < 3;
        }
        return $this->vaccin_rappel_prevu &&
            $this->vaccin_rappel_prevu->isFuture() &&
            ($this->vaccin_dose_numero ?? 1) < 3;
    }

    /**
     * Badge de statut pour l'UI (compatible multiples vaccins)
     */
    public function getVaccinStatusBadgeAttribute(): array
    {
        if (!$this->is_vaccinated) {
            return [
                'text' => 'Non vacciné',
                'class' => 'status-inactive',
                'icon' => 'bi bi-x-circle',
            ];
        }

        if ($this->has_pending_reminder) {
            return [
                'text' => 'Rappel prévu',
                'class' => 'status-warning',
                'icon' => 'bi bi-exclamation-triangle',
            ];
        }

        return [
            'text' => 'Vacciné',
            'class' => 'status-active',
            'icon' => 'bi bi-shield-check',
        ];
    }

    /**
     * Compteur de vaccins (table + fallback champ simple)
     */
    public function getVaccinationsCountAttribute(): int
    {
        if ($this->relationLoaded('vaccinations')) {
            return $this->vaccinations->count();
        }
        $count = $this->vaccinations()->count();
        // Fallback: si 0 mais champ simple rempli, compter comme 1
        if ($count === 0 && !empty($this->vaccin_type) && !empty($this->vaccin_date)) {
            return 1;
        }
        return $count;
    }

    /**
     * Dernier vaccin (pour affichage rapide)
     */
    public function getLastVaccinationAttribute()
    {
        return $this->vaccinations()->latest('date_administration')->first();
    }




    public function vaccinations()
    {
        return $this->hasMany(Vaccination::class)->orderBy('date_administration', 'desc');
    }

    // ✅ Accessor pour vérifier si vacciné (compatible multiples + simple)
    public function getIsVaccinatedAttribute(): bool
    {
        return $this->vaccinations()->exists() ||
            (!empty($this->vaccin_type) && !empty($this->vaccin_date));
    }

    // ✅ Accessor pour le nom du dernier vaccin
    public function getVaccinNomAttribute(): string
    {
        $lastVaccin = $this->vaccinations()->first();
        if ($lastVaccin) {
            return $lastVaccin->nom_lisible;
        }

        // Fallback champ simple
        if ($this->vaccin_type === 'autre' && $this->vaccin_nom_autre) {
            return $this->vaccin_nom_autre;
        }
        return match ($this->vaccin_type) {
            'myxomatose' => 'Myxomatose',
            'vhd' => 'VHD (Maladie hémorragique)',
            'pasteurellose' => 'Pasteurellose',
            'coccidiose' => 'Coccidiose',
            default => 'Non vacciné',
        };
    }
}