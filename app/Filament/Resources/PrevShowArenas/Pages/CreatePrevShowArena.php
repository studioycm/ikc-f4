<?php

namespace App\Filament\Resources\PrevShowArenas\Pages;

use App\Filament\Resources\PrevShowArenas\PrevShowArenaResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePrevShowArena extends CreateRecord
{
    protected static string $resource = PrevShowArenaResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
