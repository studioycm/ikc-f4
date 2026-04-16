<?php

namespace App\Filament\Resources\PrevBreedingRelatedDogs\Pages;

use App\Filament\Resources\PrevBreedingRelatedDogs\PrevBreedingRelatedDogResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePrevBreedingRelatedDog extends CreateRecord
{
    protected static string $resource = PrevBreedingRelatedDogResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
