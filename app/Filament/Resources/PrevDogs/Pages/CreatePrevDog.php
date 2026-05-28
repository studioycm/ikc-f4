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

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }

    public function getContentTabLabel(): ?string
    {
        return __('dog/model/general.labels.singular');
    }
}
