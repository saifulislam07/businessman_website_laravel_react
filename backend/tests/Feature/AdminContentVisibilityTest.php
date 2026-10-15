<?php

use App\Filament\Resources\AboutResource\Pages\ManageAbouts;
use App\Filament\Resources\ExpertiseResource\Pages\ManageExpertises;
use App\Filament\Resources\GalleryItemResource\Pages\ManageGalleryItems;
use App\Filament\Resources\MilestoneResource\Pages\ManageMilestones;
use App\Filament\Resources\PartnerResource\Pages\ManagePartners;
use App\Filament\Resources\StatResource\Pages\ManageStats;
use App\Filament\Resources\TestimonialResource\Pages\ManageTestimonials;
use App\Filament\Resources\VentureResource\Pages\ManageVentures;
use App\Models\About;
use App\Models\Expertise;
use App\Models\GalleryItem;
use App\Models\Milestone;
use App\Models\Partner;
use App\Models\Stat;
use App\Models\Testimonial;
use App\Models\User;
use App\Models\Venture;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
    config(['media-library.disk_name' => 'public']);
    $this->actingAs(User::factory()->create());
});

/*
 * For every content section: warm the /home cache, edit a record through the admin panel,
 * and confirm the public API shows the change immediately (the cache is cleared by the observers).
 */
dataset('sections', [
    'stats' => [ManageStats::class, fn () => Stat::create(['label' => 'Old', 'value' => '1']), ['label' => 'Edited stat', 'value' => '9'], 'stats', 'label', 'Edited stat'],
    'expertise' => [ManageExpertises::class, fn () => Expertise::create(['title' => 'Old']), ['title' => 'Edited expertise'], 'expertise', 'title', 'Edited expertise'],
    'milestones' => [ManageMilestones::class, fn () => Milestone::create(['year' => '2020', 'title' => 'Old']), ['title' => 'Edited milestone', 'year' => '2021'], 'milestones', 'title', 'Edited milestone'],
    'partners' => [ManagePartners::class, fn () => Partner::create(['name' => 'Old']), ['name' => 'Edited partner'], 'partners', 'name', 'Edited partner'],
    'testimonials' => [ManageTestimonials::class, fn () => Testimonial::create(['name' => 'Old', 'quote' => 'Old']), ['name' => 'Edited person', 'quote' => 'Edited quote'], 'testimonials', 'quote', 'Edited quote'],
    'ventures' => [ManageVentures::class, fn () => Venture::create(['name' => 'Old', 'slug' => 'old', 'industry' => 'X', 'country' => 'BN']), ['name' => 'Edited venture', 'slug' => 'old', 'industry' => 'X', 'country' => 'MY'], 'ventures', 'name', 'Edited venture'],
    'gallery' => [ManageGalleryItems::class, fn () => GalleryItem::create(['caption' => 'Old']), ['caption' => 'Edited caption', 'image' => [UploadedFile::fake()->image('g.jpg', 600, 400)]], 'gallery', 'caption', 'Edited caption'],
]);

it('shows an edit made in the admin on the public API immediately', function (string $page, Closure $make, array $data, string $key, string $field, string $expected) {
    $record = $make();
    $this->getJson('/api/v1/home')->assertOk();
    expect(Cache::has('home'))->toBeTrue();

    livewire($page)->callTableAction('edit', $record, data: $data)->assertHasNoTableActionErrors();

    expect($this->getJson('/api/v1/home')->json("data.$key.0.$field"))->toBe($expected);
})->with('sections');

it('shows an edit to the About section immediately', function () {
    $about = About::create(['bio' => 'Old bio']);
    $this->getJson('/api/v1/home')->assertOk();

    livewire(ManageAbouts::class)->callTableAction('edit', $about, data: ['bio' => '<p>New <strong>bio</strong></p>', 'vision' => 'New vision'])->assertHasNoTableActionErrors();

    $this->getJson('/api/v1/home')->assertJsonPath('data.about.bio', '<p>New <strong>bio</strong></p>')->assertJsonPath('data.about.vision', 'New vision');
});

it('hides a record from the site when it is switched off and removes it when deleted', function () {
    $venture = Venture::create(['name' => 'Acme', 'slug' => 'acme', 'industry' => 'X', 'country' => 'BN']);
    $this->getJson('/api/v1/home')->assertJsonCount(1, 'data.ventures');

    livewire(ManageVentures::class)->call('updateTableColumnState', 'is_active', (string) $venture->getKey(), false);
    $this->getJson('/api/v1/home')->assertJsonCount(0, 'data.ventures');

    livewire(ManageVentures::class)->call('updateTableColumnState', 'is_active', (string) $venture->getKey(), true);
    $this->getJson('/api/v1/home')->assertJsonCount(1, 'data.ventures');

    livewire(ManageVentures::class)->callTableAction('delete', $venture);
    $this->getJson('/api/v1/home')->assertJsonCount(0, 'data.ventures');
});
