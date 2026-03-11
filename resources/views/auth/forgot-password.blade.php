<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Réinitialisation du mot de passe - CuniApp Élevage</title>

    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            /* Couleurs principales */
            --primary: #2563EB;
            --primary-dark: #1D4ED8;
            --primary-subtle: rgba(37, 99, 235, 0.1);
            --accent-cyan: #06B6D4;
            --accent-green: #10B981;
            --accent-red: #EF4444;

            /* Couleurs neutres */
            --white: #FFFFFF;
            --surface: #FFFFFF;
            --gray-50: #F9FAFB;
            --gray-200: #E5E7EB;
            --gray-400: #9CA3AF;
            --gray-500: #6B7280;
            --gray-700: #374151;
            --gray-800: #1F2937;

            /* Espacements & bordures */
            --radius: 8px;
            --radius-lg: 12px;
            --radius-xl: 16px;
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.5;
            color: var(--gray-800);
            background: linear-gradient(135deg, #1e3a5f 0%, #2563EB 50%, #06B6D4 100%);
            min-height: 100vh;
        }

        .welcome-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .welcome-content {
            display: grid;
            gap: 40px;
            max-width: 500px;
            width: 100%;
            align-items: center;
        }

        /* Brand Section */
        .brand-section {
            text-align: center;
            margin-bottom: 24px;
            color: var(--white);
        }

        .logo-container {
            width: 60px;
            height: 60px;
            margin: 0 auto 16px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent-cyan) 100%);
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--shadow-lg);
        }

        .logo-container svg {
            width: 36px;
            height: 36px;
        }

        .brand-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--white);
        }

        .brand-title span {
            background: linear-gradient(135deg, var(--accent-cyan) 0%, var(--accent-green) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Auth Container */
        .auth-container {
            background: var(--surface);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
        }

        .auth-forms {
            padding: 32px;
        }

        /* Form Typography */
        .form-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--gray-800);
            margin-bottom: 8px;
            text-align: center;
        }

        .form-subtitle {
            font-size: 14px;
            color: var(--gray-500);
            text-align: center;
            margin-bottom: 24px;
        }

        /* Form Elements */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: var(--gray-700);
            margin-bottom: 8px;
        }

        .form-input-wrapper {
            position: relative;
        }

        .form-input-wrapper i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-400);
            font-size: 16px;
            pointer-events: none;
        }

        .form-input {
            width: 100%;
            padding: 12px 14px 12px 44px;
            font-size: 14px;
            border: 2px solid var(--gray-200);
            border-radius: var(--radius);
            background: var(--white);
            color: var(--gray-800);
            transition: all 0.3s ease;
        }

        .form-input::placeholder {
            color: var(--gray-400);
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px var(--primary-subtle);
        }

        .form-input.error {
            border-color: var(--accent-red);
        }

        /* Validation Messages */
        .validation-message {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            margin-top: 6px;
            padding: 8px 12px;
            border-radius: var(--radius);
        }

        .validation-message.error {
            background: rgba(239, 68, 68, 0.1);
            color: var(--accent-red);
        }

        /* Alert Boxes */
        .alert-box {
            padding: 14px 18px;
            border-radius: var(--radius);
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            font-size: 14px;
        }

        .alert-box.success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: var(--accent-green);
        }

        .alert-box.error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: var(--accent-red);
        }

        .validation-summary-list {
            list-style: none;
            padding-left: 0;
            margin: 0;
        }

        .validation-summary-list li {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            margin-bottom: 8px;
            font-size: 13px;
        }

        .validation-summary-list li:last-child {
            margin-bottom: 0;
        }

        .validation-summary-list li i {
            color: var(--accent-red);
            font-size: 14px;
            margin-top: 2px;
            flex-shrink: 0;
        }

        /* Buttons */
        .btn-submit {
            width: 100%;
            padding: 14px 24px;
            font-size: 14px;
            font-weight: 600;
            color: var(--white);
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border: none;
            border-radius: var(--radius);
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-submit:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(37, 99, 235, 0.4);
        }

        .btn-submit:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .btn-submit .btn-loading {
            display: none;
        }

        .btn-submit.loading .btn-text {
            display: none;
        }

        .btn-submit.loading .btn-loading {
            display: inline;
        }

        .btn-submit.loading i {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        /* Links */
        .forgot-password {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--primary);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .forgot-password:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .auth-forms {
                padding: 24px;
            }

            .brand-title {
                font-size: 24px;
            }

            .form-title {
                font-size: 20px;
            }
        }

        /* Accessibility */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }

        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }
    </style>
