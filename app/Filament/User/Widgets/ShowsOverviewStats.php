<?php

namespace App\Filament\User\Widgets;

use App\Filament\User\Pages\ShowsDashboard;
use App\Filament\User\Widgets\Concerns\InteractsWithCurrentPrevUser;
use App\Models\PrevShowDog;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Icons\Heroicon;

class ShowsOverviewStats extends BaseWidget
{
    use InteractsWithCurrentPrevUser;

//    protected int|string|array $columnSpan = 2;

    public function getColumnSpan(): int|array
    {
        return [
            'xs' => 1,
            'sm' => 1,
            'md' => 1,
            'lg' => 1,
            'xl' => 2,
        ];
    }

    protected function getColumns(): int|array
    {
        return [
            'xs' => 1,
            'sm' => 1,
            'md' => 1,
            'lg' => 1,
            'xl' => 2,
        ];
    }

    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $dogSagirIds = $this->getCurrentPrevUserDogSagirIds();

        $showEntriesQuery = PrevShowDog::query()
            ->when(
                $dogSagirIds === [],
                fn(Builder $query): Builder => $query->whereRaw('1 = 0'),
                fn(Builder $query): Builder => $query->whereIn('SagirID', $dogSagirIds),
            );

        return [
            Stat::make(__('Shows'), (clone $showEntriesQuery)->distinct('ShowID')->count('ShowID'))
                ->color('info')
                ->icon(Heroicon::Trophy)
                ->url(ShowsDashboard::getUrl(panel: 'user')),
            Stat::make(__('Show Entries'), (clone $showEntriesQuery)->count())
                ->icon(Heroicon::OutlinedTicket)
                ->url(ShowsDashboard::getUrl(panel: 'user')),
            Stat::make(__('Upcoming Shows'), (clone $showEntriesQuery)
                ->whereHas('show', fn(Builder $query): Builder => $query->whereDate('StartDate', '>=', now()->toDateString()))
                ->count())
                ->color('warning')
                ->icon(Heroicon::Trophy)
                ->url(ShowsDashboard::getUrl(panel: 'user')),
            Stat::make(__('Results'), (clone $showEntriesQuery)->has('prevShowResult')->count())
                ->color('success')
                ->icon(Heroicon::Trophy)
                ->url(ShowsDashboard::getUrl(panel: 'user')),
        ];
    }
}
