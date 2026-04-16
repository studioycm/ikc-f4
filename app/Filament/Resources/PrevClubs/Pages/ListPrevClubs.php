<?php

namespace App\Filament\Resources\PrevClubs\Pages;

use App\Filament\Resources\PrevClubs\PrevClubResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrevClubs extends ListRecords
{
    protected static string $resource = PrevClubResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
