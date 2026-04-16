<?php

namespace App\Filament\Resources\PrevShowClasses\Pages;

use App\Filament\Resources\PrevShowClasses\PrevShowClassResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditPrevShowClass extends EditRecord
{
    protected static string $resource = PrevShowClassResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
