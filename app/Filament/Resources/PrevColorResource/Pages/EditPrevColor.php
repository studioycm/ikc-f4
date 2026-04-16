<?php

namespace App\Filament\Resources\PrevColorResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\PrevColorResource;
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
