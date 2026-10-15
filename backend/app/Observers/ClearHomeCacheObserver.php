<?php

namespace App\Observers;

use App\Http\Controllers\Api\V1\HomeController;
use Illuminate\Support\Facades\Cache;

/**
 * Forgets the cached /home payload whenever content changes.
 */
class ClearHomeCacheObserver
{
    public function saved(mixed $model): void
    {
        Cache::forget(HomeController::CACHE_KEY);
    }

    public function deleted(mixed $model): void
    {
        Cache::forget(HomeController::CACHE_KEY);
    }
}
