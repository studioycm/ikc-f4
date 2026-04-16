<?php

namespace App\Filament\Resources\PrevBreedResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\PrevBreedResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPrevBreed extends EditRecord
{
    protected static string $resource = PrevBreedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
