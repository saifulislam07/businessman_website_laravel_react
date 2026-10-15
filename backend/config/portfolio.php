<?php

/*
 * Site-specific settings read from .env. Application code must read these through config()
 * rather than env(), because env() returns null once the configuration is cached in production.
 */

return [

    // Public site; the API host redirects its bare domain here.
    'frontend_url' => env('FRONTEND_URL', 'https://example.com'),

    // Account created by the AdminUserSeeder (php artisan db:seed).
    'admin_email' => env('ADMIN_EMAIL'),
    'admin_password' => env('ADMIN_PASSWORD'),

];
