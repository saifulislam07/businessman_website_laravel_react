<?php

namespace App\Providers;

use App\Models\About;
use App\Models\Expertise;
use App\Models\GalleryItem;
use App\Models\Milestone;
use App\Models\Partner;
use App\Models\Setting;
use App\Models\Stat;
use App\Models\Testimonial;
use App\Models\Venture;
use App\Models\VisitedCountry;
use App\Observers\ClearHomeCacheObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Content models whose changes must invalidate the cached /home payload.
     */
    private const CONTENT_MODELS = [
        About::class,
        Expertise::class,
        GalleryItem::class,
        Milestone::class,
        Partner::class,
        Setting::class,
        Stat::class,
        Testimonial::class,
        Venture::class,
        VisitedCountry::class,
    ];

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        foreach (self::CONTENT_MODELS as $model) {
            $model::observe(ClearHomeCacheObserver::class);
        }

        // Images are attached after the parent model is saved, so media changes clear the cache too.
        Media::observe(ClearHomeCacheObserver::class);

        RateLimiter::for('contact', fn (Request $request) => Limit::perMinute(3)->by($request->ip()));
    }
}