</head>

<body>
    <div class="welcome-container">
        <div class="welcome-content">

            <!-- Brand Section -->
            <div class="brand-section">
                <div class="logo-container">
                    <svg viewBox="0 0 40 40" fill="none" aria-hidden="true">
                        <path d="M20 5L35 15V25L20 35L5 25V15L20 5Z" fill="white" />
                        <path d="M20 12L28 17V23L20 28L12 23V17L20 12Z" fill="rgba(255,255,255,0.8)" />
                    </svg>
                </div>
                <h1 class="brand-title">CuniApp <span>Élevage</span></h1>
            </div>

            <!-- Forgot Password Form -->
            <div class="auth-container">
                <div class="auth-forms">

                    {{-- Success Message avec traduction Laravel --}}
                    @if (session('status'))
                        <div class="alert-box success" role="alert" aria-live="polite">
                            <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                            <div>{{ __(session('status')) }}</div>
                        </div>
                    @endif

                    {{-- Error Messages avec traduction --}}
                    @if ($errors->any())
                        <div class="alert-box error" role="alert" aria-live="assertive">
                            <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
                            <div>
                                <strong>Erreur</strong>
                                <ul class="validation-summary-list">
                                    @foreach ($errors->all() as $error)
                                        <li>
                                            <i class="bi bi-x-circle-fill" aria-hidden="true"></i>
                                            <span>{{ $error }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <h2 class="form-title">Mot de passe oublié ?</h2>
                    <p class="form-subtitle">Entrez votre adresse email pour recevoir un lien de réinitialisation</p>

                    <form method="POST" action="{{ route('password.email') }}" id="forgotPasswordForm">
                        @csrf

                        <div class="form-group">
                            <label class="form-label" for="email">Adresse email</label>
                            <div class="form-input-wrapper">
                                <i class="bi bi-envelope" aria-hidden="true"></i>
                                <input type="email" id="email" name="email"
                                    class="form-input @error('email') error @enderror" placeholder="votre@email.com"
                                    required autofocus autocomplete="email" value="{{ old('email') }}"
                                    aria-describedby="@error('email') email-error @enderror">
                            </div>
                            @error('email')
                                <div class="validation-message error" id="email-error" role="alert">
                                    <i class="bi bi-exclamation-circle-fill" aria-hidden="true"></i>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>

                        <button type="submit" class="btn-submit" id="submitBtn">
                            <span class="btn-text">Envoyer le lien</span>
                            <span class="btn-loading">Envoi en cours...</span>
                            <i class="bi bi-arrow-right"></i>
                        </button>

                        <div style="text-align: center; margin-top: 16px;">
                            <a href="{{ route('login') }}" class="forgot-password">
                                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                                Retour à la connexion
                            </a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript pour l'UX -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('forgotPasswordForm');
            const submitBtn = document.getElementById('submitBtn');
            const emailInput = document.getElementById('email');

            // Gestion du chargement du bouton
            form.addEventListener('submit', function() {
                submitBtn.classList.add('loading');
                submitBtn.disabled = true;
            });

            // Focus sur le champ email s'il y a une erreur
            @if ($errors->has('email'))
                emailInput.focus();
            @endif

            // Amélioration de l'accessibilité : annonces ARIA dynamiques
            const alertBoxes = document.querySelectorAll('.alert-box');
            alertBoxes.forEach(box => {
                if (!box.hasAttribute('role')) {
                    box.setAttribute('role', 'alert');
                }
            });
        });
    </script>
</body>

</html>
