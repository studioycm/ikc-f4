<?php

namespace App\Filament\Resources\PrevUserTasks\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\PrevUserTasks\PrevUserTaskResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPrevUserTasks extends ListRecords
{
    protected static string $resource = PrevUserTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
