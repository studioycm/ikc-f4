<?php

namespace App\Filament\Resources\PrevUsers\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\PrevUsers\PrevUserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPrevUser extends EditRecord
{
    protected static string $resource = PrevUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
