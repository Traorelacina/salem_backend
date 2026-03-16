<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Paths that should be CORS enabled
    |--------------------------------------------------------------------------
    |
    | Définir ici les routes qui accepteront les requêtes cross-origin.
    |
    */
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    /*
    |--------------------------------------------------------------------------
    | Allowed HTTP methods
    |--------------------------------------------------------------------------
    |
    | Méthodes HTTP autorisées pour les requêtes cross-origin.
    | Utiliser ['*'] pour tout autoriser.
    |
    */
    'allowed_methods' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | Allowed origins
    |--------------------------------------------------------------------------
    |
    | Origines autorisées à accéder à ton API.
    | Ici, ton frontend Netlify.
    |
    */
    'allowed_origins' => ['https://salemtech.netlify.app'],
    // 'allowed_origins' => ['*'], // pour dev seulement

    /*
    |--------------------------------------------------------------------------
    | Allowed origin patterns
    |--------------------------------------------------------------------------
    |
    | Motifs pour autoriser des origines dynamiques.
    |
    */
    'allowed_origins_patterns' => [],

    /*
    |--------------------------------------------------------------------------
    | Allowed headers
    |--------------------------------------------------------------------------
    |
    | En-têtes autorisés pour les requêtes cross-origin.
    | ['*'] autorise tout.
    |
    */
    'allowed_headers' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | Exposed headers
    |--------------------------------------------------------------------------
    |
    | En-têtes exposés à la réponse côté client.
    |
    */
    'exposed_headers' => [],

    /*
    |--------------------------------------------------------------------------
    | Max age
    |--------------------------------------------------------------------------
    |
    | Temps de mise en cache de la requête preflight (en secondes).
    |
    */
    'max_age' => 0,

    /*
    |--------------------------------------------------------------------------
    | Supports credentials
    |--------------------------------------------------------------------------
    |
    | Si tu utilises des cookies ou des tokens d'authentification.
    |
    */
    'supports_credentials' => true,

];
