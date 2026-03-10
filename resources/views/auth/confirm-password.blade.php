@extends('layouts.cuniapp')
@section('title', 'Confirmer le mot de passe - CuniApp Élevage')

@section('content')
<div class="welcome-container">
    <div class="welcome-content" style="grid-template-columns: 1fr; max-width: 500px;">
        
        <!-- Brand Section -->
        <div class="brand-section" style="text-align: center; margin-bottom: 24px;">
            <div class="logo-container" style="margin: 0 auto 16px;">
                <svg viewBox="0 0 40 40" fill="none">
                    <path d="M20 5L35 15V25L20 35L5 25V15L20 5Z" fill="white"/>
                    <path d="M20 12L28 17V23L20 28L12 23V17L20 12Z" fill="rgba(255,255,255,0.8)"/>
                </svg>
            </div>
            <h1 class="brand-title" style="font-size: 28px;">CuniApp <span>Élevage</span></h1>
        </div>

        <!-- Confirm Password Form -->
        <div class="auth-container">
            <div class="auth-forms" style="padding: 32px;">
                
                @if($errors->any())
                    <div class="alert-box error">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <div>
                            <strong>Erreur</strong>
                            <ul class="validation-summary-list">
                                @foreach($errors->all() as $error)
                                    <li><i class="bi bi-x-circle-fill"></i><span>{{ $error }}</span></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <div style="margin-bottom: 24px; text-align: center;">
                    <div style="width: 80px; height: 80px; background: rgba(239, 68, 68, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                        <i class="bi bi-shield-lock" style="font-size: 32px; color: var(--accent-red);"></i>
                    </div>
                    <h2 class="form-title">Zone sécurisée</h2>
                    <p class="form-subtitle" style="margin-top: 12px;">
                        Veuillez confirmer votre mot de passe pour accéder à cette zone sensible.
                    </p>
                </div>

                <form method="POST" action="{{ route('password.confirm') }}">
                    @csrf
                    
                    <div class="form-group">
                        <label class="form-label">Mot de passe</label>
                        <div class="form-input-wrapper">
                            <input type="password" name="password" class="form-input @error('password') error @enderror" 
                                   placeholder="••••••••" required autofocus autocomplete="current-password">
                            <i class="bi bi-lock"></i>
                        </div>
                        @error('password')
                            <div class="validation-message error">
                                <i class="bi bi-exclamation-circle-fill"></i><span>{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    <button type="submit" class="btn-submit" style="margin-bottom: 16px;">
                        <span>Confirmer</span>
                        <i class="bi bi-check-circle"></i>
                    </button>

                    <div style="text-align: center;">
                        @if(Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="forgot-password">
                                Mot de passe oublié ?
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection