<?php

namespace App\Filament\User\Pages;

use App\Filament\User\Widgets\ClubManagersWidget;
use App\Filament\User\Widgets\Sections\UserClubMembershipsTable;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;

class MembershipsDashboard extends BaseDashboard
{
    protected static ?int $navigationSort = 60;

    protected static string | \BackedEnum | null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string $routePath = 'memberships';

    public static function getNavigationLabel(): string
    {
        return __('Membership');
    }

    public function getTitle(): string
    {
        return __('Membership');
    }

    public function getColumns(): int|array
    {
        return 1;
    }

    public function getWidgets(): array
    {
        return [
            UserClubMembershipsTable::class,
            ClubManagersWidget::class,
        ];
    }
}
