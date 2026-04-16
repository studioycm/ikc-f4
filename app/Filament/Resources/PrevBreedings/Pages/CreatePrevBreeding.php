<?php

namespace App\Filament\Resources\PrevBreedings\Pages;

use App\Filament\Resources\PrevBreedings\PrevBreedingResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePrevBreeding extends CreateRecord
{
    protected static string $resource = PrevBreedingResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
