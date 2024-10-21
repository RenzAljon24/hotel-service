<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'], // Allow all methods

    'allowed_origins' => ['*'], // Allow all origins temporarily for debugging purposes

    'allowed_headers' => ['*'], // Allow all headers temporarily for debugging purposes

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true, // Set to true if needed, or false otherwise

];


