<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VisitedCountryResource\Pages;
use App\Models\VisitedCountry;
use App\Support\Countries;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class VisitedCountryResource extends Resource
{
    protected static ?string $model = VisitedCountry::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Countries visited';

    protected static ?string $modelLabel = 'country';

    protected static ?string $pluralModelLabel = 'Countries visited';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('code')
                ->label('Country')
                ->options(Countries::ALL)
                ->searchable()
                ->required()
                ->unique(ignoreRecord: true)
                ->helperText('The flag is added automatically.'),
            Toggle::make('is_active')->label('Show on the website')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('code')
                    ->label('Flag')
                    ->getStateUsing(fn (VisitedCountry $record): string => rtrim(config('portfolio.frontend_url'), '/').'/flags/'.$record->code.'.png')
                    ->height(24),
                TextColumn::make('name')->label('Country'),
                ToggleColumn::make('is_active')->label('Visible'),
            ])
            ->reorderable('sort_order')
            ->emptyStateHeading('Nothing here yet')
            ->emptyStateDescription('Use the button at the top right to add the first one.')
            ->defaultSort('sort_order')
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageVisitedCountries::route('/')];
    }
}
