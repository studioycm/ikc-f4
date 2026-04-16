<?php

namespace App\Filament\Resources\PrevVetAuths\Pages;

use App\Filament\Resources\PrevVetAuths\PrevVetAuthResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrevVetAuths extends ListRecords
{
    protected static string $resource = PrevVetAuthResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
