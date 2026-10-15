<?php

use App\Filament\Pages\SiteSettings;
use App\Filament\Resources\ContactMessageResource;
use App\Filament\Resources\ContactMessageResource\Pages\ListContactMessages;
use App\Filament\Resources\ContactMessageResource\Pages\ViewContactMessage;
use App\Filament\Resources\StatResource\Pages\ManageStats;
use App\Filament\Resources\VentureResource\Pages\ManageVentures;
use App\Models\ContactMessage;
use App\Models\Setting;
use App\Models\Stat;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

it('requires login for the admin panel', function () {
    auth()->logout();

    $this->get('/admin')->assertRedirect('/admin/login');
});

it('renders every admin page', function () {
    $this->get('/admin')->assertOk();
    $this->get('/admin/site-settings')->assertOk();

    foreach (['abouts', 'stats', 'ventures', 'expertises', 'milestones', 'gallery-items', 'partners', 'testimonials', 'contact-messages'] as $path) {
        $this->get("/admin/$path")->assertOk();
    }
});

it('creates a venture and shows it in the API immediately', function () {
    $this->getJson('/api/v1/home')->assertJsonCount(0, 'data.ventures');

    livewire(ManageVentures::class)
        ->callAction('create', [
            'name' => 'Acme Holdings',
            'slug' => 'acme-holdings',
            'industry' => 'Retail',
            'country' => 'MY',
            'is_active' => true,
        ])
        ->assertHasNoActionErrors();

    $this->getJson('/api/v1/home')->assertJsonPath('data.ventures.0.name', 'Acme Holdings');
});

it('clears the home cache when a table is reordered', function () {
    $a = Stat::create(['label' => 'A', 'value' => '1', 'sort_order' => 1]);
    $b = Stat::create(['label' => 'B', 'value' => '2', 'sort_order' => 2]);

    $this->getJson('/api/v1/home');
    expect(Cache::has('home'))->toBeTrue();

    livewire(ManageStats::class)->call('reorderTable', [$b->id, $a->id]);

    expect(Cache::has('home'))->toBeFalse()
        ->and(Stat::ordered()->pluck('label')->all())->toBe(['B', 'A']);
});

it('saves site settings and the hero image', function () {
    Storage::fake('public');
    config(['media-library.disk_name' => 'public']);

    livewire(SiteSettings::class)
        ->fillForm(['name' => 'Your Name', 'tagline' => 'New tagline', 'seo_title' => 'SEO'])
        ->set('data.hero_image', [UploadedFile::fake()->image('hero.jpg', 800, 600)])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(Setting::where('key', 'tagline')->first()->value)->toBe('New tagline');

    $response = $this->getJson('/api/v1/settings')->assertOk();
    expect($response->json('data.tagline'))->toBe('New tagline')
        ->and($response->json('data.hero_image.webp'))->toContain('.webp')
        ->and($response->json('data.hero_image.medium'))->toContain('-medium.webp')
        ->and($response->json('data.hero_image.thumb'))->toContain('-thumb.webp');
});

it('saves the two logos and serves them from the settings API', function () {
    Storage::fake('public');
    config(['media-library.disk_name' => 'public']);

    livewire(SiteSettings::class)
        ->fillForm(['name' => 'Your Name'])
        ->set('data.logo', [UploadedFile::fake()->image('logo.png', 640, 256)])
        ->set('data.logo_light', [UploadedFile::fake()->image('logo-light.png', 640, 256)])
        ->call('save')
        ->assertHasNoFormErrors();

    $data = $this->getJson('/api/v1/settings')->assertOk()->json('data');

    expect($data['logo']['medium'])->toContain('-medium.webp')
        ->and($data['logo_light']['medium'])->toContain('-medium.webp');
});

it('marks a message as read when opened and lists unread count as badge', function () {
    $message = ContactMessage::create(['name' => 'Jane', 'email' => 'jane@example.com', 'message' => 'Hi']);

    expect(ContactMessageResource::getNavigationBadge())->toBe('1');

    livewire(ViewContactMessage::class, ['record' => $message->getKey()])->assertSuccessful();

    expect($message->fresh()->is_read)->toBeTrue()
        ->and(ContactMessageResource::getNavigationBadge())->toBeNull();
});

it('lists messages and filters by read status', function () {
    $unread = ContactMessage::create(['name' => 'Unread', 'email' => 'a@example.com', 'message' => 'Hi']);
    $read = ContactMessage::create(['name' => 'Read', 'email' => 'b@example.com', 'message' => 'Hi', 'is_read' => true]);

    livewire(ListContactMessages::class)
        ->assertCanSeeTableRecords([$unread, $read])
        ->filterTable('is_read', false)
        ->assertCanSeeTableRecords([$unread])
        ->assertCanNotSeeTableRecords([$read]);
});
