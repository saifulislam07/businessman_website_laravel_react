<?php

namespace App\Filament\Concerns;

use App\Http\Controllers\Api\V1\HomeController;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Cache;

/**
 * Shared behaviour for the content "Manage" pages: an explanatory subheading, a "View on site"
 * shortcut to the matching section, and cache clearing after drag-and-drop reordering.
 *
 * Pages implement siteSection() (the anchor on the public site) and siteSubheading().
 */
trait ManagesSiteSection
{
    abstract protected function siteSection(): string;

    abstract protected function siteSubheading(): string;

    public function getSubheading(): string | Htmlable | null
    {
        return $this->siteSubheading() ?: null;
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('viewOnSite')
                ->label('View on site')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('gray')
                ->url(fn (): string => rtrim(config('portfolio.frontend_url'), '/').'/#'.$this->siteSection())
                ->openUrlInNewTab(),
        ];
    }

    /**
     * Filament reorders with a bulk query update, which skips model observers,
     * so the cached /home payload must be cleared by hand.
     */
    public function reorderTable(array $order): void
    {
        parent::reorderTable($order);

        Cache::forget(HomeController::CACHE_KEY);
    }
}
