<?php

namespace App\Filament\Resources\GalleryItemResource\Pages;

use App\Filament\Concerns\ManagesSiteSection;
use App\Filament\Resources\GalleryItemResource;
use Filament\Resources\Pages\ManageRecords;

class ManageGalleryItems extends ManageRecords
{
    use ManagesSiteSection;

    protected static string $resource = GalleryItemResource::class;

    protected function siteSection(): string
    {
        return 'gallery';
    }

    protected function siteSubheading(): string
    {
        return 'Photos shown in the gallery. Each category name becomes a filter button.';
    }
}
