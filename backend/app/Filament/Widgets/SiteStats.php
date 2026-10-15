<?php

namespace App\Filament\Widgets;

use App\Models\ContactMessage;
use App\Models\GalleryItem;
use App\Models\Venture;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SiteStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected static ?string $pollingInterval = null;

    protected function getColumns(): int
    {
        return 4;
    }

    protected function getStats(): array
    {
        $unread = ContactMessage::where('is_read', false)->count();
        $total = ContactMessage::count();

        return [
            Stat::make('Unread messages', $unread)
                ->description($unread > 0 ? 'Waiting for your reply' : 'All caught up')
                ->descriptionIcon($unread > 0 ? 'heroicon-m-bell-alert' : 'heroicon-m-check-circle')
                ->color($unread > 0 ? 'warning' : 'success'),

            Stat::make('Messages (last 7 days)', array_sum($this->messagesPerDay()))
                ->description($total.' in total')
                ->descriptionIcon('heroicon-m-envelope')
                ->chart($this->messagesPerDay())
                ->color('primary'),

            Stat::make('Ventures on the site', Venture::active()->count())
                ->description(Venture::count().' in total')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('primary'),

            Stat::make('Gallery photos', GalleryItem::active()->count())
                ->description(GalleryItem::count().' uploaded')
                ->descriptionIcon('heroicon-m-photo')
                ->color('primary'),
        ];
    }

    /**
     * Messages received on each of the last 7 days, oldest first (zero-filled).
     *
     * @return array<int, int>
     */
    private function messagesPerDay(): array
    {
        $counts = ContactMessage::query()
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->get(['created_at'])
            ->countBy(fn (ContactMessage $m) => $m->created_at->toDateString());

        return collect(range(6, 0))
            ->map(fn (int $daysAgo) => (int) ($counts[now()->subDays($daysAgo)->toDateString()] ?? 0))
            ->all();
    }
}
