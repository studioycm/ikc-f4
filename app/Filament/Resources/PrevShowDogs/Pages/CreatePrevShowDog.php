<?php

namespace App\Filament\Resources\PrevShowDogs\Pages;

use App\Filament\Resources\PrevShowDogs\PrevShowDogResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePrevShowDog extends CreateRecord
{
    protected static string $resource = PrevShowDogResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
