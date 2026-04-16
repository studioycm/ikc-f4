<?php

namespace App\Filament\Resources\PrevShowRegistrations\Pages;

use App\Filament\Resources\PrevShowRegistrations\PrevShowRegistrationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrevShowRegistrations extends ListRecords
{
    protected static string $resource = PrevShowRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
