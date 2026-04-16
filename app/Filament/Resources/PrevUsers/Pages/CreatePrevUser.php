<?php

namespace App\Filament\Resources\PrevUsers\Pages;

use App\Filament\Resources\PrevUsers\PrevUserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePrevUser extends CreateRecord
{
    protected static string $resource = PrevUserResource::class;
}
