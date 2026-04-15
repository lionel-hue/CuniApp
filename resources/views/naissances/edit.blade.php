@extends('layouts.cuniapp')

@section('title', 'Modifier Naissance - CuniApp Élevage')

@section('content')
<div class="page-header">
    <div>
        <h2 class="page-title"><i class="bi bi-pencil-square"></i> Modifier la Naissance</h2>
        <div class="breadcrumb">
            <a href="{{ route('dashboard') }}">Tableau de bord</a>
            <span>/</span>
            <a href="{{ route('naissances.index') }}">Naissances</a>
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
            <li>
                @if(str_contains($error, 'validation.required'))
                    ⚠️ Ce champ est obligatoire
                @elseif(str_contains($error, 'validation.'))
                    {{ str_replace('validation.', '', $error) }}
                @else
                    {{ $error }}
                @endif
            </li>
            @endforeach
        </ul>
    </div>
</div>
@endif

<div class="cuni-card">
    <div class="card-header-custom">
        <h3 class="card-title"><i class="bi bi-egg-fill"></i> Informations de la Naissance</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('naissances.update', $naissance) }}" method="POST" id="naissanceEditForm">
            @csrf
            @method('PUT')
            
            @if (!$naissance->sex_verified)
            <div style="background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.3); 
                border-radius: var(--radius-lg); padding: 16px; margin-bottom: 24px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <i class="bi bi-exclamation-triangle" style="font-size: 24px; color: var(--accent-orange);"></i>
                    <div style="flex: 1;">
                        <div style="font-weight: 600; color: var(--accent-orange);">
                            Vérification du sexe en attente
                        </div>
                        <div style="font-size: 13px; color: var(--text-secondary);">
                            Cette portée a {{ $naissance->jours_depuis_naissance }} jours.
                            @if($canVerifySex)
                                <span style="color: var(--accent-green); font-weight: 600;">
                                    ✅ Vous pouvez maintenant vérifier le sexe des lapereaux
                                </span>
                            @else
                                <span style="color: var(--accent-orange); font-weight: 600;">
                                    ⏳ Attendez encore {{ $daysUntilVerification }} jours
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- ✅ CHAMPS OBLIGATOIRES DE LA NAISSANCE -->
            <div class="settings-grid" style="margin-bottom: 24px;">
                <div class="form-group">
                    <label class="form-label">État de santé général *</label>
                    <select name="etat_sante" class="form-select" required>
                        <option value="Excellent" {{ old('etat_sante', $naissance->etat_sante) == 'Excellent' ? 'selected' : '' }}>Excellent</option>
                        <option value="Bon" {{ old('etat_sante', $naissance->etat_sante) == 'Bon' ? 'selected' : '' }}>Bon</option>
                        <option value="Moyen" {{ old('etat_sante', $naissance->etat_sante) == 'Moyen' ? 'selected' : '' }}>Moyen</option>
                        <option value="Faible" {{ old('etat_sante', $naissance->etat_sante) == 'Faible' ? 'selected' : '' }}>Faible</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Date de sevrage prévue *</label>
                    <input type="date" name="date_sevrage_prevue" class="form-control" 
                           value="{{ old('date_sevrage_prevue', $naissance->date_sevrage_prevue?->format('Y-m-d')) }}" required>
                    <small style="color: var(--text-tertiary); font-size: 12px;">
                        <i class="bi bi-info-circle"></i> Recommandé: 6 semaines après la naissance
                    </small>
                </div>
            </div>

            <!-- ✅ Lapereaux avec vaccins multiples -->
            <div class="form-section" style="border: 2px solid var(--primary-subtle);">
                <h4 class="section-subtitle"><i class="bi bi-collection"></i> Lapereaux ({{ count($lapereaux) }})</h4>
                
                <div id="rabbitsContainer"></div>
                
                <button type="button" class="btn-cuni secondary" onclick="addRabbitRow()" style="margin-top: 12px;">
                    <i class="bi bi-plus-lg"></i> Ajouter un lapereau
                </button>
            </div>

            <!-- Verification Checkbox (only if 10+ days) -->
            @if($canVerifySex)
            <div class="form-section" style="background: rgba(16, 185, 129, 0.05); border-color: rgba(16, 185, 129, 0.2); margin-top: 24px;">
                <h4 class="section-subtitle">
                    <i class="bi bi-shield-check" style="color: var(--accent-green);"></i> 
                    Confirmation de Vérification
                </h4>
                <div class="form-group">
                    <label style="display: flex; align-items: flex-start; gap: 12px; cursor: pointer;">
                        <input type="checkbox" name="sex_verified" value="1" 
                            {{ old('sex_verified', $naissance->sex_verified) ? 'checked' : '' }} 
                            style="width: 20px; height: 20px; accent-color: var(--accent-green);">
                        <div>
                            <div style="font-weight: 600; color: var(--text-primary);">
                                Sexe et date vérifiés
                            </div>
                            <div style="font-size: 13px; color: var(--text-tertiary);">
                                Je confirme que le sexe des {{ count($lapereaux) }} lapereaux a été vérifié
                            </div>
                        </div>
                    </label>
                </div>
            </div>
            @else
            <div class="alert-box warning" style="margin-top: 24px;">
                <i class="bi bi-lock"></i>
                <div>
                    <strong>Vérification verrouillée:</strong> 
                    La vérification du sexe sera disponible dans {{ $daysUntilVerification }} jours.
                </div>
            </div>
            @endif

            <div style="margin-top: 32px; display: flex; gap: 12px; padding-top: 24px; border-top: 1px solid var(--surface-border);">
                <button type="submit" class="btn-cuni primary">
                    <i class="bi bi-check-circle"></i> Enregistrer les modifications
                </button>
                <a href="{{ route('naissances.index') }}" class="btn-cuni secondary">
                    <i class="bi bi-x-circle"></i> Annuler
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let rabbitCount = 0;
// ✅ Lapereaux formatés avec vaccins multiples
const existingRabbits = @json($lapereaux ?? []);
const canVerifySex = {{ $canVerifySex ? 'true' : 'false' }};

