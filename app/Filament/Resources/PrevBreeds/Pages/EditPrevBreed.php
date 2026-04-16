<?php

namespace App\Filament\Resources\PrevBreeds\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\PrevBreeds\PrevBreedResource;
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
