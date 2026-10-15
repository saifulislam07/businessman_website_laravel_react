<?php

namespace App\Filament\Resources\ContactMessageResource\Pages;

use App\Filament\Resources\ContactMessageResource;
use App\Models\ContactMessage;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListContactMessages extends ListRecords
{
    protected static string $resource = ContactMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportAll')
                ->label('Export all (CSV)')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(fn () => ContactMessageResource::csv(ContactMessage::latest()->get())),
        ];
    }
}
