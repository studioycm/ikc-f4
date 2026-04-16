<?php

namespace App\Filament\Resources\PrevHealths\Pages;

use App\Filament\Resources\PrevHealths\PrevHealthResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePrevHealth extends CreateRecord
{
    protected static string $resource = PrevHealthResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
