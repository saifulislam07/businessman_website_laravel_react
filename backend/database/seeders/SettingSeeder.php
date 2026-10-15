<?php

namespace Database\Seeders;

use App\Models\Setting;
use Database\Seeders\Concerns\PlaceholderImage;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'name' => 'Fayer Ahammed',
            'hero_image' => null,
            'og_image' => null,
            'tagline' => 'Entrepreneur building enduring businesses across Brunei and Malaysia.',
            'email' => 'hello@fayerahammed.com',
            'phone' => '+673 222 0000',
            'whatsapp' => '+6738880000',
            'brunei_address' => 'Unit 12, Bangunan Contoh, Jalan Gadong, Bandar Seri Begawan BE1318, Brunei Darussalam',
            'malaysia_address' => 'Level 18, Menara Contoh, Jalan Sultan Ismail, 50250 Kuala Lumpur, Malaysia',
            'map_embed_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3989.8!2d114.93!3d4.89',
            'linkedin' => 'https://www.linkedin.com/in/fayerahammed',
            'facebook' => 'https://www.facebook.com/fayerahammed',
            'instagram' => 'https://www.instagram.com/fayerahammed',
            'seo_title' => 'Fayer Ahammed — Entrepreneur in Brunei & Malaysia',
            'seo_description' => 'Official website of Fayer Ahammed, entrepreneur and businessman with ventures across Brunei and Malaysia.',
        ];

        $order = 0;
        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value, 'sort_order' => $order++, 'is_active' => true]);
        }

        $holder = Setting::where('key', 'hero_image')->first();
        $holder->clearMediaCollection(Setting::IMAGE_COLLECTION);
        $holder->addMedia(PlaceholderImage::make('Hero Portrait', 1200, 1500))->toMediaCollection(Setting::IMAGE_COLLECTION);
    }
}
