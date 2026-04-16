<?php

namespace App\Filament\Resources\PrevHealths\Pages;

use App\Filament\Resources\PrevHealths\PrevHealthResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrevHealths extends ListRecords
{
    protected static string $resource = PrevHealthResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
