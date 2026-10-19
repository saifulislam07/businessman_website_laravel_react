<?php

namespace App\Filament\Resources\VisitedCountryResource\Pages;

use App\Filament\Concerns\ManagesSiteSection;
use App\Filament\Resources\VisitedCountryResource;
use Filament\Resources\Pages\ManageRecords;

class ManageVisitedCountries extends ManageRecords
{
    use ManagesSiteSection;

    protected static string $resource = VisitedCountryResource::class;

    protected function siteSection(): string
    {
        return 'top';
    }

    protected function siteSubheading(): string
    {
        return 'Flags of the countries visited, shown under the buttons in the top section. Drag the handle to reorder.';
    }
}
