<?php

namespace App\Support;

use Spatie\MediaLibrary\HasMedia;

class MediaUrls
{
    /**
     * Absolute URLs for the webp (1600w), medium (800w) and thumb (400w) conversions, or null when no image is attached.
     *
     * @return array{webp: string, medium: string, thumb: string}|null
     */
    public static function image(HasMedia $model, string $collection): ?array
    {
        $media = $model->getFirstMedia($collection);

        if (! $media) {
            return null;
        }

        return [
            'webp' => $media->getFullUrl('webp'),
            'medium' => $media->getFullUrl('medium'),
            'thumb' => $media->getFullUrl('thumb'),
        ];
    }
}