// Compteur de vaccins par lapereau
let vaccineFieldCount = {};

function addRabbitRow(data = {}) {
    rabbitCount++;
    vaccineFieldCount[rabbitCount] = 0;
    
    const container = document.getElementById('rabbitsContainer');
    const row = document.createElement('div');
    row.className = 'rabbit-row';
    row.style.cssText = 'display: grid; grid-template-columns: 1fr 1fr 1fr 1fr 1fr auto; gap: 10px; margin-bottom: 10px; padding: 10px; background: var(--surface-alt); border-radius: var(--radius); align-items: end;';
    
    // Gestion du champ Sexe
    let sexHtml = '';
    if (canVerifySex) {
        sexHtml = `
            <select name="rabbits[${rabbitCount}][sex]" class="form-select rabbit-sex" required>
                <option value="">-- Sélectionner --</option>
                <option value="male" ${data.sex === 'male' ? 'selected' : ''}>Mâle</option>
                <option value="female" ${data.sex === 'female' ? 'selected' : ''}>Femelle</option>
            </select>
        `;
    } else {
        if (data.sex) {
            const sexLabel = data.sex === 'male' ? 'Mâle' : 'Femelle';
            sexHtml = `
                <div style="display:flex;align-items:center;gap:8px;">
                    <span style="font-weight:500;color:var(--text-primary);">${sexLabel}</span>
                    <input type="hidden" name="rabbits[${rabbitCount}][sex]" value="${data.sex}">
                </div>
                <small style="color:var(--accent-orange);font-size:10px;">🔒 Vérifié</small>
            `;
        } else {
            sexHtml = `
                <select name="rabbits[${rabbitCount}][sex]" class="form-select" disabled>
                    <option value="">-- À vérifier (10j) --</option>
                    <option value="male">Mâle</option>
                    <option value="female">Femelle</option>
                </select>
                <small style="color:var(--accent-orange);font-size:10px;">⏳ 10 jours</small>
            `;
        }
    }
    
    // Parsing des dates
    const parseDate = (d) => d ? String(d).split(/[T\s]/)[0] : '';
    
    row.innerHTML = `
        <!-- Nom + ID caché -->
        <div>
            <label class="form-label" style="font-size: 12px;">Nom</label>
            <input type="text" name="rabbits[${rabbitCount}][nom]" class="form-control" 
                value="${data.nom || ''}" placeholder="Ex: Toto">
            ${data.id ? `<input type="hidden" name="rabbits[${rabbitCount}][id]" value="${data.id}">` : ''}
        </div>

        <!-- Sexe -->
        <div>
            <label class="form-label" style="font-size: 12px;">Sexe ${canVerifySex ? '*' : ''}</label>
            ${sexHtml}
        </div>

        <!-- Code (lecture seule) -->
        <div>
            <label class="form-label" style="font-size: 12px;">Code</label>
            <input type="text" class="form-control" value="${data.code || 'Auto'}" readonly 
                style="background: var(--gray-100); font-size: 11px;">
        </div>

        <!-- État -->
        <div>
            <label class="form-label" style="font-size: 12px;">État *</label>
            <select name="rabbits[${rabbitCount}][etat]" class="form-select rabbit-etat" required>
                <option value="vivant" ${data.etat === 'vivant' ? 'selected' : ''}>Vivant</option>
                <option value="mort" ${data.etat === 'mort' ? 'selected' : ''}>Mort</option>
                <option value="vendu" ${data.etat === 'vendu' ? 'selected' : ''}>Vendu</option>
            </select>
        </div>

        <!-- ✅ VACCINATIONS MULTIPLES (pleine largeur) -->
        <div style="grid-column: 1 / -1;">
            <label class="form-label" style="font-size: 12px;">
                <i class="bi bi-shield-check"></i> Vaccinations
            </label>
            
            <!-- Container pour les vaccins existants + nouveaux -->
            <div id="vaccinesContainer_${rabbitCount}" style="margin-bottom: 6px;">
                ${buildExistingVaccines(rabbitCount, data.vaccinations || [])}
            </div>
            
            <!-- Bouton ajouter un vaccin -->
            <button type="button" class="btn-cuni secondary sm" 
                    onclick="addVaccineField(${rabbitCount})" 
                    style="width: 100%; font-size: 10px; padding: 4px 8px; margin-bottom: 2px;">
                <i class="bi bi-plus"></i> Ajouter un vaccin
            </button>
            
            <small style="color: var(--text-tertiary); font-size: 9px; display: block;">
                <i class="bi bi-info-circle"></i> Admin + rappel optionnel
            </small>
        </div>

        <!-- Bouton supprimer -->
        <button type="button" class="btn-cuni sm danger" onclick="removeRabbitRow(this)" 
                style="margin-bottom: 0; align-self: center;">
            <i class="bi bi-trash"></i>
        </button>
    `;

    container.appendChild(row);
    
    // Initialiser les affichages de nom pour les vaccins existants
    if (data.vaccinations && Array.isArray(data.vaccinations)) {
        data.vaccinations.forEach((_, i) => {
            const select = row.querySelector(`select[name="rabbits[${rabbitCount}][vaccins][${i+1}][type]"]`);
            if (select) updateVaccineName(select, rabbitCount, i + 1);
        });
    }
}

