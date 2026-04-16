<?php

namespace App\Filament\Resources\PrevShows\Pages;

use App\Filament\Resources\PrevShows\PrevShowResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrevShows extends ListRecords
{
    protected static string $resource = PrevShowResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
