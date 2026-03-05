<?php

return [
    /*
    |--------------------------------------------------------------------------
    | JWT configuration
    |--------------------------------------------------------------------------
    |
    | secret: la clé secrète utilisée pour signer les tokens. Défini via
    |         la variable d'environnement JWT_SECRET. Si elle n'est pas
    |         fournie, on utilise la key de l'application (APP_KEY).
    |
    | ttl_minutes: durée de validité du token en minutes (par défaut 7 jours)
    |
    */

    'secret' => (function () {
        $secret = env('JWT_SECRET', env('APP_KEY'));
        
        // If the secret is base64-encoded (Laravel format), decode it
        if (strpos($secret, 'base64:') === 0) {
            $secret = base64_decode(substr($secret, 7));
        }
        
        return $secret;
    })(),

    'ttl_minutes' => env('JWT_TTL_MINUTES', 60 * 24 * 7),
];
