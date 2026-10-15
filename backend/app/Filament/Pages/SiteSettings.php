<?php

namespace App\Filament\Pages;

use App\Filament\Support\ImageUpload;
use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class SiteSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Site Settings';

    protected static ?string $title = 'Site Settings';

    protected static string $view = 'filament.pages.site-settings';

    /** Plain text settings stored as key => value rows. */
    private const TEXT_KEYS = [
        'name', 'tagline', 'email', 'phone', 'whatsapp', 'brunei_address', 'malaysia_address',
        'map_embed_url', 'linkedin', 'facebook', 'instagram', 'seo_title', 'seo_description',
        'about_title', 'about_subtitle', 'ventures_title', 'ventures_subtitle', 'expertise_title', 'expertise_subtitle',
        'journey_title', 'journey_subtitle', 'gallery_title', 'gallery_subtitle', 'partners_title', 'partners_subtitle',
        'partners_label', 'contact_title', 'contact_subtitle',
    ];

    /** Website sections whose heading and sub-heading can be edited: key => [label, default title, default subtitle]. */
    private const SECTIONS = [
        'about' => ['About', 'About', null],
        'ventures' => ['Business Ventures', 'Business Ventures', 'Companies built and led across Brunei and Malaysia.'],
        'expertise' => ['Expertise', 'Expertise', 'Industries and disciplines where experience runs deepest.'],
        'journey' => ['Journey', 'Journey', 'Key moments along the way.'],
        'gallery' => ['Gallery', 'Gallery', 'Moments from business, events and community.'],
        'partners' => ['Partners & Testimonials', 'Trusted by Partners', null],
        'contact' => ['Contact', 'Get in Touch', 'Send a message and we will reply as soon as possible.'],
    ];

    public ?array $data = [];

    public function getSubheading(): string | Htmlable | null
    {
        return 'Your name, logo, contact details, section headings and search-engine text. Changes appear on the website straight away.';
    }

    public function mount(): void
    {
        $values = Setting::whereIn('key', self::TEXT_KEYS)->pluck('value', 'key')->all();

        $this->form->fill($values);
    }

    public function form(Form $form): Form
    {
        $hero = $this->imageRow('hero_image');
        $og = $this->imageRow('og_image');
        $logo = $this->imageRow('logo');
        $logoLight = $this->imageRow('logo_light');

        return $form
            ->statePath('data')
            ->schema([
                Tabs::make('Settings')
                    ->persistTabInQueryString()
                    ->tabs([
                        Tabs\Tab::make('Identity')
                            ->icon('heroicon-o-identification')
                            ->columns(2)
                            ->schema([
                                TextInput::make('name')->label('Your name')->required()->maxLength(255),
                                TextInput::make('tagline')->label('Tagline')->maxLength(255)
                                    ->helperText('One line shown under your name at the top of the site.'),
                                ImageUpload::make('logo', Setting::LOGO_COLLECTION, 'Logo (for light backgrounds)')
                                    ->model($logo)
                                    ->acceptedFileTypes(['image/png', 'image/webp', 'image/jpeg'])
                                    ->helperText('Shown in the top bar once you scroll. Use a PNG or WebP with a transparent background and dark text, about 640 px wide. Leave empty to use the default logo.'),
                                ImageUpload::make('logo_light', Setting::LOGO_LIGHT_COLLECTION, 'Logo (for dark backgrounds)')
                                    ->model($logoLight)
                                    ->acceptedFileTypes(['image/png', 'image/webp', 'image/jpeg'])
                                    ->helperText('Shown over the dark hero, the mobile menu and the footer. Use a PNG or WebP with a transparent background and white text. Leave empty to use the default logo.'),
                                ImageUpload::make('hero_image', Setting::IMAGE_COLLECTION, 'Hero portrait')
                                    ->model($hero)
                                    ->helperText('Portrait shown beside your name. A tall photo (4:5) works best. JPG, PNG or WebP, up to 5 MB.'),
                            ]),

                        Tabs\Tab::make('Contact')
                            ->icon('heroicon-o-phone')
                            ->columns(2)
                            ->schema([
                                TextInput::make('email')->label('Public email')->email()->maxLength(255),
                                TextInput::make('phone')->label('Phone number')->tel()->maxLength(50),
                                TextInput::make('whatsapp')->label('WhatsApp number')->tel()->maxLength(50)
                                    ->helperText('With country code, e.g. +6738880000. Powers the "Chat on WhatsApp" buttons.'),
                                TextInput::make('map_embed_url')->label('Google Maps embed link')->url()->maxLength(1000)
                                    ->helperText('On Google Maps choose Share, then Embed a map, and copy the address after src="…".'),
                                Textarea::make('brunei_address')->label('Brunei office address')->rows(3),
                                Textarea::make('malaysia_address')->label('Malaysia office address')->rows(3),
                            ]),

                        Tabs\Tab::make('Social')
                            ->icon('heroicon-o-share')
                            ->columns(1)
                            ->schema([
                                TextInput::make('linkedin')->label('LinkedIn page')->url()->maxLength(255)->placeholder('https://www.linkedin.com/in/…'),
                                TextInput::make('facebook')->label('Facebook page')->url()->maxLength(255)->placeholder('https://www.facebook.com/…'),
                                TextInput::make('instagram')->label('Instagram profile')->url()->maxLength(255)->placeholder('https://www.instagram.com/…'),
                            ]),

                        Tabs\Tab::make('Section Titles')
                            ->icon('heroicon-o-bars-3-bottom-left')
                            ->schema($this->sectionTitleFields()),

                        Tabs\Tab::make('SEO')
                            ->icon('heroicon-o-magnifying-glass')
                            ->columns(1)
                            ->schema([
                                TextInput::make('seo_title')->label('Page title')->maxLength(70)
                                    ->helperText('The headline in Google results. Aim for 50–60 characters.'),
                                Textarea::make('seo_description')->label('Page description')->rows(3)->maxLength(200)
                                    ->helperText('The short text under the headline in Google results. Aim for 120–160 characters.'),
                                ImageUpload::make('og_image', Setting::OG_COLLECTION, 'Share image')
                                    ->model($og)
                                    ->helperText('Shown when the site is shared on WhatsApp, Facebook or LinkedIn. Use 1200 × 630 pixels.'),
                            ]),
                    ]),
            ]);
    }

    public function save(): void
    {
        $state = $this->form->getState();

        $order = 0;
        foreach (self::TEXT_KEYS as $key) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $state[$key] ?? null, 'sort_order' => $order++, 'is_active' => true],
            );
        }

        // Uploads attach to the hero_image / og_image rows via each field's own model.
        $this->form->saveRelationships();

        Notification::make()->title('Settings saved')->success()->send();
    }

    /** @return array<Action> */
    public function getFormActions(): array
    {
        return [
            Action::make('save')->label('Save changes')->submit('save')->keyBindings(['mod+s']),
        ];
    }

    /** @return array<Section> */
    private function sectionTitleFields(): array
    {
        $sections = [];

        foreach (self::SECTIONS as $key => [$label, $title, $subtitle]) {
            $fields = [
                TextInput::make("{$key}_title")->label('Heading')->placeholder($title)->maxLength(100),
                TextInput::make("{$key}_subtitle")->label('Sub-heading')->placeholder($subtitle ?? 'None')->maxLength(200),
            ];

            if ($key === 'partners') {
                $fields[] = TextInput::make('partners_label')->label('Partner logos label')->placeholder('Our partners')->maxLength(60);
            }

            $sections[] = Section::make($label)->columns(2)->compact()->schema($fields);
        }

        return $sections;
    }

    private function imageRow(string $key): Setting
    {
        return Setting::firstOrCreate(['key' => $key], ['value' => null, 'sort_order' => 100, 'is_active' => true]);
    }
}
