<?php

namespace Database\Seeders;

use App\Models\About;
use App\Models\Expertise;
use App\Models\GalleryItem;
use App\Models\Milestone;
use App\Models\Partner;
use App\Models\Stat;
use App\Models\Testimonial;
use App\Models\Venture;
use Database\Seeders\Concerns\PlaceholderImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Spatie\MediaLibrary\HasMedia;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $about = About::create([
            'bio' => '<p>Your Name is an entrepreneur and businessman based between Brunei and Malaysia. Over more than a decade he has built and led companies in trading, technology and property.</p><p>His approach is simple: <strong>long-term partnerships, disciplined operations</strong> and a quiet focus on quality.</p>',
            'vision' => 'To build businesses that create lasting value for partners, teams and communities across Southeast Asia.',
        ]);
        $this->attach($about, 'About Photo', 1000, 1200);

        $stats = [
            ['Years of Experience', '15+', 'calendar'],
            ['Ventures Founded', '8', 'building'],
            ['Team Members', '120+', 'users'],
            ['Countries Visited', '3', 'globe'],
        ];
        foreach ($stats as $i => [$label, $value, $icon]) {
            Stat::create(['label' => $label, 'value' => $value, 'icon' => $icon, 'sort_order' => $i]);
        }

        $ventures = [
            ['Borneo Trading Sdn Bhd', 'borneo-trading', 'Trading & Distribution', 'BN', '<p>Regional import and distribution of consumer and industrial goods.</p>', 2012],
            ['Nusantara Tech Solutions', 'nusantara-tech', 'Technology', 'MY', '<p>Software, cloud and digital transformation services for SMEs.</p>', 2017],
            ['Seri Property Holdings', 'seri-property', 'Real Estate', 'BN', '<p>Commercial and residential property development and management.</p>', 2019],
        ];
        foreach ($ventures as $i => [$name, $slug, $industry, $country, $desc, $year]) {
            $venture = Venture::create([
                'name' => $name, 'slug' => $slug, 'industry' => $industry, 'country' => $country,
                'description' => $desc, 'founded_year' => $year, 'website_url' => 'https://example.com',
                'sort_order' => $i,
            ]);
            $this->attach($venture, $name, 400, 400);
        }

        $expertise = [
            ['Business Strategy', '<p>Market entry and growth planning across Brunei and Malaysia.</p>', 'Target'],
            ['Trading & Supply Chain', '<p>Sourcing, logistics and distribution networks.</p>', 'Truck'],
            ['Technology & Digital', '<p>Building scalable digital products and platforms.</p>', 'Cpu'],
            ['Real Estate', '<p>Property development, investment and asset management.</p>', 'Building2'],
            ['Partnerships', '<p>Joint ventures and long-term strategic alliances.</p>', 'Handshake'],
        ];
        foreach ($expertise as $i => [$title, $desc, $icon]) {
            Expertise::create(['title' => $title, 'description' => $desc, 'icon' => $icon, 'sort_order' => $i]);
        }

        $milestones = [
            ['2010', 'Started in business', '<p>Began a small trading operation in Bandar Seri Begawan.</p>'],
            ['2012 - 2013', 'Founded Borneo Trading', '<p>Incorporated the first company and expanded distribution.</p>'],
            ['2015', 'Entered Malaysia', '<p>Opened a Kuala Lumpur office to serve regional partners.</p>'],
            ['2017', 'Launched Nusantara Tech', '<p>Diversified into technology and digital services.</p>'],
            ['2019', 'Property division', '<p>Established Seri Property Holdings.</p>'],
            ['2024', 'Regional expansion', '<p>Growing teams and partnerships across Southeast Asia.</p>'],
        ];
        foreach ($milestones as $i => [$year, $title, $desc]) {
            Milestone::create(['year' => $year, 'title' => $title, 'description' => $desc, 'sort_order' => $i]);
        }

        $gallery = [
            ['Business conference', 'Events'], ['Office opening', 'Business'], ['Partner meeting', 'Business'],
            ['Community programme', 'Community'], ['Site visit', 'Business'], ['Award evening', 'Events'],
        ];
        foreach ($gallery as $i => [$caption, $category]) {
            $item = GalleryItem::create(['caption' => $caption, 'category' => $category, 'sort_order' => $i]);
            $this->attach($item, $caption, 1200, 800);
        }

        foreach (['Bank Alpha', 'Logistics Beta', 'Tech Gamma', 'Holdings Delta'] as $i => $name) {
            $partner = Partner::create(['name' => $name, 'url' => 'https://example.com', 'sort_order' => $i]);
            $this->attach($partner, $name, 400, 200);
        }

        $testimonials = [
            ['Ahmad Rahman', 'Managing Director', 'Rahman Group', 'He is a rare partner: calm, precise and always true to his word.'],
            ['Siti Nurhaliza', 'Head of Operations', 'Coastal Logistics', 'Working with his team transformed how we handle regional supply.'],
            ['David Tan', 'CEO', 'Tan Capital', 'A thoughtful investor with a genuine long-term view.'],
        ];
        foreach ($testimonials as $i => [$name, $designation, $company, $quote]) {
            $t = Testimonial::create(compact('name', 'designation', 'company', 'quote') + ['sort_order' => $i]);
            $this->attach($t, $name, 300, 300);
        }
    }

    private function attach(Model&HasMedia $model, string $label, int $w, int $h): void
    {
        $model->addMedia(PlaceholderImage::make($label, $w, $h))->toMediaCollection($model::IMAGE_COLLECTION);
    }
}
