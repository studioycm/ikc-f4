<?php

namespace App\Filament\Resources\PrevShowResults\Pages;

use App\Filament\Resources\PrevShowResults\PrevShowResultResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePrevShowResult extends CreateRecord
{
    protected static string $resource = PrevShowResultResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
