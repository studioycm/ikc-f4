<?php

namespace App\Filament\Resources\PrevShowRegistrations\Pages;

use App\Filament\Resources\PrevShowRegistrations\PrevShowRegistrationResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePrevShowRegistration extends CreateRecord
{
    protected static string $resource = PrevShowRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
