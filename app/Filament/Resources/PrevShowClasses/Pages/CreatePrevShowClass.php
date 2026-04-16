<?php

namespace App\Filament\Resources\PrevShowClasses\Pages;

use App\Filament\Resources\PrevShowClasses\PrevShowClassResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePrevShowClass extends CreateRecord
{
    protected static string $resource = PrevShowClassResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
