<?php

return [
    'paths' => ['api/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'http://localhost:4200',
        'https://your-angular-app.com',
    ],
    'allowed_headers' => ['*'],
    'supports_credentials' => false,
];
