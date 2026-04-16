<?php

namespace App\Filament\Resources\PrevBreedingRelatedDogs\Pages;

use App\Filament\Resources\PrevBreedingRelatedDogs\PrevBreedingRelatedDogResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrevBreedingRelatedDogs extends ListRecords
{
    protected static string $resource = PrevBreedingRelatedDogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