// ✅ Construire les champs pour les vaccins EXISTANTS (depuis la BDD)
function buildExistingVaccines(rabbitIndex, vaccinations) {
    if (!Array.isArray(vaccinations) || vaccinations.length === 0) return '';
    
    return vaccinations.map((v, i) => {
        const vaccineIndex = i + 1;
        const type = v.type || '';
        const nomAutre = v.nom_personnalise || v.nom_autre || '';
        const date = parseDate(v.date_administration || v.date);
        const dose = v.dose_numero || v.dose || 1;
        const rappel = parseDate(v.rappel_prevu || v.rappel);
        const notes = v.notes || '';
        const vaccinId = v.id || '';
        
        return `
            <div class="vaccine-field" style="background: var(--surface-alt); padding: 8px 10px; border-radius: var(--radius); margin-bottom: 6px; border: 1px solid var(--surface-border);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                    <strong style="font-size: 10px; color: var(--text-primary);">Vaccin #${vaccineIndex}</strong>
                    <button type="button" class="btn-cuni sm danger" onclick="removeVaccineField(this)" style="padding: 2px 6px; font-size: 9px;">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
                
                <select name="rabbits[${rabbitIndex}][vaccins][${vaccineIndex}][type]" 
                        class="form-select" 
                        style="font-size: 10px; padding: 5px 8px; margin-bottom: 4px;"
                        onchange="updateVaccineName(this, ${rabbitIndex}, ${vaccineIndex})">
                    <option value="">-- Type --</option>
                    <option value="myxomatose" ${type === 'myxomatose' ? 'selected' : ''}>Myxomatose</option>
                    <option value="vhd" ${type === 'vhd' ? 'selected' : ''}>VHD</option>
                    <option value="pasteurellose" ${type === 'pasteurellose' ? 'selected' : ''}>Pasteurellose</option>
                    <option value="coccidiose" ${type === 'coccidiose' ? 'selected' : ''}>Coccidiose</option>
                    <option value="autre" ${type === 'autre' ? 'selected' : ''}>Autre</option>
                </select>
                
                <input type="hidden" name="rabbits[${rabbitIndex}][vaccins][${vaccineIndex}][id]" value="${vaccinId}">
                
                <input type="text" 
                       name="rabbits[${rabbitIndex}][vaccins][${vaccineIndex}][nom_autre]" 
                       class="form-control vaccine-nom-autre"
                       placeholder="Nom si Autre" 
                       value="${nomAutre}"
                       style="font-size: 10px; padding: 5px 8px; margin-bottom: 4px; display: ${type === 'autre' ? 'block' : 'none'};">
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 4px; margin-bottom: 4px;">
                    <div>
                        <label style="font-size: 8px; color: var(--text-tertiary);">📅 Admin</label>
                        <input type="date" 
                               name="rabbits[${rabbitIndex}][vaccins][${vaccineIndex}][date]" 
                               class="form-control" 
                               value="${date}"
                               style="font-size: 10px; padding: 4px 6px;"
                               required>
                    </div>
                    <div>
                        <label style="font-size: 8px; color: var(--text-tertiary);">🔁 Rappel</label>
                        <input type="date" 
                               name="rabbits[${rabbitIndex}][vaccins][${vaccineIndex}][rappel]" 
                               class="form-control" 
                               value="${rappel}"
                               style="font-size: 10px; padding: 4px 6px;">
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 60px 1fr; gap: 4px;">
                    <div>
                        <label style="font-size: 8px; color: var(--text-tertiary);">Dose</label>
                        <input type="number" 
                               name="rabbits[${rabbitIndex}][vaccins][${vaccineIndex}][dose]" 
                               class="form-control" 
                               value="${dose}"
                               min="1" max="10"
                               style="font-size: 10px; padding: 4px 6px; text-align: center;">
                    </div>
                    <div>
                        <label style="font-size: 8px; color: var(--text-tertiary);">Notes</label>
                        <input type="text"
                               name="rabbits[${rabbitIndex}][vaccins][${vaccineIndex}][notes]" 
                               class="form-control" 
                               value="${notes}"
                               placeholder="Lot..."
                               style="font-size: 10px; padding: 4px 6px;">
                    </div>
                </div>
                
                <div id="vaccineNameDisplay_${rabbitIndex}_${vaccineIndex}" 
                     style="display:${type ? 'block' : 'none'}; font-size:9px; font-weight:600; color:var(--accent-green); margin-top:4px; padding:2px 6px; background:rgba(16,185,129,0.1); border-radius:3px; text-align:center;">
                    ✅ ${getVaccineName(type, nomAutre)}
                </div>
            </div>
        `;
    }).join('');
}

