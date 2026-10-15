<?php

namespace App\Filament\Resources\MilestoneResource\Pages;

use App\Filament\Concerns\ManagesSiteSection;
use App\Filament\Resources\MilestoneResource;
use Filament\Resources\Pages\ManageRecords;

class ManageMilestones extends ManageRecords
{
    use ManagesSiteSection;

    protected static string $resource = MilestoneResource::class;

    protected function siteSection(): string
    {
        return 'journey';
    }

    protected function siteSubheading(): string
    {
        return 'Key moments shown on the Journey timeline, in the order below.';
    }
}
