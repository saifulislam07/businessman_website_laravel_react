<?php

namespace App\Services;

use App\Models\Setting;
use App\Support\MediaUrls;

class SettingsService
{
    /**
     * Active settings as a flat key => value object; hero_image, og_image, logo and logo_light are replaced by their image URLs.
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

        $logo = $settings->firstWhere('key', 'logo');
        $result['logo'] = $logo ? MediaUrls::image($logo, Setting::LOGO_COLLECTION) : null;

        $logoLight = $settings->firstWhere('key', 'logo_light');
        $result['logo_light'] = $logoLight ? MediaUrls::image($logoLight, Setting::LOGO_LIGHT_COLLECTION) : null;

        return $result;
    }
}
