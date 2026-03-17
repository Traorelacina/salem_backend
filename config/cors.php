<?php

return [
    'paths' => ['api/*', 'auth/*', 'admin/*', 'v1/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'https://salemtech.netlify.app',
        // Ajoute d'autres origines si besoin (preview Netlify, localhost, etc.)
        'http://localhost:5173',
    ],

    'allowed_origins_patterns' => [
        // Couvre les URLs de preview Netlify du type xxxxxxx--salemtech.netlify.app
        '#^https://[a-z0-9]+-*--salemtech\.netlify\.app$#',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true, // Nécessaire pour Sanctum
];