// ✅ Ajouter un NOUVEAU champ vaccin
function addVaccineField(rabbitIndex) {
    if (!vaccineFieldCount[rabbitIndex]) vaccineFieldCount[rabbitIndex] = 0;
    vaccineFieldCount[rabbitIndex]++;
    
    const container = document.getElementById(`vaccinesContainer_${rabbitIndex}`);
    const vaccineIndex = vaccineFieldCount[rabbitIndex];
    
    // Générer un index unique pour les nouveaux vaccins (après les existants)
    const existingCount = container.querySelectorAll('.vaccine-field').length;
    const newIndex = existingCount + 1;
    
    container.insertAdjacentHTML('beforeend', buildNewVaccineField(rabbitIndex, newIndex));
}

// ✅ Construire un champ pour un NOUVEAU vaccin (sans ID)
function buildNewVaccineField(rabbitIndex, vaccineIndex) {
    return `
        <div class="vaccine-field" style="background: var(--surface-alt); padding: 8px 10px; border-radius: var(--radius); margin-bottom: 6px; border: 1px solid var(--surface-border);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                <strong style="font-size: 10px; color: var(--text-primary);">Nouveau vaccin</strong>
                <button type="button" class="btn-cuni sm danger" onclick="removeVaccineField(this)" style="padding: 2px 6px; font-size: 9px;">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
            
            <select name="rabbits[${rabbitIndex}][vaccins][new_${vaccineIndex}][type]" 
                    class="form-select" 
                    style="font-size: 10px; padding: 5px 8px; margin-bottom: 4px;"
                    onchange="updateVaccineName(this, ${rabbitIndex}, 'new_${vaccineIndex}')">
                <option value="">-- Type --</option>
                <option value="myxomatose">Myxomatose</option>
                <option value="vhd">VHD</option>
                <option value="pasteurellose">Pasteurellose</option>
                <option value="coccidiose">Coccidiose</option>
                <option value="autre">Autre</option>
            </select>
            
            <input type="text" 
                   name="rabbits[${rabbitIndex}][vaccins][new_${vaccineIndex}][nom_autre]" 
                   class="form-control vaccine-nom-autre"
                   placeholder="Nom si Autre" 
                   style="font-size: 10px; padding: 5px 8px; margin-bottom: 4px; display: none;">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 4px; margin-bottom: 4px;">
                <div>
                    <label style="font-size: 8px; color: var(--text-tertiary);">📅 Admin</label>
                    <input type="date" 
                           name="rabbits[${rabbitIndex}][vaccins][new_${vaccineIndex}][date]" 
                           class="form-control" 
                           style="font-size: 10px; padding: 4px 6px;"
                           required>
                </div>
                <div>
                    <label style="font-size: 8px; color: var(--text-tertiary);">🔁 Rappel</label>
                    <input type="date" 
                           name="rabbits[${rabbitIndex}][vaccins][new_${vaccineIndex}][rappel]" 
                           class="form-control" 
                           style="font-size: 10px; padding: 4px 6px;">
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 60px 1fr; gap: 4px;">
                <div>
                    <label style="font-size: 8px; color: var(--text-tertiary);">Dose</label>
                    <input type="number" 
                           name="rabbits[${rabbitIndex}][vaccins][new_${vaccineIndex}][dose]" 
                           class="form-control" 
                           value="1"
                           min="1" max="10"
                           style="font-size: 10px; padding: 4px 6px; text-align: center;">
                </div>
                <div>
                    <label style="font-size: 8px; color: var(--text-tertiary);">Notes</label>
                    <input type="text"
                           name="rabbits[${rabbitIndex}][vaccins][new_${vaccineIndex}][notes]" 
                           class="form-control" 
                           placeholder="Lot..."
                           style="font-size: 10px; padding: 4px 6px;">
                </div>
            </div>
            
            <div id="vaccineNameDisplay_${rabbitIndex}_new_${vaccineIndex}" 
                 style="display:none; font-size:9px; font-weight:600; color:var(--accent-green); margin-top:4px; padding:2px 6px; background:rgba(16,185,129,0.1); border-radius:3px; text-align:center;">
            </div>
        </div>
    `;
}

