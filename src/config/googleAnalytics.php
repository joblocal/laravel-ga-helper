<?php

return [
    'app_name' => env('GOOGLE_ANALYTICS_APP_NAME', 'Hello Analytics Reporting'),
    'type' => env('GOOGLE_ANALYTICS_TYPE', 'service_account'),
    'client_id' => env('GOOGLE_ANALYTICS_CLIENT_ID', null),
    'client_email' => env('GOOGLE_ANALYTICS_CLIENT_EMAIL', null),
    'private_key' => env('GOOGLE_ANALYTICS_SIGNING_KEY', null),
];
