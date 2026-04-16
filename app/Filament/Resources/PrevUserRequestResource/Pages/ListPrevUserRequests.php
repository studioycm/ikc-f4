<?php

namespace App\Filament\Resources\PrevUserRequestResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\PrevUserRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPrevUserRequests extends ListRecords
{
    protected static string $resource = PrevUserRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
