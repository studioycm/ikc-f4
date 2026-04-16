<?php

namespace App\Filament\Resources\PrevUserRequests\Pages;

use App\Filament\Resources\PrevUserRequests\PrevUserRequestResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePrevUserRequest extends CreateRecord
{
    protected static string $resource = PrevUserRequestResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
