<?php

namespace App\Filament\Resources\TestimonialResource\Pages;

use App\Filament\Concerns\ManagesSiteSection;
use App\Filament\Resources\TestimonialResource;
use Filament\Resources\Pages\ManageRecords;

class ManageTestimonials extends ManageRecords
{
    use ManagesSiteSection;

    protected static string $resource = TestimonialResource::class;

    protected function siteSection(): string
    {
        return 'partners';
    }

    protected function siteSubheading(): string
    {
        return 'Quotes shown in the rotating testimonial slider.';
    }
}
