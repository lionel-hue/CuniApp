{{-- resources/views/vendor/notifications/email.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Notification' }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;
            background: #f8fafc;
        }
        .email-container {
            background: #ffffff; border-radius: 12px; overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #2563EB 0%, #06B6D4 100%);
            padding: 24px; text-align: center;
        }
        .logo {
            width: 50px; height: 50px; margin: 0 auto 12px;
            background: rgba(255,255,255,0.2); border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
        }
        .brand-name {
            color: #ffffff; font-size: 20px; font-weight: 700; margin: 0;
        }
        .email-body { padding: 32px 24px; }
        .greeting { font-size: 18px; color: #1f2937; margin-bottom: 16px; }
        .intro { color: #4b5563; margin-bottom: 24px; line-height: 1.7; }
        .action-button {
            display: inline-block;
            background: linear-gradient(135deg, #2563EB, #1D4ED8);
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 15px;
            margin: 16px 0;
            text-align: center;
        }
        .action-button:hover {
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3);
        }
        .expiry-note {
            background: #fff7ed; border-left: 4px solid #f97316;
            padding: 12px 16px; border-radius: 0 6px 6px 0;
            margin: 20px 0; font-size: 14px; color: #92400e;
        }
        .fallback-link {
            background: #f3f4f6; padding: 12px; border-radius: 6px;
            font-size: 12px; word-break: break-all; color: #4b5563;
            margin: 16px 0;
        }
        .fallback-link a { color: #2563EB; text-decoration: none; }
        .security-note {
            background: #f0fdf4; border: 1px solid #bbf7d0;
            padding: 12px 16px; border-radius: 6px;
            font-size: 14px; color: #166534; margin: 20px 0;
        }
        .email-footer {
            background: #f9fafb; padding: 20px 24px;
            text-align: center; font-size: 13px; color: #6b7280;
            border-top: 1px solid #e5e7eb;
        }
        .footer-links { margin: 12px 0; }
        .footer-links a {
            color: #2563EB; text-decoration: none; margin: 0 8px;
        }
        @media (max-width: 600px) {
            body { padding: 10px; }
            .email-body { padding: 24px 16px; }
            .action-button { width: 100%; box-sizing: border-box; }
        }
    </style>
</head>
<body>
    <div class="email-container">
        
        <!-- Header avec logo -->
        <div class="email-header">
            <div class="logo">
                <svg width="30" height="30" viewBox="0 0 40 40" fill="none">
                    <path d="M20 5L35 15V25L20 35L5 25V15L20 5Z" fill="white"/>
                    <path d="M20 12L28 17V23L20 28L12 23V17L20 12Z" fill="rgba(255,255,255,0.9)"/>
                </svg>
            </div>
            <h1 class="brand-name">CuniApp Élevage</h1>
        </div>

        <!-- Corps de l'email -->
        <div class="email-body">
            
            <!-- Salutation -->
            <p class="greeting">
                @if(isset($name) && $name)
                    Bonjour {{ $name }},
                @else
                    Bonjour,
                @endif
            </p>

            <!-- Message principal -->
            <p class="intro">
                Vous recevez cet email car une demande de réinitialisation de mot de passe a été effectuée pour votre compte.
            </p>

            <!-- Bouton d'action -->
            @if(isset($actionText) && isset($actionUrl))
                <div style="text-align: center;">
                    <a href="{{ $actionUrl }}" class="action-button">
                        {{ $actionText ?? 'Réinitialiser mon mot de passe' }}
                    </a>
                </div>
            @endif

            <!-- Note d'expiration -->
            <div class="expiry-note">
                <strong>⏱ Ce lien est valable pendant 5 minutes.</strong><br>
                Passé ce délai, vous devrez effectuer une nouvelle demande.
            </div>

            <!-- Lien de secours -->
            @if(isset($actionUrl))
                <p style="font-size: 14px; color: #6b7280;">
                    Si le bouton ne fonctionne pas, copiez-collez ce lien dans votre navigateur :
                </p>
                <div class="fallback-link">
                    {{ $actionUrl }}
                </div>
            @endif

            <!-- Note de sécurité -->
            <div class="security-note">
                <strong>Vous n'avez pas demandé cette réinitialisation ?</strong><br>
                Aucune action n'est requise. Votre mot de passe reste inchangé et votre compte est sécurisé.
            </div>

            <!-- Salutation finale -->
            <p class="intro" style="margin-top: 32px;">
                Cordialement,<br>
                <strong>L'équipe CuniApp Élevage</strong>
            </p>

        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p style="margin: 0 0 8px 0;">
                © {{ date('Y') }} CuniApp Élevage. Tous droits réservés.
            </p>
            <div class="footer-links">
                <a href="{{ url('/privacy') }}">Confidentialité</a>
                •
                <a href="{{ url('/terms') }}">Conditions</a>
                •
                <a href="{{ url('/contact') }}">Contact</a>
            </div>
            <p style="margin: 12px 0 0 0; font-size: 12px;">
                Cet email a été envoyé à {{ $email ?? 'votre adresse' }}.
            </p>
        </div>

    </div>
</body>
</html>