<?php

namespace App\Filament\User\Pages;

use App\Filament\User\Widgets\Sections\ShowParticipationTable;
use App\Filament\User\Widgets\ShowsOverviewStats;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;

class ShowsDashboard extends BaseDashboard
{
    protected static ?int $navigationSort = 30;

    protected static string | \BackedEnum | null $navigationIcon = Heroicon::OutlinedTrophy;

    protected static string $routePath = 'shows';

    public static function getNavigationLabel(): string
    {
        return __('Shows');
    }

    public function getTitle(): string
    {
        return __('Shows');
    }

    public function getColumns(): int|array
    {
        return 1;
    }

    public function getWidgets(): array
    {
        return [
            ShowsOverviewStats::class,
            ShowParticipationTable::class,
        ];
    }
}
