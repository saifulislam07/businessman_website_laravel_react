<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ContactMessageResource;
use App\Models\ContactMessage;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestMessages extends TableWidget
{
    protected static ?int $sort = 2;

    protected static ?string $heading = 'Latest messages';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(ContactMessage::query()->latest()->limit(5))
            ->columns([
                IconColumn::make('is_read')->label('Read')->boolean(),
                TextColumn::make('name'),
                TextColumn::make('email'),
                TextColumn::make('subject')->limit(40)->placeholder('—'),
                TextColumn::make('created_at')->label('Received')->since(),
            ])
            ->recordUrl(fn (ContactMessage $r) => ContactMessageResource::getUrl('view', ['record' => $r]))
            ->paginated(false);
    }
}
