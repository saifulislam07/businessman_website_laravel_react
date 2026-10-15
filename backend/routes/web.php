<?php

use Illuminate\Support\Facades\Route;

// This host only serves the JSON API and the admin panel. Send visitors who land on the bare
// domain to the public site instead of exposing a framework default page.
Route::get('/', fn () => redirect()->away(config('portfolio.frontend_url')));
