{{-- resources/views/sales/partials/rabbit-grid.blade.php --}}

@forelse($rabbits as $rabbit)
    @php
        $isSold = in_array($rabbit->id, $soldIds ?? []);
        $category = str_replace('s', '', $type); // males -> male, females -> female, lapereaux -> lapereau
    @endphp
    <label class="rabbit-card" 
           style="display: flex; flex-direction: column; gap: 8px; padding: 12px; 
                  background: var(--surface-alt); border: 1px solid var(--surface-border); 
                  border-radius: var(--radius); cursor: pointer; transition: all 0.2s ease;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <input type="checkbox" 
                   name="selected_{{ $type }}[]" 
                   value="{{ $rabbit->id }}" 
                   class="rabbit-checkbox" 
                   data-category="{{ $category }}"
                   data-code="{{ $rabbit->code }}" 
                   data-name="{{ $rabbit->nom }}"
                   {{ $isSold ? 'checked' : '' }}
                   onchange="handleRabbitSelection('{{ $category }}', {{ $rabbit->id }})">
            <div style="flex: 1;">
                <div style="font-weight: 600;">{{ $rabbit->nom ?? 'Sans nom' }}</div>
                <div style="font-size: 12px; color: var(--text-tertiary);">
                    {{ $rabbit->code }} • {{ $rabbit->race ?? 'Non spécifié' }}
                    @if($category === 'lapereau' && $rabbit->naissance?->miseBas?->femelle)
                        • {{ $rabbit->naissance->miseBas->femelle->nom }}
                    @endif
                </div>
            </div>
        </div>
        
        {{-- Price Input Container --}}
        <div class="price-input-container" 
             id="price-{{ $category }}-{{ $rabbit->id }}" 
             style="display: {{ $isSold ? 'block' : 'none' }}; margin-top: 8px;">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                <label style="font-size: 11px; color: var(--text-secondary); flex: 1;">
                    Prix individuel (FCFA)
                </label>
                <button type="button" 
                        class="btn-reset-price" 
                        onclick="resetToGlobalPrice('{{ $category }}', {{ $rabbit->id }})"
                        title="Réinitialiser au prix global"
                        style="background: var(--primary-subtle); border: none; 
                               border-radius: 4px; padding: 4px 8px; font-size: 10px; 
                               color: var(--primary); cursor: pointer;">
                    <i class="bi bi-arrow-counterclockwise"></i> Prix global
                </button>
            </div>
            <input type="number" 
                   name="{{ $category }}_prices[]" 
                   class="form-control rabbit-price" 
                   data-category="{{ $category }}" 
                   data-rabbit-id="{{ $rabbit->id }}" 
                   placeholder="0" 
                   min="0" 
                   step="100"
                   onchange="calculateTotalAmount(); markPriceAsCustom('{{ $category }}', {{ $rabbit->id }})"
                   style="padding: 8px; font-size: 13px;">
            <div class="price-indicator" 
                 id="price-indicator-{{ $category }}-{{ $rabbit->id }}" 
                 style="font-size: 10px; color: var(--accent-green); 
                        margin-top: 4px; display: none;">
                <i class="bi bi-check-circle"></i> Prix global appliqué
            </div>
        </div>
    </label>
@empty
    <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: var(--text-tertiary);">
        <i class="bi bi-inbox" style="font-size: 48px; opacity: 0.5;"></i>
        <p>Aucun {{ $type }} trouvé</p>
    </div>
@endforelse

{{-- Pagination Controls --}}
@if($rabbits->hasMorePages())
    <div style="grid-column: 1 / -1; display: flex; justify-content: center; gap: 8px; margin-top: 16px;">
        <button type="button" 
                class="btn-cuni secondary sm load-more-btn"
                data-type="{{ $type }}"
                data-page="{{ $rabbits->currentPage() + 1 }}"
                onclick="loadMoreRabbits(this)">
            <i class="bi bi-plus-lg"></i> Charger plus ({{ $rabbits->lastPage() - $rabbits->currentPage() }} pages restantes)
        </button>
    </div>
@endif