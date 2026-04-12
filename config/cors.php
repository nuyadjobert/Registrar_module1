<?php

return [
    'paths' => ['api/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'http://localhost:4200',      // ✅ Your local frontend
        'http://localhost:*',         // Allow any local port
        'https://your-angular-app.com', // Your future production URL
    ],
    'allowed_headers' => ['*'],
    'supports_credentials' => true,
];