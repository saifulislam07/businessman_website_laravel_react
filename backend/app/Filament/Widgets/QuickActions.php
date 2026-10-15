<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\SiteSettings;
use App\Filament\Resources\ContactMessageResource;
use App\Filament\Resources\GalleryItemResource;
use App\Filament\Resources\VentureResource;
use Filament\Widgets\Widget;

class QuickActions extends Widget
{
    protected static string $view = 'filament.widgets.quick-actions';

    protected static ?int $sort = 0;

    protected int | string | array $columnSpan = 'full';

    protected function getViewData(): array
    {
        return [
            'name' => auth()->user()?->name ?? 'Administrator',
            'actions' => [
                ['label' => 'Site settings', 'hint' => 'Name, contact details, SEO', 'icon' => 'heroicon-o-cog-6-tooth', 'url' => SiteSettings::getUrl()],
                ['label' => 'Ventures', 'hint' => 'Add or edit your companies', 'icon' => 'heroicon-o-building-office', 'url' => VentureResource::getUrl()],
                ['label' => 'Gallery', 'hint' => 'Upload new photos', 'icon' => 'heroicon-o-photo', 'url' => GalleryItemResource::getUrl()],
                ['label' => 'Messages', 'hint' => 'Read and reply to enquiries', 'icon' => 'heroicon-o-envelope', 'url' => ContactMessageResource::getUrl()],
                ['label' => 'View website', 'hint' => 'Opens in a new tab', 'icon' => 'heroicon-o-arrow-top-right-on-square', 'url' => config('portfolio.frontend_url'), 'external' => true],
            ],
        ];
    }
}
