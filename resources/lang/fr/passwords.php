<?php
// resources/lang/fr/passwords.php

return [

    /*
    |--------------------------------------------------------------------------
    | Password Reset Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are the default lines which match reasons
    | that are given by the password broker for a password update attempt
    | has failed, such as for an invalid token or invalid new password.
    |
    */

    // ✅ Message de succès après envoi du lien
    'sent' => 'Nous avons envoyé un lien de réinitialisation de mot de passe à votre adresse email.',

    // ✅ Message de succès après réinitialisation
    'reset' => 'Votre mot de passe a été réinitialisé avec succès !',

    // ✅ Erreur : token invalide ou expiré
    'token' => 'Ce lien de réinitialisation est invalide ou a expiré.',

    // ✅ Erreur : email non trouvé
    'user' => "Nous ne trouvons aucun compte associé à cette adresse email.",

    // ✅ Erreur : trop de tentatives (rate limiting)
    'throttled' => 'Veuillez patienter quelques instants avant de réessayer.',

    // ✅ Message pour la confirmation du nouveau mot de passe
    'password' => 'Le mot de passe doit contenir au moins 8 caractères et être confirmé.',

];