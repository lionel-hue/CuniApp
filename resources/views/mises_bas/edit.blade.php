@extends('layouts.cuniapp')

@section('title', 'Modifier Mise Bas - CuniApp Élevage')

@section('content')
<div class="page-header">
    <div>
        <h2 class="page-title">
            <i class="bi bi-pencil-square"></i>
            Modifier la Mise Bas
        </h2>
        <div class="breadcrumb">
            <a href="{{ route('dashboard') }}">Tableau de bord</a>
            <span>/</span>
            <a href="{{ route('mises-bas.index') }}">Mises Bas</a>
            <span>/</span>
            <span>Modification</span>
        </div>
    </div>
</div>

@if ($errors->any())
<div class="alert-cuni error">
    <i class="bi bi-exclamation-triangle-fill"></i>
    <div>
        <strong>Erreurs de validation</strong>
        <ul style="margin: 8px 0 0 20px; padding: 0;">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif

<div class="cuni-card">
    <div class="card-header-custom">
        <h3 class="card-title">
            <i class="bi bi-egg"></i>
            Informations de la Mise Bas
        </h3>
        <a href="{{ route('mises-bas.index') }}" class="btn-cuni sm secondary">
            <i class="bi bi-arrow-left"></i>
            Retour
        </a>
    </div>
    <div class="card-body">
        <form action="{{ route('mises-bas.update', $miseBas->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="settings-grid">
                
                <!-- ✅ Femelle -->
                <div class="form-group">
                    <label class="form-label">Femelle *</label>
                    <select name="femelle_id" class="form-select" required>
                        @foreach($femelles as $femelle)
                        <option value="{{ $femelle->id }}" {{ old('femelle_id', $miseBas->femelle_id) == $femelle->id ? 'selected' : '' }}>
                            {{ $femelle->nom }} ({{ $femelle->code }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- ✅ Date de mise bas (formatée Y-m-d) -->
                <div class="form-group">
                    <label class="form-label">Date de mise bas *</label>
                    <input type="date" 
                           name="date_mise_bas" 
                           class="form-control" 
                           value="{{ old('date_mise_bas', $miseBas->date_mise_bas?->format('Y-m-d')) }}" 
                           required>
                    <small style="color: var(--text-tertiary); font-size: 12px; margin-top: 6px; display: block;">
                        <i class="bi bi-clock"></i> Date réelle de la naissance
                    </small>
                </div>

                <!-- ✅ Jeunes vivants (avec fallback) -->
                <div class="form-group">
                    <label class="form-label">Jeunes vivants *</label>
                    <input type="number" 
                           name="nb_vivant" 
                           class="form-control" 
                           value="{{ old('nb_vivant', $miseBas->nb_vivant ?? 1) }}" 
                           required 
                           min="1"
                           max="20">
                    <small style="color: var(--text-tertiary); font-size: 12px; margin-top: 6px; display: block;">
                        <i class="bi bi-info-circle"></i> Minimum 1 lapereau vivant
                    </small>
                </div>

                <!-- ✅ Morts-nés (avec fallback) -->
                <div class="form-group">
                    <label class="form-label">Morts-nés</label>
                    <input type="number" 
                           name="nb_mort_ne" 
                           class="form-control" 
                           value="{{ old('nb_mort_ne', $miseBas->nb_mort_ne ?? 0) }}" 
                           min="0"
                           max="20">
                    <small style="color: var(--text-tertiary); font-size: 12px; margin-top: 6px; display: block;">
                        <i class="bi bi-info-circle"></i> Optionnel - laissez 0 si aucun
                    </small>
                </div>

                <!-- ✅ Total de la portée (calculé, lecture seule) -->
                <div class="form-group">
                    <label class="form-label">Total de la portée</label>
                    <input type="text" 
                           id="total_portee" 
                           class="form-control" 
                           value="{{ ($miseBas->nb_vivant ?? 0) + ($miseBas->nb_mort_ne ?? 0) }}"
                           readonly 
                           style="background: var(--surface-alt); font-weight: 600;">
                    <small style="color: var(--text-tertiary); font-size: 12px; margin-top: 6px; display: block;">
                        <i class="bi bi-calculator"></i> Calculé automatiquement
                    </small>
                </div>

                <!-- ✅ Date de sevrage (formatée Y-m-d) -->
                <div class="form-group">
                    <label class="form-label">Date de sevrage (prévue)</label>
                    <input type="date" 
                           name="date_sevrage" 
                           id="date_sevrage"
                           class="form-control" 
                           value="{{ old('date_sevrage', $miseBas->date_sevrage?->format('Y-m-d')) }}">
                    <small style="color: var(--text-tertiary); font-size: 12px; margin-top: 6px; display: block;">
                        <i class="bi bi-info-circle"></i> Recommandé: 6 semaines après la naissance
                    </small>
                </div>

                <!-- ✅ Poids moyen au sevrage (avec fallback) -->
                <div class="form-group">
                    <label class="form-label">Poids moyen au sevrage (kg)</label>
                    <input type="number" 
                           step="0.01" 
                           name="poids_moyen_sevrage" 
                           class="form-control" 
                           value="{{ old('poids_moyen_sevrage', $miseBas->poids_moyen_sevrage ?? '') }}" 
                           min="0"
                           max="5"
                           placeholder="0.00">
                    <small style="color: var(--text-tertiary); font-size: 12px; margin-top: 6px; display: block;">
                        <i class="bi bi-info-circle"></i> Poids moyen par lapereau
                    </small>
                </div>

            </div>

            <!-- Action Buttons -->
            <div style="margin-top: 32px; display: flex; gap: 12px; padding-top: 24px; border-top: 1px solid var(--surface-border);">
                <button type="submit" class="btn-cuni primary">
                    <i class="bi bi-check-circle"></i>
                    Enregistrer les modifications
                </button>
                <a href="{{ route('mises-bas.index') }}" class="btn-cuni secondary">
                    <i class="bi bi-x-circle"></i>
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>

{{-- ✅ Scripts pour calculs automatiques --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // === 1. Auto-calculate total portée ===
    const nbVivantInput = document.querySelector('input[name="nb_vivant"]');
    const nbMortNeInput = document.querySelector('input[name="nb_mort_ne"]');
    const totalPorteeInput = document.getElementById('total_portee');
    
    function updateTotal() {
        const vivant = parseInt(nbVivantInput.value) || 0;
        const mortNe = parseInt(nbMortNeInput.value) || 0;
        if (totalPorteeInput) {
            totalPorteeInput.value = vivant + mortNe;
        }
    }
    
    if (nbVivantInput) nbVivantInput.addEventListener('input', updateTotal);
    if (nbMortNeInput) nbMortNeInput.addEventListener('input', updateTotal);
    updateTotal(); // Initial calculation
    
    // === 2. Auto-calculate sevrage date if empty ===
    const dateMiseBasInput = document.querySelector('input[name="date_mise_bas"]');
    const dateSevrageInput = document.getElementById('date_sevrage');
    
    function calculateSevrageDate(birthDateString) {
        if (!birthDateString) return null;
        const birthDate = new Date(birthDateString);
        const sevrageDate = new Date(birthDate);
        sevrageDate.setDate(sevrageDate.getDate() + 42); // 6 weeks = 42 days
        return sevrageDate.toISOString().split('T')[0];
    }
    
    // If sevrage date is empty on load, calculate it from birth date
    if (dateMiseBasInput?.value && dateSevrageInput && !dateSevrageInput.value) {
        dateSevrageInput.value = calculateSevrageDate(dateMiseBasInput.value);
    }
    
    // Recalculate when birth date changes (only if sevrage was auto-filled or empty)
    if (dateMiseBasInput && dateSevrageInput) {
        dateMiseBasInput.addEventListener('change', function() {
            // Only auto-fill if sevrage was empty or matches previous auto-calculation
            if (!dateSevrageInput.dataset.manuallyEdited || !dateSevrageInput.value) {
                const newSevrage = calculateSevrageDate(this.value);
                if (newSevrage) {
                    dateSevrageInput.value = newSevrage;
                }
            }
        });
        
        // Mark as manually edited if user changes the sevrage date
        dateSevrageInput.addEventListener('input', function() {
            this.dataset.manuallyEdited = 'true';
        });
    }
});
</script>
@endpush

{{-- ✅ Styles pour le formulaire --}}
@push('styles')
<style>
    .settings-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 16px;
    }
    
    .form-group {
        margin-bottom: 16px;
    }
    
    .form-label {
        display: block;
        font-weight: 500;
        margin-bottom: 6px;
        color: var(--text-primary);
        font-size: 13px;
    }
    
    .form-control, .form-select {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid var(--surface-border);
        border-radius: var(--radius);
        background: var(--surface);
        color: var(--text-primary);
        font-size: 13px;
    }
    
    .form-control:focus, .form-select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-subtle);
    }
    
    .form-control[readonly] {
        background: var(--surface-alt);
        cursor: not-allowed;
    }
    
    small {
        color: var(--text-tertiary);
        font-size: 11px;
        display: block;
        margin-top: 4px;
    }
    
    @media (max-width: 768px) {
        .settings-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush
@endsection