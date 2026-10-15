<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GalleryItemResource\Pages;
use App\Filament\Support\ImageUpload;
use App\Models\GalleryItem;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class GalleryItemResource extends Resource
{
    protected static ?string $model = GalleryItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 6;

    protected static ?string $modelLabel = 'gallery image';

    protected static ?string $pluralModelLabel = 'Gallery';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('caption')->maxLength(255),
            TextInput::make('category')->maxLength(255)->datalist(
                fn () => GalleryItem::query()->whereNotNull('category')->distinct()->pluck('category')->all()
            ),
            ImageUpload::make('image', GalleryItem::IMAGE_COLLECTION)->required(),
            Toggle::make('is_active')->label('Show on the website')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('image')->collection(GalleryItem::IMAGE_COLLECTION)->conversion('thumb'),
                TextColumn::make('caption')->searchable(),
                TextColumn::make('category')->badge(),
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
        return ['index' => Pages\ManageGalleryItems::route('/')];
    }
}
