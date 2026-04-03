<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finaliser votre inscription — {{ config('app.name', 'CuniApp') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --primary: #2563EB;
            --primary-dark: #1D4ED8;
            --primary-subtle: #EFF6FF;
            --accent-cyan: #06B6D4;
            --accent-green: #10B981;
            --accent-red: #EF4444;
            --accent-orange: #F59E0B;
            --white: #FFFFFF;
            --gray-100: #F3F4F6;
            --gray-200: #E5E7EB;
            --gray-400: #9CA3AF;
            --gray-500: #6B7280;
            --gray-700: #374151;
            --gray-800: #1F2937;
            --radius: 8px;
            --radius-md: 12px;
            --radius-xl: 20px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1e3a5f 0%, #2563EB 50%, #06B6D4 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        /* Floating particles */
        .particle {
            position: fixed;
            border-radius: 50%;
            background: rgba(255,255,255,0.08);
            animation: float 20s infinite ease-in-out;
            pointer-events: none;
        }
        .particle:nth-child(1) { width: 300px; height: 300px; top: -100px; left: -100px; }
        .particle:nth-child(2) { width: 200px; height: 200px; top: 60%; right: -60px; animation-delay: -7s; }
        .particle:nth-child(3) { width: 150px; height: 150px; bottom: -60px; left: 35%; animation-delay: -14s; }
        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(20px, -25px) rotate(120deg); }
            66% { transform: translate(-15px, 15px) rotate(240deg); }
        }

        .page-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 520px;
        }

        /* Logo */
        .logo-row {
            text-align: center;
            margin-bottom: 28px;
            animation: slideDown 0.6s ease-out;
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .logo-bubble {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, var(--primary), var(--accent-cyan));
            border-radius: var(--radius-md);
            margin-bottom: 12px;
            box-shadow: 0 8px 24px rgba(37,99,235,0.35);
        }
        .logo-bubble i { font-size: 32px; color: white; }
        .logo-name {
            font-size: 22px;
            font-weight: 700;
            color: white;
            letter-spacing: -0.02em;
        }

        /* Card */
        .card {
            background: white;
            border-radius: var(--radius-xl);
            box-shadow: 0 20px 60px -10px rgba(0,0,0,0.3);
            overflow: hidden;
            animation: slideUp 0.7s ease-out;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .card-header {
            background: linear-gradient(135deg, #F0F9FF, #E0F2FE);
            padding: 28px 36px 24px;
            border-bottom: 1px solid var(--gray-200);
        }
        .google-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: 999px;
            padding: 6px 14px;
            font-size: 13px;
            font-weight: 500;
            color: var(--gray-700);
            margin-bottom: 16px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        }
        .google-badge svg { width: 16px; height: 16px; flex-shrink: 0; }
        .card-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--gray-800);
            margin-bottom: 6px;
        }
        .card-subtitle {
            font-size: 14px;
            color: var(--gray-500);
            line-height: 1.6;
        }

        /* User preview */
        .user-preview {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 16px;
            padding: 12px 16px;
            background: white;
            border-radius: var(--radius-md);
            border: 1px solid var(--gray-200);
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--accent-cyan));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 16px;
            flex-shrink: 0;
        }
        .user-info-name { font-size: 14px; font-weight: 600; color: var(--gray-800); }
        .user-info-email { font-size: 12px; color: var(--gray-500); }

        .card-body { padding: 32px 36px; }

        /* Step indicator */
        .step-indicator {
            display: flex;
            align-items: center;
            gap: 0;
            margin-bottom: 28px;
        }
        .step {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            font-weight: 600;
        }
        .step-dot {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
        }
        .step.done .step-dot { background: var(--accent-green); color: white; }
        .step.done .step-label { color: var(--accent-green); }
        .step.active .step-dot { background: var(--primary); color: white; }
        .step.active .step-label { color: var(--primary); }
        .step-line { flex: 1; height: 2px; background: var(--gray-200); margin: 0 8px; }
        .step-line.done { background: var(--accent-green); }

        /* Form */
        .form-group { margin-bottom: 20px; }
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: var(--gray-700);
            margin-bottom: 8px;
        }
        .input-wrapper { position: relative; }
        .input-wrapper i {
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
            background: white;
            color: var(--gray-800);
            font-family: inherit;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        textarea.form-input {
            padding-top: 14px;
            resize: vertical;
            min-height: 100px;
        }
        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px var(--primary-subtle);
        }
        .form-input::placeholder { color: var(--gray-400); }
        .form-input.is-invalid { border-color: var(--accent-red); }

        /* Textarea icon alignment */
        .input-wrapper.textarea-wrapper i { top: 18px; transform: none; }

        .error-msg {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: var(--accent-red);
            margin-top: 6px;
            padding: 7px 12px;
            background: rgba(239,68,68,0.08);
            border-radius: var(--radius);
            border: 1px solid rgba(239,68,68,0.2);
        }

        /* Terms */
        .terms-box {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 14px 16px;
            background: var(--gray-100);
            border-radius: var(--radius-md);
            border: 1px solid var(--gray-200);
            margin-bottom: 24px;
            cursor: pointer;
        }
        .terms-box input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--primary);
            cursor: pointer;
            flex-shrink: 0;
            margin-top: 2px;
        }
        .terms-text { font-size: 13px; color: var(--gray-700); line-height: 1.5; }
        .terms-text a { color: var(--primary); font-weight: 500; text-decoration: none; }
        .terms-text a:hover { text-decoration: underline; }

        /* Alert */
        .alert {
            border-radius: var(--radius);
            padding: 12px 16px;
            font-size: 13px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 20px;
        }
        .alert-error { background: rgba(239,68,68,0.08); color: var(--accent-red); border: 1px solid rgba(239,68,68,0.2); }
        .alert i { font-size: 16px; flex-shrink: 0; margin-top: 1px; }

        /* Info box */
        .trial-info {
            background: linear-gradient(135deg, rgba(16,185,129,0.08), rgba(6,182,212,0.08));
            border: 1px solid rgba(16,185,129,0.2);
            border-radius: var(--radius-md);
            padding: 14px 16px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .trial-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--accent-green), var(--accent-cyan));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
            flex-shrink: 0;
        }
        .trial-text-title { font-size: 13px; font-weight: 600; color: #065F46; }
        .trial-text-desc { font-size: 12px; color: #047857; margin-top: 2px; }

        /* Submit button */
        .btn-primary {
            width: 100%;
            padding: 14px;
            font-size: 15px;
            font-weight: 600;
            color: white;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border: none;
            border-radius: var(--radius);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(37,99,235,0.35);
            transition: all 0.2s;
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(37,99,235,0.4); }
        .btn-primary:active { transform: none; }

        /* Back link */
        .back-link {
            text-align: center;
            margin-top: 16px;
            font-size: 13px;
            color: var(--gray-500);
        }
        .back-link a { color: var(--primary); font-weight: 500; text-decoration: none; }
        .back-link a:hover { text-decoration: underline; }

        @media (max-width: 540px) {
            .card-header, .card-body { padding: 24px 20px; }
        }
    </style>
</head>
<body>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>

    <div class="page-wrapper">
        <!-- Logo -->
        <div class="logo-row">
            <div class="logo-bubble">
                <i class="bi bi-layers"></i>
            </div>
            <div class="logo-name">CuniApp Élevage</div>
        </div>

        <!-- Card -->
        <div class="card">
            <!-- Header -->
            <div class="card-header">
                <div class="google-badge">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/>
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                    </svg>
                    Connecté via Google
                </div>
                <h1 class="card-title">Créez votre espace entreprise</h1>
                <p class="card-subtitle">Une dernière étape pour accéder à votre tableau de bord élevage.</p>

                @if(isset($pendingUser))
                <div class="user-preview">
                    <div class="user-avatar">{{ strtoupper(substr($pendingUser['name'], 0, 1)) }}</div>
                    <div>
                        <div class="user-info-name">{{ $pendingUser['name'] }}</div>
                        <div class="user-info-email">{{ $pendingUser['email'] }}</div>
                    </div>
                    <i class="bi bi-check-circle-fill" style="color: var(--accent-green); margin-left: auto; font-size: 18px;"></i>
                </div>
                @endif
            </div>

            <!-- Body -->
            <div class="card-body">
                <!-- Step indicator -->
                <div class="step-indicator">
                    <div class="step done">
                        <div class="step-dot"><i class="bi bi-check"></i></div>
                        <span class="step-label">Google</span>
                    </div>
                    <div class="step-line done"></div>
                    <div class="step active">
                        <div class="step-dot">2</div>
                        <span class="step-label">Entreprise</span>
                    </div>
                    <div class="step-line"></div>
                    <div class="step">
                        <div class="step-dot" style="background: var(--gray-200); color: var(--gray-500);">3</div>
                        <span class="step-label" style="color: var(--gray-400);">Dashboard</span>
                    </div>
                </div>

                <!-- Free trial info -->
                <div class="trial-info">
                    <div class="trial-icon"><i class="bi bi-gift"></i></div>
                    <div>
                        <div class="trial-text-title">🎉 Essai gratuit de 14 jours inclus</div>
                        <div class="trial-text-desc">Accès complet à toutes les fonctionnalités. Aucune carte bancaire requise.</div>
                    </div>
                </div>

                <!-- Global error -->
                @if($errors->has('error'))
                    <div class="alert alert-error">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <span>{{ $errors->first('error') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('auth.google.complete.store') }}">
                    @csrf

                    <!-- Firm Name -->
                    <div class="form-group">
                        <label class="form-label" for="firm_name">
                            Nom de votre élevage / entreprise <span style="color: var(--accent-red);">*</span>
                        </label>
                        <div class="input-wrapper">
                            <i class="bi bi-building"></i>
                            <input
                                type="text"
                                id="firm_name"
                                name="firm_name"
                                class="form-input @error('firm_name') is-invalid @enderror"
                                placeholder="Ex : Élevage Dupont, Ferme du Soleil..."
                                value="{{ old('firm_name') }}"
                                required
                                autofocus
                            >
                        </div>
                        @error('firm_name')
                            <div class="error-msg">
                                <i class="bi bi-exclamation-circle-fill"></i>
                                <span>{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    <!-- Firm Description -->
                    <div class="form-group">
                        <label class="form-label" for="firm_description">
                            Description <span style="color: var(--gray-400); font-weight: 400;">(optionnel)</span>
                        </label>
                        <div class="input-wrapper textarea-wrapper">
                            <i class="bi bi-chat-text"></i>
                            <textarea
                                id="firm_description"
                                name="firm_description"
                                class="form-input @error('firm_description') is-invalid @enderror"
                                placeholder="Décrivez brièvement votre activité d'élevage…"
                                style="padding-left: 44px;"
                            >{{ old('firm_description') }}</textarea>
                        </div>
                        @error('firm_description')
                            <div class="error-msg">
                                <i class="bi bi-exclamation-circle-fill"></i>
                                <span>{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    <!-- Terms -->
                    <label class="terms-box">
                        <input type="checkbox" name="terms" id="terms" {{ old('terms') ? 'checked' : '' }}>
                        <span class="terms-text">
                            J'accepte les
                            <a href="{{ route('terms') }}" target="_blank">conditions d'utilisation</a>
                            et la
                            <a href="{{ route('privacy') }}" target="_blank">politique de confidentialité</a>
                            de CuniApp.
                        </span>
                    </label>
                    @error('terms')
                        <div class="error-msg" style="margin-top: -16px; margin-bottom: 16px;">
                            <i class="bi bi-exclamation-circle-fill"></i>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror

                    <button type="submit" class="btn-primary">
                        <i class="bi bi-rocket-takeoff"></i>
                        <span>Créer mon espace et démarrer</span>
                    </button>
                </form>

                <div class="back-link">
                    <a href="{{ route('welcome') }}">
                        <i class="bi bi-arrow-left"></i> Annuler et revenir à l'accueil
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
