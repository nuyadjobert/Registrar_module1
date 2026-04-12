<?php

return [
    'paths' => ['api/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['*'],  // ✅ Allow ALL for now
    'allowed_headers' => ['*'],
    'supports_credentials' => true,
];