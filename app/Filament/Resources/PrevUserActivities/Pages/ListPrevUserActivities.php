<?php

namespace App\Filament\Resources\PrevUserActivities\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\PrevUserActivities\PrevUserActivityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPrevUserActivities extends ListRecords
{
    protected static string $resource = PrevUserActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
