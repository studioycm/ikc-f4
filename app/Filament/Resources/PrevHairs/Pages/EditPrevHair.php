<?php

namespace App\Filament\Resources\PrevHairs\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\PrevHairs\PrevHairResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPrevHair extends EditRecord
{
    protected static string $resource = PrevHairResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
