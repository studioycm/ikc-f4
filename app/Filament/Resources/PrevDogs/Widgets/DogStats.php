<?php

namespace App\Filament\Resources\PrevDogs\Widgets;

use App\Filament\Resources\PrevDogs\Pages\ListPrevDogs;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DogStats extends BaseWidget
{
    use InteractsWithPageTable;

    protected ?string $pollingInterval = null;

    protected int|string|array $columnSpan = [
        'md' => 1,
        'xl' => 1,
        'sm' => 3, // Takes half width on small screens
    ];

    protected function getTablePage(): string
    {
        return ListPrevDogs::class;
    }

    protected function getStats(): array
    {
        return [
            Stat::make(__('Displaying'), $this->getPageTableQuery()->count()),
        ];
    }
}
