<?php

namespace App\Filament\Resources\PrevShowBreeds\Pages;

use App\Filament\Resources\PrevShowBreeds\PrevShowBreedResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrevShowBreeds extends ListRecords
{
    protected static string $resource = PrevShowBreedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