// ✅ Supprimer un champ vaccin
function removeVaccineField(btn) {
    btn.closest('.vaccine-field').remove();
}

// ✅ Mettre à jour l'affichage du nom du vaccin
function updateVaccineName(select, rabbitIndex, vaccineIndex) {
    const autreInput = select.parentElement.querySelector('.vaccine-nom-autre');
    const nameDiv = document.getElementById(`vaccineNameDisplay_${rabbitIndex}_${vaccineIndex}`);
    let name = '';
    
    if (select.value === 'autre') {
        autreInput.style.display = 'block';
        autreInput.required = true;
        name = autreInput.value || 'Personnalisé';
    } else if (select.value) {
        autreInput.style.display = 'none';
        autreInput.required = false;
        const names = { myxomatose: 'Myxomatose', vhd: 'VHD', pasteurellose: 'Pasteurellose', coccidiose: 'Coccidiose' };
        name = names[select.value] || 'Vaccin';
    } else {
        autreInput.style.display = 'none';
    }
    
    if (nameDiv) {
        nameDiv.textContent = name ? `✅ ${name}` : '';
        nameDiv.style.display = name ? 'block' : 'none';
    }
}

// ✅ Nom lisible d'un vaccin
function getVaccineName(type, nomAutre) {
    if (type === 'autre' && nomAutre) return nomAutre;
    const names = { myxomatose: 'Myxomatose', vhd: 'VHD', pasteurellose: 'Pasteurellose', coccidiose: 'Coccidiose' };
    return names[type] || 'Vaccin';
}

