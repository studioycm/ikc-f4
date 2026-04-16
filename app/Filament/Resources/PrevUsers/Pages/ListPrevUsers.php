<?php

namespace App\Filament\Resources\PrevUsers\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\PrevUsers\Widgets\UserStats;
use App\Filament\Resources\PrevUsers\PrevUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Pages\Concerns\ExposesTableToWidgets;

class ListPrevUsers extends ListRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = PrevUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            UserStats::class,
        ];
    }

    public function setPage($page, $pageName = 'page'): void
    {
        parent::setPage($page, $pageName);

        $this->dispatch('scroll-to-top');
    }
}
