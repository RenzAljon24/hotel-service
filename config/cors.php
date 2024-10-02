<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'], // Allow all methods

    'allowed_origins' => ['http://localhost:3000'],

    'allowed_headers' => ['Content-Type', 'X-Requested-With', 'Accept', 'Authorization'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true, // Set this to true if you need credentials

];
