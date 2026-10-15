<?php

namespace App\Filament\Resources\VentureResource\Pages;

use App\Filament\Concerns\ManagesSiteSection;
use App\Filament\Resources\VentureResource;
use Filament\Resources\Pages\ManageRecords;

class ManageVentures extends ManageRecords
{
    use ManagesSiteSection;

    protected static string $resource = VentureResource::class;

    protected function siteSection(): string
    {
        return 'ventures';
    }

    protected function siteSubheading(): string
    {
        return 'Companies shown in Business Ventures. Switch one off to hide it without deleting it.';
    }
}
