<?php

namespace App\Filament\Resources\PrevShowRegistrations\Pages;

use App\Filament\Resources\PrevShowRegistrations\PrevShowRegistrationResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditPrevShowRegistration extends EditRecord
{
    protected static string $resource = PrevShowRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
