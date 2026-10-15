<?php

namespace App\Filament\Resources\PartnerResource\Pages;

use App\Filament\Concerns\ManagesSiteSection;
use App\Filament\Resources\PartnerResource;
use Filament\Resources\Pages\ManageRecords;

class ManagePartners extends ManageRecords
{
    use ManagesSiteSection;

    protected static string $resource = PartnerResource::class;

    protected function siteSection(): string
    {
        return 'partners';
    }

    protected function siteSubheading(): string
    {
        return 'Logos shown in the partners strip. Add the website to make a logo clickable.';
    }
}
