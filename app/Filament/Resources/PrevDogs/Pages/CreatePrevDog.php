<?php

namespace App\Filament\Resources\PrevDogs\Pages;

use App\Filament\Resources\PrevDogs\PrevDogResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePrevDog extends CreateRecord
{
    protected static string $resource = PrevDogResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
