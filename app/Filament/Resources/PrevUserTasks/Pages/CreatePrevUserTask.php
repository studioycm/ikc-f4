<?php

namespace App\Filament\Resources\PrevUserTasks\Pages;

use App\Filament\Resources\PrevUserTasks\PrevUserTaskResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePrevUserTask extends CreateRecord
{
    protected static string $resource = PrevUserTaskResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
