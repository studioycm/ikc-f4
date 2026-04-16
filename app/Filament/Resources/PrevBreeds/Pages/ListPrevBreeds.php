<?php

namespace App\Filament\Resources\PrevBreeds\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\PrevBreeds\Widgets\BreedStats;
use App\Filament\Resources\PrevBreeds\PrevBreedResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Pages\Concerns\ExposesTableToWidgets;

class ListPrevBreeds extends ListRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = PrevBreedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            BreedStats::class,
        ];
    }

    public function setPage($page, $pageName = 'page'): void
    {
        parent::setPage($page, $pageName);

        $this->dispatch('scroll-to-top');
    }
}
