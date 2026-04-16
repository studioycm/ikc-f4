<?php

namespace App\Filament\Resources\PrevDogDocuments\Pages;

use App\Filament\Resources\PrevDogDocuments\PrevDogDocumentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrevDogDocuments extends ListRecords
{
    protected static string $resource = PrevDogDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
