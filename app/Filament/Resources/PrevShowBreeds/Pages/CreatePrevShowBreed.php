<?php

namespace App\Filament\Resources\PrevShowBreeds\Pages;

use App\Filament\Resources\PrevShowBreeds\PrevShowBreedResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePrevShowBreed extends CreateRecord
{
    protected static string $resource = PrevShowBreedResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
