<?php

namespace App\Filament\Resources\StatResource\Pages;

use App\Filament\Concerns\ManagesSiteSection;
use App\Filament\Resources\StatResource;
use Filament\Resources\Pages\ManageRecords;

class ManageStats extends ManageRecords
{
    use ManagesSiteSection;

    protected static string $resource = StatResource::class;

    protected function siteSection(): string
    {
        return 'about';
    }

    protected function siteSubheading(): string
    {
        return 'Key figures shown as cards under the About section. Drag the handle to reorder.';
    }
}
