<?php

namespace App\Filament\Resources\PrevDogImports\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\PrevDogImports\PrevDogImportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPrevDogImports extends ListRecords
{
    protected static string $resource = PrevDogImportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
