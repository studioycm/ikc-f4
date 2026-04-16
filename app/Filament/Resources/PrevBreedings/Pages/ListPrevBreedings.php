<?php

namespace App\Filament\Resources\PrevBreedings\Pages;

use App\Filament\Resources\PrevBreedings\PrevBreedingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrevBreedings extends ListRecords
{
    protected static string $resource = PrevBreedingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
