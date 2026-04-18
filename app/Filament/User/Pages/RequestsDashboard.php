<?php

namespace App\Filament\User\Pages;

use App\Filament\User\Widgets\Sections\UserRequestsTable;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;

class RequestsDashboard extends BaseDashboard
{
    protected static ?int $navigationSort = 70;

    protected static string | \BackedEnum | null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string $routePath = 'requests';

    public static function getNavigationLabel(): string
    {
        return __('Requests');
    }

    public function getTitle(): string
    {
        return __('Requests');
    }

    public function getColumns(): int|array
    {
        return 1;
    }

    public function getWidgets(): array
    {
        return [
            UserRequestsTable::class,
        ];
    }
}
