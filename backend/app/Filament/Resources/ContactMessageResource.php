<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactMessageResource\Pages;
use App\Models\ContactMessage;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationGroup = 'Inbox';

    protected static ?string $navigationLabel = 'Messages';

    protected static ?string $modelLabel = 'message';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        $unread = ContactMessage::where('is_read', false)->count();

        return $unread > 0 ? (string) $unread : null;
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make()->columns(2)->schema([
                TextEntry::make('name'),
                TextEntry::make('email')->copyable(),
                TextEntry::make('phone')->placeholder('—'),
                TextEntry::make('subject')->placeholder('—'),
                TextEntry::make('created_at')->label('Received')->dateTime(),
                TextEntry::make('ip')->label('IP address'),
                TextEntry::make('message')->columnSpanFull()->extraAttributes(['class' => 'whitespace-pre-line']),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->weight(fn (ContactMessage $r) => $r->is_read ? null : 'bold'),
                TextColumn::make('email')->searchable(),
                TextColumn::make('subject')->limit(40)->placeholder('—'),
                TextColumn::make('created_at')->label('Received')->dateTime()->sortable(),
                ToggleColumn::make('is_read')->label('Read'),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordClasses(fn (ContactMessage $record) => $record->is_read ? null : 'fi-ta-row-unread')
            ->emptyStateHeading('No messages yet')
            ->emptyStateDescription('Messages sent through the website contact form appear here.')
            ->emptyStateIcon('heroicon-o-envelope')
            ->filters([
                TernaryFilter::make('is_read')->label('Read status')
                    ->trueLabel('Read')->falseLabel('Unread')->placeholder('All'),
            ])
            ->actions([
                ViewAction::make(),
                Action::make('reply')
                    ->label('Reply by email')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->url(fn (ContactMessage $r) => 'mailto:'.$r->email.'?subject='.rawurlencode('Re: '.($r->subject ?: 'Your message')))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                BulkAction::make('export')
                    ->label('Export CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn (Collection $records) => self::csv($records)),
                DeleteBulkAction::make(),
            ]);
    }

    public static function csv(Collection $records): StreamedResponse
    {
        return response()->streamDownload(function () use ($records) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Received', 'Name', 'Email', 'Phone', 'Subject', 'Message', 'IP', 'Read']);

            foreach ($records as $m) {
                fputcsv($out, [$m->created_at, $m->name, $m->email, $m->phone, $m->subject, $m->message, $m->ip, $m->is_read ? 'yes' : 'no']);
            }

            fclose($out);
        }, 'contact-messages-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactMessages::route('/'),
            'view' => Pages\ViewContactMessage::route('/{record}'),
        ];
    }
}
