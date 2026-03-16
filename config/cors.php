return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['https://salemtech.netlify.app'], // ton frontend
    //'allowed_origins' => ['*'], // option temporaire pour dev

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true, // nécessaire si tu utilises cookies ou tokens
];
