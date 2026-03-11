@extends('layouts.cuniapp')
@section('title', 'Vérification email - CuniApp Élevage')

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

        <!-- Verify Email Card -->
        <div class="auth-container">
            <div class="auth-forms" style="padding: 32px; text-align: center;">
                
                @if(session('status') == 'verification-link-sent')
                    <div class="alert-box success">
                        <i class="bi bi-check-circle-fill"></i>
                        <div>Un nouveau lien de vérification a été envoyé à votre adresse email.</div>
                    </div>
                @endif

                <div style="margin-bottom: 24px;">
                    <div style="width: 80px; height: 80px; background: rgba(37, 99, 235, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                        <i class="bi bi-envelope-check" style="font-size: 32px; color: var(--primary);"></i>
                    </div>
                    <h2 class="form-title">Vérifiez votre email</h2>
                    <p class="form-subtitle" style="margin-top: 12px;">
                        Merci de vous être inscrit ! Avant de commencer, veuillez vérifier votre adresse email en cliquant sur le lien que nous venons de vous envoyer.
                    </p>
                </div>

                @if(auth()->user() && !auth()->user()->hasVerifiedEmail())
                <form method="POST" action="{{ route('verification.send') }}" style="margin-bottom: 16px;">
                    @csrf
                    <button type="submit" class="btn-submit">
                        <span>Renvoyer l'email de vérification</span>
                        <i class="bi bi-send"></i>
                    </button>
                </form>
                @endif

                <form method="POST" action="{{ route('logout') }}" style="margin-bottom: 16px;">
                    @csrf
                    <button type="submit" class="btn-submit" style="background: linear-gradient(135deg, var(--gray-600) 0%, var(--gray-700) 100%);">
                        <span>Se déconnecter</span>
                        <i class="bi bi-box-arrow-right"></i>
                    </button>
                </form>

                <p style="font-size: 13px; color: var(--gray-500);">
                    Vous n'avez pas reçu l'email ? 
                    <a href="#" onclick="event.preventDefault(); document.querySelector('form[action=\"{{ route('verification.send') }}\"]').submit();" 
                       style="color: var(--primary); font-weight: 500;">
                        Cliquez ici pour en renvoyer un
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection