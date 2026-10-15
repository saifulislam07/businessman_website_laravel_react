<?php

namespace App\Filament\Resources\ExpertiseResource\Pages;

use App\Filament\Concerns\ManagesSiteSection;
use App\Filament\Resources\ExpertiseResource;
use Filament\Resources\Pages\ManageRecords;

class ManageExpertises extends ManageRecords
{
    use ManagesSiteSection;

    protected static string $resource = ExpertiseResource::class;

    protected function siteSection(): string
    {
        return 'expertise';
    }

    protected function siteSubheading(): string
    {
        return 'Industries and skills shown as icon cards. Drag the handle to reorder.';
    }
}