// ✅ Supprimer une ligne lapereau
function removeRabbitRow(btn) {
    btn.closest('.rabbit-row').remove();
}

// ✅ Parsing de date helper
function parseDate(d) {
    return d ? String(d).split(/[T\s]/)[0] : '';
}

// ✅ Initialisation au chargement
window.addEventListener('DOMContentLoaded', () => {
    if (existingRabbits.length > 0) {
        existingRabbits.forEach(rabbit => {
            // Préparer les vaccins pour l'affichage
            const vaccinations = rabbit.vaccinations || [];
            addRabbitRow({
                id: rabbit.id,
                nom: rabbit.nom,
                code: rabbit.code,
                sex: rabbit.sex,
                etat: rabbit.etat,
                poids_naissance: rabbit.poids_naissance,
                etat_sante: rabbit.etat_sante,
                vaccinations: vaccinations.map(v => ({
                    id: v.id,
                    type: v.type,
                    nom_personnalise: v.nom_personnalise,
                    date_administration: v.date_administration,
                    dose_numero: v.dose_numero,
                    rappel_prevu: v.rappel_prevu,
                    notes: v.notes
                }))
            });
        });
    } else {
        addRabbitRow();
        addRabbitRow();
        addRabbitRow();
    }
});

// ✅ Validation frontend avant soumission
document.getElementById('naissanceEditForm')?.addEventListener('submit', function(e) {
    let hasError = false;
    
    // Vérifier les dates de vaccination
    document.querySelectorAll('[name$="[vaccins]"][name$="[date]"]').forEach(input => {
        if (input.value && !input.value.match(/^\d{4}-\d{2}-\d{2}$/)) {
            input.style.borderColor = 'var(--accent-red)';
            hasError = true;
        }
    });
    
    // Vérifier "Autre" → nom requis
    document.querySelectorAll('select[name$="[vaccins]"][name$="[type]"]').forEach(select => {
        if (select.value === 'autre') {
            const autreInput = select.parentElement.querySelector('.vaccine-nom-autre');
            if (!autreInput?.value.trim()) {
                autreInput?.style.setProperty('border-color', 'var(--accent-red)', 'important');
                hasError = true;
            }
        }
    });
    
    if (hasError) {
        e.preventDefault();
        showToast('⚠️ Veuillez corriger les erreurs de vaccination', 'error');
        return false;
    }
});

// ✅ Toast notification
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.style.cssText = `position: fixed; bottom: 100px; right: 30px; background: var(--surface); border: 1px solid var(--surface-border); border-left: 4px solid ${type === 'success' ? 'var(--accent-green)' : 'var(--accent-orange)'}; padding: 16px 24px; border-radius: var(--radius-md); box-shadow: var(--shadow-lg); z-index: 9999;`;
    toast.innerHTML = `<span style="color: var(--text-primary);">${message}</span>`;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}
</script>
@endpush
@endsection