<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpertiseResource\Pages;
use App\Filament\Support\ImageUpload;
use App\Models\Expertise;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class ExpertiseResource extends Resource
{
    protected static ?string $model = Expertise::class;

    protected static ?string $navigationIcon = 'heroicon-o-light-bulb';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Expertise';

    protected static ?string $modelLabel = 'expertise item';

    protected static ?string $pluralModelLabel = 'Expertise';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('title')->required()->maxLength(255),
            TextInput::make('icon')->maxLength(255)->helperText('Lucide icon name, e.g. "Briefcase".'),
            RichEditor::make('description')->toolbarButtons(['bold', 'italic', 'strike', 'link', 'h3', 'bulletList', 'orderedList', 'blockquote', 'undo', 'redo'])->disableGrammarly()->columnSpanFull(),
            ImageUpload::make('image', Expertise::IMAGE_COLLECTION, 'Photo (optional)'),
            Toggle::make('is_active')->label('Show on the website')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable(),
                TextColumn::make('icon'),
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
        return ['index' => Pages\ManageExpertises::route('/')];
    }
}
