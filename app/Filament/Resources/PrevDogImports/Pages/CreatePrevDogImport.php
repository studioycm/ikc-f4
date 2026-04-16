<?php

namespace App\Filament\Resources\PrevDogImports\Pages;

use App\Filament\Resources\PrevDogImports\PrevDogImportResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePrevDogImport extends CreateRecord
{
    protected static string $resource = PrevDogImportResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
