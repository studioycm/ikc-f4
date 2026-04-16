<?php

namespace App\Filament\Resources\PrevSkillUsers\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\PrevSkillUsers\PrevSkillUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPrevSkillUsers extends ListRecords
{
    protected static string $resource = PrevSkillUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
