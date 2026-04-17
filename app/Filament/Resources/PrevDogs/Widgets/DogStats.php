<?php

namespace App\Filament\Resources\PrevDogs\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use App\Filament\Resources\PrevDogs\Pages\ListPrevDogs;

class DogStats extends BaseWidget
{
    use InteractsWithPageTable;

    protected ?string $pollingInterval = null;

    protected int | string | array $columnSpan = [
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
            Stat::make(__('Total'), $this->getPageTableQuery()->count()),
        ];
    }
}
