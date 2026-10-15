<?php

namespace App\Filament\Resources\AboutResource\Pages;

use App\Filament\Concerns\ManagesSiteSection;
use App\Filament\Resources\AboutResource;
use Filament\Resources\Pages\ManageRecords;

class ManageAbouts extends ManageRecords
{
    use ManagesSiteSection;

    protected static string $resource = AboutResource::class;

    protected function siteSection(): string
    {
        return 'about';
    }

    protected function siteSubheading(): string
    {
        return 'The biography, vision quote and photo shown in the About section.';
    }
}
