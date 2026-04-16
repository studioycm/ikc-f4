<?php

namespace App\Filament\Resources\PrevClubs\Pages;

use App\Filament\Resources\PrevClubs\PrevClubResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePrevClub extends CreateRecord
{
    protected static string $resource = PrevClubResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
