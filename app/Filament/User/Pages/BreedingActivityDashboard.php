<?php

namespace App\Filament\User\Pages;

use App\Filament\User\Widgets\BreedingOverviewStats;
use App\Filament\User\Widgets\Sections\BreedingActivityTable;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;

class BreedingActivityDashboard extends BaseDashboard
{
    protected static ?int $navigationSort = 50;

    protected static string | \BackedEnum | null $navigationIcon = Heroicon::OutlinedHeart;

    protected static string $routePath = 'breeding-activity';

    public static function getNavigationLabel(): string
    {
        return __('Previous Litters');
    }

    public function getTitle(): string
    {
        return __('Previous Litters');
    }

    public function getColumns(): int|array
    {
        return 1;
    }

    public function getWidgets(): array
    {
        return [
            BreedingOverviewStats::class,
            BreedingActivityTable::class,
        ];
    }
}
