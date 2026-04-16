<?php

namespace App\Filament\Resources\PrevColors\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\PrevColors\PrevColorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPrevColor extends EditRecord
{
    protected static string $resource = PrevColorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
