<?php

namespace App\Filament\Resources\PrevVetAuths\Pages;

use App\Filament\Resources\PrevVetAuths\PrevVetAuthResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePrevVetAuth extends CreateRecord
{
    protected static string $resource = PrevVetAuthResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
