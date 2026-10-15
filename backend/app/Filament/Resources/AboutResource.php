<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AboutResource\Pages;
use App\Filament\Support\ImageUpload;
use App\Models\About;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
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
use Illuminate\Support\Str;
use App\Support\RichText;

class AboutResource extends Resource
{
    protected static ?string $model = About::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'About entry';

    protected static ?string $pluralModelLabel = 'About';

    public static function form(Form $form): Form
    {
        return $form->schema([
            RichEditor::make('bio')->label('Biography')->required()->toolbarButtons(['bold', 'italic', 'strike', 'link', 'h3', 'bulletList', 'orderedList', 'blockquote', 'undo', 'redo'])->disableGrammarly()->columnSpanFull()
                ->helperText('Your story, shown in the About section. Press Enter for a new paragraph.'),
            Textarea::make('vision')->label('Vision statement')->rows(3)->columnSpanFull()
                ->helperText('One or two sentences, shown as a highlighted quote.'),
            ImageUpload::make('photo', About::IMAGE_COLLECTION, 'Photo'),
            Toggle::make('is_active')->label('Show on the website')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('photo')->collection(About::IMAGE_COLLECTION)->conversion('thumb')->circular(),
                TextColumn::make('bio')->label('Biography')->formatStateUsing(fn (?string $state) => Str::limit(RichText::text($state), 90)),
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
        return ['index' => Pages\ManageAbouts::route('/')];
    }
}
