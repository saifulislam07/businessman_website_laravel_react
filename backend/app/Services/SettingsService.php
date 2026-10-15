<?php

namespace App\Services;

use App\Models\Setting;
use App\Support\MediaUrls;

class SettingsService
{
    /**
     * Active settings as a flat key => value object; hero_image and og_image are replaced by their image URLs.
     *
     * @return array<string, mixed>
     */
    public function all(): array
    {
        $settings = Setting::active()->ordered()->get();

        $result = $settings->pluck('value', 'key')->all();

        $hero = $settings->firstWhere('key', 'hero_image');
        $result['hero_image'] = $hero ? MediaUrls::image($hero, Setting::IMAGE_COLLECTION) : null;

        $og = $settings->firstWhere('key', 'og_image');
        $result['og_image'] = $og ? MediaUrls::image($og, Setting::OG_COLLECTION) : null;

        return $result;
    }
}
