<?php

namespace App\Filament\User\Widgets;

use App\Enums\Legacy\LegacyDogGender;
use App\Filament\User\Pages\DogsDashboard;
use App\Filament\User\Widgets\Concerns\InteractsWithCurrentPrevUser;
use App\Models\PrevDog;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Icons\Heroicon;

class DogsOverviewStats extends BaseWidget
{
    use InteractsWithCurrentPrevUser;

    protected int|string|array $columnSpan = 1;

    protected function getColumns(): int
    {
        return 1;
    }

    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $dogsQuery = PrevDog::query()
            ->whereHas('owners', function (Builder $query): void {
                $query->where('users.id', $this->getCurrentPrevUserId());
            });

        return [
            Stat::make(__('Total dogs'), $dogsQuery->count())
                ->icon(Heroicon::OutlinedHeart)
                ->url(DogsDashboard::getUrl(panel: 'user')),
            Stat::make(__('Female dogs'), (clone $dogsQuery)->where('GenderID', LegacyDogGender::Female->value)->count())
                ->color('pink')
                ->url(DogsDashboard::getUrl(panel: 'user')),
            Stat::make(__('Male dogs'), (clone $dogsQuery)->where('GenderID', LegacyDogGender::Male->value)->count())
                ->color('info')
                ->url(DogsDashboard::getUrl(panel: 'user')),
        ];
    }
}
