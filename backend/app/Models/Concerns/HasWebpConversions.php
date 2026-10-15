<?php

namespace App\Models\Concerns;

use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Adds "webp" (max 1600px wide), "medium" (800px) and "thumb" (400px) conversions to every image collection.
 * Includes InteractsWithMedia. Models using this trait must implement Spatie\MediaLibrary\HasMedia.
 */
trait HasWebpConversions
{
    use InteractsWithMedia {
        InteractsWithMedia::registerMediaConversions as private baseRegisterMediaConversions;
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        // Each size is limited by width only (the tall second bound is just a ceiling), so the
        // widths in the frontend's srcset (400w / 800w / 1600w) are accurate for portrait photos too.
        $this->addMediaConversion('webp')
            ->format('webp')
            ->fit(Fit::Max, 1600, 6000)
            ->nonQueued();

        $this->addMediaConversion('medium')
            ->format('webp')
            ->fit(Fit::Max, 800, 3000)
            ->nonQueued();

        $this->addMediaConversion('thumb')
            ->format('webp')
            ->fit(Fit::Max, 400, 1500)
            ->nonQueued();
    }
}
