@extends('layouts.cuniapp')

@section('title', 'Nouvelle Naissance - CuniApp Élevage')

@section('content')
    <div class="page-header">
        <div>
            <h2 class="page-title"><i class="bi bi-plus-circle"></i> Nouvelle Naissance</h2>
            <div class="breadcrumb">
                <a href="{{ route('dashboard') }}">Tableau de bord</a>
                <span>/</span>
                <a href="{{ route('naissances.index') }}">Naissances</a>
                <span>/</span>
                <span>Nouveau</span>
            </div>
        </div>
    </div>

    {{-- ✅ ERROR DISPLAY WITH FRENCH MESSAGES --}}
    @if ($errors->any())
        <div class="alert-cuni error">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <div>
                <strong>Erreurs de validation</strong>
                <ul style="margin: 8px 0 0 20px; padding: 0;">
                    @foreach ($errors->all() as $error)
                        <li>
                            @if (str_contains($error, 'validation.unique'))
                                ❌ Ce code existe déjà dans la base de données
                            @elseif(str_contains($error, 'validation.required'))
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
            <form action="{{ route('naissances.store') }}" method="POST" id="naissanceForm">
                @csrf

                <div class="settings-grid">
                    <!-- Section: Mise Bas -->
                    <div class="form-section">
                        <h4 class="section-subtitle"><i class="bi bi-calendar-check"></i> Mise Bas</h4>
                        <div class="form-group">
                            <label class="form-label">Mise Bas *</label>
                            <select name="mise_bas_id" class="form-select" required id="miseBasSelect">
                                <option value="">-- Sélectionner une mise bas --</option>
                                @foreach ($misesBas as $mb)
                                    <option value="{{ $mb->id }}"
                                        {{ old('mise_bas_id') == $mb->id || (isset($miseBas) && $miseBas->id == $mb->id) ? 'selected' : '' }}>
                                        {{ $mb->femelle->nom }} ({{ $mb->femelle->code }}) -
                                        {{ $mb->date_mise_bas->format('d/m/Y') }}
                                        @if ($mb->nb_vivant || $mb->nb_mort_ne)
                                            ({{ $mb->nb_vivant }} vivants + {{ $mb->nb_mort_ne }} morts-nés)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            <small style="color: var(--text-tertiary); font-size: 12px; margin-top: 6px; display: block;">
                                <i class="bi bi-info-circle"></i> La date de naissance sera automatiquement celle de la mise
                                bas
                            </small>
                        </div>

                        @if (isset($miseBas))
                            <div class="alert-box info" style="margin-top: 16px;">
                                <i class="bi bi-info-circle-fill"></i>
                                <div>
                                    <strong>Mise bas sélectionnée:</strong> {{ $miseBas->femelle->nom }}<br>
                                    <small>Date: {{ $miseBas->date_mise_bas->format('d/m/Y') }}</small><br>
                                    @if ($miseBas->nb_vivant || $miseBas->nb_mort_ne)
                                        <small>Maximum: <strong>{{ $miseBas->nb_vivant + $miseBas->nb_mort_ne }}
                                                lapereaux</strong>
                                            ({{ $miseBas->nb_vivant }} vivants + {{ $miseBas->nb_mort_ne }}
                                            morts-nés)</small>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Section: Santé Générale de la Portée -->
                    <div class="form-section">
                        <h4 class="section-subtitle"><i class="bi bi-heart-pulse"></i> Santé & Suivi (Portée)</h4>
                        <div class="form-group">
                            <label class="form-label">État de santé général *</label>
                            <select name="etat_sante" class="form-select" required>
                                <option value="Excellent" {{ old('etat_sante') == 'Excellent' ? 'selected' : '' }}>
                                    Excellent</option>
                                <option value="Bon"
                                    {{ old('etat_sante') == 'Bon' || !old('etat_sante') ? 'selected' : '' }}>Bon</option>
                                <option value="Moyen" {{ old('etat_sante') == 'Moyen' ? 'selected' : '' }}>Moyen</option>
                                <option value="Faible" {{ old('etat_sante') == 'Faible' ? 'selected' : '' }}>Faible
                                </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Poids moyen à la naissance (g)</label>
                            <input type="number" step="0.01" name="poids_moyen_naissance" class="form-control"
                                value="{{ old('poids_moyen_naissance') }}" min="0" max="200">
                            <small style="color: var(--text-tertiary); font-size: 12px;">
                                <i class="bi bi-info-circle"></i> Moyenne de la portée (optionnel)
                            </small>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Date de sevrage prévue *</label>
                            <input type="date" name="date_sevrage_prevue" class="form-control"
                                value="{{ old('date_sevrage_prevue') }}" required>
                            <small style="color: var(--text-tertiary); font-size: 12px;">
                                <i class="bi bi-info-circle"></i> Recommandé: 6 semaines après la naissance
                            </small>
                        </div>
                    </div>
                </div>

                <!-- ✅ Lapereaux Section with Individual Fields + Multiple Vaccines -->
                <div class="form-section" style="margin-top: 24px; border: 2px solid var(--primary-subtle);">
                    <h4 class="section-subtitle">
                        <i class="bi bi-collection"></i> Lapereaux (Champs Individuels)
                    </h4>

                    <div class="alert-box warning" style="margin-bottom: 16px;">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <div>
                            <strong>Important:</strong>
                            <ul style="margin: 8px 0 0 16px; padding: 0;">
                                <li>Le sexe ne peut être vérifié qu'après 10 jours</li>
                                <li>Le nombre de lapereaux ne doit pas dépasser celui de la mise bas</li>
                                <li>Chaque lapereau a son propre poids, santé et vaccinations</li>
                                <li>✨ <strong>Les codes sont auto-générés mais modifiables</strong></li>
                            </ul>
                        </div>
                    </div>

                    <!-- ✅ Max Allowed Display -->
                    <div id="maxAllowedDisplay"
                        style="display: none; margin-bottom: 16px; padding: 12px; background: var(--primary-subtle); border-radius: var(--radius);">
                        <strong>Maximum autorisé:</strong> <span id="maxAllowedValue">0</span> lapereaux<br>
                        <small>Actuellement: <span id="currentCount">0</span> lapereaux</small>
                    </div>

                    <div id="rabbitsContainer"></div>

                    <button type="button" class="btn-cuni secondary" onclick="addRabbitRow()" style="margin-top: 12px;">
                        <i class="bi bi-plus-lg"></i> Ajouter un lapereau
                    </button>

                    <div style="margin-top: 16px; font-weight: 600; color: var(--primary);">
                        Total Lapereaux: <span id="totalLapereauxDisplay">0</span>
                    </div>
                </div>

                <div
                    style="margin-top: 32px; display: flex; gap: 12px; padding-top: 24px; border-top: 1px solid var(--surface-border);">
                    <button type="submit" class="btn-cuni primary" id="submitBtn">
                        <i class="bi bi-check-circle"></i> Enregistrer
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
            // ✅ Variables globales
            let rabbitCount = 0;
            let maxAllowed = 0;
            let vaccineFieldCount = {}; // Compteur de vaccins par lapereau

            // ✅ Générer un code auto unique
            function generateAutoCode() {
                const year = new Date().getFullYear();
                return `LAP-${year}-${String(Math.floor(Math.random() * 9000) + 1000)}`;
            }

            // ✅ Ajouter une ligne lapereau avec vaccins multiples
            function addRabbitRow(data = {}) {
                if (maxAllowed > 0 && document.querySelectorAll('.rabbit-row').length >= maxAllowed) {
                    showToast(`Maximum de ${maxAllowed} lapereaux atteint`, 'warning');
                    return;
                }

                rabbitCount++;
                vaccineFieldCount[rabbitCount] = 0;

                const container = document.getElementById('rabbitsContainer');
                const row = document.createElement('div');
                row.className = 'rabbit-row';
                row.style.cssText =
                    'display: grid; grid-template-columns: 1fr 1fr 1fr 1fr 1fr auto; gap: 10px; margin-bottom: 10px; padding: 10px; background: var(--surface-alt); border-radius: var(--radius); align-items: end;';

                const autoCode = data.code || generateAutoCode();

                row.innerHTML = `
        <!-- Code -->
        <div>
            <label class="form-label" style="font-size: 12px;">Code *</label>
            <input type="text" name="rabbits[${rabbitCount}][code]" class="form-control rabbit-code" 
                   value="${autoCode}" placeholder="LAP-2026-0001" 
                   data-check-url="{{ route('lapins.check-code') }}">
            <small style="color: var(--primary); font-size: 10px;">✨ Modifiable</small>
            <div class="code-validation" style="font-size: 10px; margin-top: 2px;"></div>
        </div>

        <!-- Nom -->
        <div>
            <label class="form-label" style="font-size: 12px;">Nom</label>
            <input type="text" name="rabbits[${rabbitCount}][nom]" class="form-control" 
                   value="${data.nom || ''}" placeholder="Ex: Toto">
        </div>

        <!-- Sexe -->
        <div>
            <label class="form-label" style="font-size: 12px;">Sexe</label>
            <select name="rabbits[${rabbitCount}][sex]" class="form-select">
                <option value="">-- À vérifier (10j) --</option>
                <option value="male" ${data.sex === 'male' ? 'selected' : ''}>Mâle</option>
                <option value="female" ${data.sex === 'female' ? 'selected' : ''}>Femelle</option>
            </select>
        </div>

        <!-- État -->
        <div>
            <label class="form-label" style="font-size: 12px;">État *</label>
            <select name="rabbits[${rabbitCount}][etat]" class="form-select rabbit-etat" required>
                <option value="vivant" ${data.etat === 'vivant' ? 'selected' : ''}>Vivant</option>
                <option value="mort" ${data.etat === 'mort' ? 'selected' : ''}>Mort-né</option>
                <option value="vendu" ${data.etat === 'vendu' ? 'selected' : ''}>Vendu</option>
            </select>
        </div>

        <!-- Poids -->
        <div>
            <label class="form-label" style="font-size: 12px;">Poids (g)</label>
            <input type="number" step="0.01" name="rabbits[${rabbitCount}][poids_naissance]" 
                   class="form-control" value="${data.poids_naissance || ''}" 
                   min="0" max="200" placeholder="50-80">
        </div>

        <!-- Santé -->
        <div>
            <label class="form-label" style="font-size: 12px;">Santé</label>
            <select name="rabbits[${rabbitCount}][etat_sante]" class="form-select">
                <option value="Excellent" ${data.etat_sante === 'Excellent' ? 'selected' : ''}>Excellent</option>
                <option value="Bon" ${data.etat_sante === 'Bon' || !data.etat_sante ? 'selected' : ''}>Bon</option>
                <option value="Moyen" ${data.etat_sante === 'Moyen' ? 'selected' : ''}>Moyen</option>
                <option value="Faible" ${data.etat_sante === 'Faible' ? 'selected' : ''}>Faible</option>
            </select>
        </div>

        <!-- ✅ VACCINATIONS MULTIPLES (pleine largeur) -->
<div style="grid-column: 1 / -1;">
    <label class="form-label" style="font-size: 11px; margin-bottom: 4px;">
        <i class="bi bi-shield-check"></i> Vaccinations
    </label>
    
    <!-- Container pour les vaccins -->
    <div id="vaccinesContainer_${rabbitCount}" style="margin-bottom: 6px;"></div>
    
    <!-- Bouton ajouter un vaccin - plus compact -->
    <button type="button" class="btn-cuni secondary sm" 
            onclick="addVaccineField(${rabbitCount})" 
            style="width: 100%; font-size: 10px; padding: 4px 8px; margin-bottom: 2px;">
        <i class="bi bi-plus"></i> Ajouter un vaccin
    </button>
    
    <small style="color: var(--text-tertiary); font-size: 9px; display: block;">
        <i class="bi bi-info-circle"></i> Admin + rappel optionnel
    </small>
</div>

        <!-- Bouton supprimer lapereau -->
        <button type="button" class="btn-cuni sm danger" onclick="removeRabbitRow(this)" 
                style="margin-bottom: 0; align-self: center;">
            <i class="bi bi-trash"></i>
        </button>
    `;

                container.appendChild(row);
                updateTotalLapereaux();
                setupCodeValidation(row.querySelector('.rabbit-code'));

                // Initialiser les affichages de nom de vaccin si des vaccins existent
                if (data.vaccins && Array.isArray(data.vaccins)) {
                    data.vaccins.forEach((_, i) => {
                        const select = row.querySelector(
                            `select[name="rabbits[${rabbitCount}][vaccins][${i+1}][type]"]`);
                        if (select) updateVaccineName(select, rabbitCount, i + 1);
                    });
                }
            }

            // ✅ Construire un champ vaccin (pour affichage initial ou ajout dynamique)
            // ✅ Construire un champ vaccin - VERSION COMPACTE
            function buildVaccineField(rabbitIndex, vaccineIndex, data = {}) {
                const type = data.type || '';
                const nomAutre = data.nom_autre || '';
                const date = data.date ? String(data.date).split(/[T\s]/)[0] : '';
                const dose = data.dose || 1;
                const rappel = data.rappel ? String(data.rappel).split(/[T\s]/)[0] : '';
                const notes = data.notes || '';

                return `
        <div class="vaccine-field" style="background: var(--surface-alt); padding: 8px 10px; border-radius: var(--radius); margin-bottom: 6px; border: 1px solid var(--surface-border);">
            <!-- Header: Numéro + Supprimer -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                <strong style="font-size: 10px; color: var(--text-primary);">Vaccin #${vaccineIndex}</strong>
                <button type="button" class="btn-cuni sm danger" onclick="removeVaccineField(this)" style="padding: 2px 6px; font-size: 9px; min-width: auto; height: auto;">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
            
            <!-- Ligne 1: Type + Nom Autre -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 6px; margin-bottom: 4px;">
                <select name="rabbits[${rabbitIndex}][vaccins][${vaccineIndex}][type]" 
                        class="form-select" 
                        style="font-size: 10px; padding: 5px 8px;"
                        onchange="updateVaccineName(this, ${rabbitIndex}, ${vaccineIndex})">
                    <option value="">-- Type --</option>
                    <option value="myxomatose" ${type === 'myxomatose' ? 'selected' : ''}>Myxomatose</option>
                    <option value="vhd" ${type === 'vhd' ? 'selected' : ''}>VHD</option>
                    <option value="pasteurellose" ${type === 'pasteurellose' ? 'selected' : ''}>Pasteurellose</option>
                    <option value="coccidiose" ${type === 'coccidiose' ? 'selected' : ''}>Coccidiose</option>
                    <option value="autre" ${type === 'autre' ? 'selected' : ''}>Autre</option>
                </select>
                
                <input type="text" 
                       name="rabbits[${rabbitIndex}][vaccins][${vaccineIndex}][nom_autre]" 
                       class="form-control vaccine-nom-autre"
                       placeholder="Nom si Autre" 
                       value="${nomAutre}"
                       style="font-size: 10px; padding: 5px 8px; display: ${type === 'autre' ? 'block' : 'none'};">
            </div>
            
            <!-- Ligne 2: Dates avec LABELS CLAIRS -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 6px; margin-bottom: 4px;">
                <div>
                    <label style="font-size: 9px; color: var(--text-tertiary); display: block; margin-bottom: 2px;">📅 Administration</label>
                    <input type="date" 
                           name="rabbits[${rabbitIndex}][vaccins][${vaccineIndex}][date]" 
                           class="form-control" 
                           value="${date}"
                           style="font-size: 10px; padding: 5px 8px;"
                           required>
                </div>
                <div>
                    <label style="font-size: 9px; color: var(--text-tertiary); display: block; margin-bottom: 2px;">🔁 Rappel (optionnel)</label>
                    <input type="date" 
                           name="rabbits[${rabbitIndex}][vaccins][${vaccineIndex}][rappel]" 
                           class="form-control" 
                           value="${rappel}"
                           placeholder="Optionnel"
                           style="font-size: 10px; padding: 5px 8px;">
                </div>
            </div>
            
            <!-- Ligne 3: Dose + Notes (compact) -->
            <div style="display: grid; grid-template-columns: 80px 1fr; gap: 6px;">
                <div>
                    <label style="font-size: 9px; color: var(--text-tertiary); display: block; margin-bottom: 2px;">Dose</label>
                    <input type="number" 
                           name="rabbits[${rabbitIndex}][vaccins][${vaccineIndex}][dose]" 
                           class="form-control" 
                           value="${dose}"
                           min="1" max="10"
                           style="font-size: 10px; padding: 5px 8px; text-align: center;">
                </div>
                <div>
                    <label style="font-size: 9px; color: var(--text-tertiary); display: block; margin-bottom: 2px;">Notes</label>
                    <input type="text"
                           name="rabbits[${rabbitIndex}][vaccins][${vaccineIndex}][notes]" 
                           class="form-control" 
                           value="${notes}"
                           placeholder="Lot, vétérinaire..."
                           style="font-size: 10px; padding: 5px 8px;">
                </div>
            </div>
            
            <!-- Affichage du nom du vaccin (en bas) -->
            <div id="vaccineNameDisplay_${rabbitIndex}_${vaccineIndex}" 
                 style="display:${type ? 'block' : 'none'}; font-size:9px; font-weight:600; color:var(--accent-green); margin-top:4px; padding:2px 6px; background:rgba(16,185,129,0.1); border-radius:3px; text-align:center;">
                ✅ ${getVaccineName(type, nomAutre)}
            </div>
        </div>
    `;
            }

            // ✅ Ajouter un champ vaccin dynamique
            function addVaccineField(rabbitIndex) {
                if (!vaccineFieldCount[rabbitIndex]) vaccineFieldCount[rabbitIndex] = 0;
                vaccineFieldCount[rabbitIndex]++;

                const container = document.getElementById(`vaccinesContainer_${rabbitIndex}`);
                const vaccineIndex = vaccineFieldCount[rabbitIndex];

                container.insertAdjacentHTML('beforeend', buildVaccineField(rabbitIndex, vaccineIndex));
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
                    const names = {
                        myxomatose: 'Myxomatose',
                        vhd: 'VHD (Hémorragique)',
                        pasteurellose: 'Pasteurellose',
                        coccidiose: 'Coccidiose'
                    };
                    name = names[select.value] || 'Vaccin';
                } else {
                    autreInput.style.display = 'none';
                }

                if (nameDiv) {
                    nameDiv.textContent = name ? `✅ ${name}` : '';
                    nameDiv.style.display = name ? 'block' : 'none';
                }
            }

            // ✅ Obtenir le nom lisible d'un vaccin
            function getVaccineName(type, nomAutre) {
                if (type === 'autre' && nomAutre) return nomAutre;
                const names = {
                    'myxomatose': 'Myxomatose',
                    'vhd': 'VHD (Hémorragique)',
                    'pasteurellose': 'Pasteurellose',
                    'coccidiose': 'Coccidiose'
                };
                return names[type] || 'Vaccin';
            }

            // ✅ Validation AJAX du code lapereau
            function setupCodeValidation(codeInput) {
                let validationTimeout;
                codeInput.addEventListener('input', function() {
                    clearTimeout(validationTimeout);
                    const validationDiv = this.parentElement.querySelector('.code-validation');
                    if (this.value.length < 3) return;

                    validationTimeout = setTimeout(() => {
                        fetch(`${this.dataset.checkUrl}?code=${encodeURIComponent(this.value)}`)
                            .then(r => r.json())
                            .then(d => {
                                if (!d.available) {
                                    validationDiv.innerHTML =
                                        '<span style="color:var(--accent-red);">❌ Existe déjà</span>';
                                    this.style.borderColor = 'var(--accent-red)';
                                } else {
                                    validationDiv.innerHTML =
                                        '<span style="color:var(--accent-green);">✅ Disponible</span>';
                                    this.style.borderColor = 'var(--accent-green)';
                                }
                            });
                    }, 500);
                });
            }

            // ✅ Supprimer une ligne lapereau
            function removeRabbitRow(btn) {
                btn.closest('.rabbit-row').remove();
                updateTotalLapereaux();
            }

            // ✅ Mettre à jour le compteur total
            function updateTotalLapereaux() {
                const count = document.querySelectorAll('.rabbit-row').length;
                document.getElementById('totalLapereauxDisplay').textContent = count;
                document.getElementById('currentCount').textContent = count;

                if (maxAllowed > 0) {
                    const display = document.getElementById('maxAllowedDisplay');
                    display.style.background = count >= maxAllowed ? 'rgba(239, 68, 68, 0.1)' : 'var(--primary-subtle)';
                }
            }

            // ✅ Charger le max autorisé depuis la sélection de mise bas
            document.getElementById('miseBasSelect')?.addEventListener('change', function() {
                const match = this.options[this.selectedIndex].text.match(/\((\d+) vivants \+ (\d+) morts-nés\)/);
                if (match) {
                    maxAllowed = parseInt(match[1]) + parseInt(match[2]);
                    document.getElementById('maxAllowedValue').textContent = maxAllowed;
                    document.getElementById('maxAllowedDisplay').style.display = 'block';
                } else {
                    maxAllowed = 0;
                    document.getElementById('maxAllowedDisplay').style.display = 'none';
                }
                updateTotalLapereaux();
            });

            // ✅ Initialisation au chargement
            window.addEventListener('DOMContentLoaded', () => {
                addRabbitRow();
                addRabbitRow();
                addRabbitRow();
                const sel = document.getElementById('miseBasSelect');
                if (sel?.value) sel.dispatchEvent(new Event('change'));
            });

            // ✅ Toast notification
            function showToast(message, type = 'info') {
                const toast = document.createElement('div');
                toast.style.cssText =
                    `position: fixed; bottom: 100px; right: 30px; background: var(--surface); border: 1px solid var(--surface-border); border-left: 4px solid ${type === 'success' ? 'var(--accent-green)' : 'var(--accent-orange)'}; padding: 16px 24px; border-radius: var(--radius-md); box-shadow: var(--shadow-lg); z-index: 9999;`;
                toast.innerHTML = `<span style="color: var(--text-primary);">${message}</span>`;
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 3000);
            }

            // ✅ Validation frontend avant soumission
            document.getElementById('naissanceForm')?.addEventListener('submit', function(e) {
                let hasError = false;

                // Vérifier les codes uniques
                document.querySelectorAll('.rabbit-code').forEach(input => {
                    if (input.style.borderColor === 'var(--accent-red)') hasError = true;
                });

                // Vérifier les vaccins: chaque vaccin doit avoir une date
                document.querySelectorAll('[name$="[vaccins]"][name$="[date]"]').forEach(input => {
                    if (input.value && !input.value.match(/^\d{4}-\d{2}-\d{2}$/)) {
                        input.style.borderColor = 'var(--accent-red)';
                        hasError = true;
                    }
                });

                // Vérifier "Autre": si type=autre, nom_autre est requis
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
                    showToast('⚠️ Veuillez corriger les erreurs de validation', 'error');
                    return false;
                }

                // ✅ Convertir les checkboxes vaccined en hidden inputs pour envoi correct
                document.querySelectorAll('input[name*="[vaccined]"]').forEach(checkbox => {
                    const hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = checkbox.name;
                    hidden.value = checkbox.checked ? '1' : '0';
                    checkbox.parentElement.appendChild(hidden);
                    checkbox.disabled = true; // Éviter l'envoi en double
                });
            });
        </script>
    @endpush
@endsection
