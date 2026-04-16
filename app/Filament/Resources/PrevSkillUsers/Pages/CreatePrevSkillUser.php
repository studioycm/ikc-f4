<?php

namespace App\Filament\Resources\PrevSkillUsers\Pages;

use App\Filament\Resources\PrevSkillUsers\PrevSkillUserResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePrevSkillUser extends CreateRecord
{
    protected static string $resource = PrevSkillUserResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
