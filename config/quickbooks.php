<?php

return [
    'qbo_integration' => env('QUICKBOOKS_INTEGRATION', FALSE),
    'client_id' => env('QUICKBOOKS_CLIENT_ID'),
    'client_secret' => env('QUICKBOOKS_CLIENT_SECRET'),
    'redirect_uri' => env('QUICKBOOKS_REDIRECT_URI'),
    'realm_id' => env('QUICKBOOKS_REALM_ID'),
    'base_url' => env('QUICKBOOKS_BASE_URL'),
];
