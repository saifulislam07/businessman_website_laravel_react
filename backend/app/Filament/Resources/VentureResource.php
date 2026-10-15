<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VentureResource\Pages;
use App\Filament\Support\ImageUpload;
use App\Models\Venture;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class VentureResource extends Resource
{
    protected static ?string $model = Venture::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 3;

    public const COUNTRIES = ['BN' => 'Brunei', 'MY' => 'Malaysia', 'OTHER' => 'Other'];

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->required()->maxLength(255)->live(onBlur: true)
                ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                    if (blank($get('slug'))) {
                        $set('slug', Str::slug($state));
                    }
                }),
            TextInput::make('slug')->required()->maxLength(255)->unique(ignoreRecord: true),
            TextInput::make('industry')->required()->maxLength(255),
            Select::make('country')->options(self::COUNTRIES)->required()->default('BN'),
            TextInput::make('website_url')->label('Website URL')->url()->maxLength(255),
            TextInput::make('founded_year')->label('Founded (year)')->numeric()->minValue(1900)->maxValue((int) date('Y')),
            RichEditor::make('description')->toolbarButtons(['bold', 'italic', 'strike', 'link', 'h3', 'bulletList', 'orderedList', 'blockquote', 'undo', 'redo'])->disableGrammarly()->columnSpanFull(),
            ImageUpload::make('logo', Venture::IMAGE_COLLECTION, 'Logo'),
            Toggle::make('is_active')->label('Show on the website')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('logo')->collection(Venture::IMAGE_COLLECTION)->conversion('thumb'),
                TextColumn::make('name')->searchable(),
                TextColumn::make('industry'),
                TextColumn::make('country')->badge()->color(fn (string $state) => match ($state) { 'BN' => 'primary', 'MY' => 'warning', default => 'gray' })->formatStateUsing(fn (string $state) => self::COUNTRIES[$state] ?? $state),
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
        return ['index' => Pages\ManageVentures::route('/')];
    }
}
